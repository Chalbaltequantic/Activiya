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

        return view('admin.smart-grn.po-grn-list', compact('rows', 'unmatchedGrns', 'search', 'status', 'perPage'));
    }
}
