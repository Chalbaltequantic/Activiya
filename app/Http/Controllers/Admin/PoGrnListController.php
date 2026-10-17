<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Grn;
use App\Models\PurchaseOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PoGrnListController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index(Request $request)
    {
        $adminId = Auth::guard('admin')->id();
        $search = trim((string) $request->query('search', ''));
        $status = (string) $request->query('status', '');
        $perPage = in_array((int) $request->query('per_page', 20), [10, 20, 50, 100], true)
            ? (int) $request->query('per_page', 20)
            : 20;

        // One row per PO. The latest successfully parsed GRN is displayed;
        // total matching GRN count is also shown, to avoid hiding split deliveries.
        // Comparison remains per-GRN (existing GrnCompareController).
        $grnSummary = DB::table('grns')
            ->selectRaw('TRIM(po_number) as normalized_po_number')
            ->selectRaw('COUNT(*) as grn_count')
            ->selectRaw('MAX(id) as latest_grn_id')
            ->where('uploaded_by', $adminId)
            ->where('status', 'parsed')
            ->whereNotNull('po_number')
            ->whereRaw("TRIM(po_number) <> ''")
            ->groupByRaw('TRIM(po_number)');

        $query = PurchaseOrder::query()
            ->where('purchase_orders.created_by', $adminId)
            ->leftJoinSub($grnSummary, 'gs', function ($join) {
                $join->on('gs.normalized_po_number', '=', DB::raw('TRIM(purchase_orders.po_no)'));
            })
            ->leftJoin('grns as g', 'g.id', '=', 'gs.latest_grn_id')
            ->select([
                'purchase_orders.id', 'purchase_orders.po_no',
                'purchase_orders.po_date', 'purchase_orders.vendor_name',
                'purchase_orders.processing_status',
                'g.id as grn_id', 'g.grn_number', 'g.grn_date',
                'g.supplier_name as grn_supplier', 'g.status as grn_status',
                'gs.grn_count',
            ])
            ->withCount('items');

        if ($search !== '') {
            $escaped = addcslashes($search, '%_\\');
            $like = '%' . $escaped . '%';
            $query->where(function ($q) use ($like) {
                $q->where('purchase_orders.po_no', 'like', $like)
                    ->orWhere('purchase_orders.vendor_name', 'like', $like)
                    ->orWhere('g.grn_number', 'like', $like)
                    ->orWhere('g.supplier_name', 'like', $like);
            });
        }

        if ($status === 'with_grn') {
            $query->whereNotNull('g.id');
        } elseif ($status === 'without_grn') {
            $query->whereNull('g.id');
        }

        $rows = $query->orderByDesc('purchase_orders.id')
            ->paginate($perPage)->withQueryString();

        // Load item rows only for the currently displayed PO page.
        $poIds = $rows->getCollection()->pluck('id')->all();
        $grnIds = $rows->getCollection()->pluck('grn_id')->filter()->unique()->values()->all();
        $poItems = empty($poIds) ? collect() : DB::table('purchase_order_items')
            ->whereIn('purchase_order_id', $poIds)->orderBy('id')->get()->groupBy('purchase_order_id');
        $grnItems = empty($grnIds) ? collect() : DB::table('grn_items')
            ->whereIn('grn_id', $grnIds)->orderBy('id')->get()->groupBy('grn_id');

        foreach ($rows->getCollection() as $row) {
            $poLines = $poItems->get($row->id, collect());
            $grnLines = $row->grn_id ? $grnItems->get($row->grn_id, collect()) : collect();
            $row->grn_items_count = $row->grn_id ? $grnLines->count() : null;
            $row->comparison = $this->compareLines($poLines, $grnLines, (bool) $row->grn_id);
            $row->matched_count = $row->comparison->where('status', 'Matched')->count();
            $row->variance_count = $row->grn_id
                ? $row->comparison->filter(fn ($line) => $line['status'] !== 'Matched')->count()
                : null;
            $row->reconciliation_status = ! $row->grn_id ? 'GRN Pending'
                : ($row->variance_count === 0 ? 'Matched' : 'Variance');
        }

        // GRNs with no PO owned by this admin. Display separately so they are not lost.
        $unmatchedGrns = Grn::query()
            ->where('uploaded_by', $adminId)
            ->where('status', 'parsed')
            ->where(function ($q) use ($adminId) {
                $q->whereNull('po_number')
                    ->orWhereRaw("TRIM(po_number) = ''")
                    ->orWhereNotExists(function ($sub) use ($adminId) {
                        $sub->selectRaw('1')
                            ->from('purchase_orders')
                            ->where('purchase_orders.created_by', $adminId)
                            ->whereRaw('TRIM(purchase_orders.po_no) = TRIM(grns.po_number)');
                    });
            })
            ->when($search !== '', function ($q) use ($search) {
                $like = '%' . addcslashes($search, '%_\\') . '%';
                $q->where(function ($sub) use ($like) {
                    $sub->where('po_number', 'like', $like)
                        ->orWhere('grn_number', 'like', $like)
                        ->orWhere('supplier_name', 'like', $like);
                });
            })
            ->orderByDesc('id')
            ->paginate($perPage, ['*'], 'unmatched_page')->withQueryString();

        $unmatchedIds = $unmatchedGrns->getCollection()->pluck('id')->all();
        $unmatchedCounts = empty($unmatchedIds) ? collect() : DB::table('grn_items')
            ->whereIn('grn_id', $unmatchedIds)->select('grn_id')
            ->selectRaw('COUNT(*) as total')->groupBy('grn_id')->pluck('total', 'grn_id');
        foreach ($unmatchedGrns->getCollection() as $grn) {
            $grn->line_count = (int) ($unmatchedCounts->get($grn->id, 0));
        }

        return view('admin.smart-grn.po-grn-list', compact('rows', 'unmatchedGrns', 'search', 'status', 'perPage'));
    }
    /** Compare the latest parsed GRN with PO lines, preserving unmatched lines. */
    private function compareLines($poLines, $grnLines, bool $hasGrn)
    {
        $normalize = fn ($value) => strtoupper(trim((string) ($value ?? '')));
        $used = [];
        $result = collect();

        foreach ($poLines as $po) {
            $poCode = $normalize($po->item_code ?? null);
            $poEan = $normalize($po->ean ?? null);
            $candidates = $grnLines->filter(function ($grn) use ($poCode, $poEan, $normalize, $used) {
                if (isset($used[$grn->id])) return false;
                $code = $normalize($grn->item_code ?? null);
                $ean = $normalize($grn->ean ?? null);
                return ($poEan !== '' && $poEan === $ean)
                    || ($poCode !== '' && $poCode === $code);
            });
            $match = $candidates->count() === 1 ? $candidates->first() : null;
            if ($match) $used[$match->id] = true;
            $poQty = (float) ($po->quantity ?? 0);
            $accepted = $match && $match->accepted_qty !== null ? (float) $match->accepted_qty : null;
            $difference = $accepted === null ? null : $accepted - $poQty;
            $status = ! $hasGrn ? 'GRN Pending'
                : ($candidates->count() > 1 ? 'Ambiguous'
                : (! $match ? 'Missing in GRN'
                : ($accepted === null ? 'Accepted Qty Missing'
                : (abs($difference) < 0.00001 ? 'Matched' : ($difference < 0 ? 'Short' : 'Excess')))));
            $result->push([
                'po_line' => $po->line_no ?? null, 'grn_line' => $match->line_no ?? null,
                'item_code' => $po->item_code ?: ($match->item_code ?? null),
                'ean' => $po->ean ?: ($match->ean ?? null),
                'description' => $po->description ?: ($match->description ?? null),
                'po_qty' => $poQty,
                'received' => $match->received_qty ?? null,
                'accepted' => $accepted, 'difference' => $difference, 'status' => $status,
            ]);
        }

        if ($hasGrn) {
            foreach ($grnLines as $grn) {
                if (isset($used[$grn->id])) continue;
                $result->push([
                    'po_line' => null, 'grn_line' => $grn->line_no ?? null,
                    'item_code' => $grn->item_code ?? null, 'ean' => $grn->ean ?? null,
                    'description' => $grn->description ?? null, 'po_qty' => null,
                    'received' => $grn->received_qty ?? null,
                    'accepted' => $grn->accepted_qty ?? null,
                    'difference' => null, 'status' => 'Unmatched GRN Item',
                ]);
            }
        }
        return $result;
    }

}
