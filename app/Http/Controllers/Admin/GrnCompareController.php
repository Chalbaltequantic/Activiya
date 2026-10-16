<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Grn;
use App\Models\PurchaseOrder;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class GrnCompareController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    private function comparison(Grn $grn): array
    {
        abort_unless((int) $grn->uploaded_by === (int) Auth::guard('admin')->id(), 404);

        $grn->load('items');

        if (! $grn->po_number) {
            return ['grn' => $grn, 'po' => null, 'rows' => [], 'message' => 'No PO number was extracted from this GRN.'];
        }

        $query = PurchaseOrder::with(['items', 'template'])
            ->where('created_by', Auth::guard('admin')->id())
            ->where('po_no', trim($grn->po_number));

        // A PO number may be reused across clients. Restrict by detected client
        // when a corresponding PO template exists.
        if ($grn->client_code && $grn->client_code !== 'unknown') {
            $query->whereHas('template', function ($builder) use ($grn) {
                $builder->where('code', $grn->client_code);
            });
        }

        $matches = $query->limit(2)->get();

        if ($matches->count() !== 1) {
            $message = $matches->isEmpty()
                ? 'No matching PO found for this admin, PO number and client.'
                : 'Multiple POs match. Manual selection is required.';

            return ['grn' => $grn, 'po' => null, 'rows' => [], 'message' => $message];
        }

        $po = $matches->first();
        $rows = [];
        $usedPoIds = [];

        foreach ($grn->items as $grnItem) {
            $candidates = $po->items->filter(function ($poItem) use ($grnItem, $usedPoIds) {
                if (in_array($poItem->id, $usedPoIds, true)) {
                    return false;
                }

                $eanMatches = $grnItem->ean && $poItem->ean
                    && $this->normalize($grnItem->ean) === $this->normalize($poItem->ean);

                $codeMatches = $grnItem->item_code && $poItem->item_code
                    && $this->normalize($grnItem->item_code) === $this->normalize($poItem->item_code);

                return $eanMatches || $codeMatches;
            });

            // Ambiguous or duplicate item identifiers must not be auto-matched.
            $poItem = $candidates->count() === 1 ? $candidates->first() : null;
            $ordered = $poItem ? (float) $poItem->quantity : null;
            $accepted = $grnItem->accepted_qty === null ? null : (float) $grnItem->accepted_qty;
            $received = $grnItem->received_qty === null ? null : (float) $grnItem->received_qty;

            if ($poItem) {
                $usedPoIds[] = $poItem->id;
            }

            $status = 'Unmatched GRN item';
            if ($candidates->count() > 1) {
                $status = 'Ambiguous match';
            } elseif ($poItem && $accepted === null) {
                $status = 'Missing accepted quantity';
            } elseif ($poItem && $accepted > $ordered) {
                $status = 'Excess';
            } elseif ($poItem && $accepted < $ordered) {
                $status = 'Partial / short';
            } elseif ($poItem) {
                $status = 'Matched';
            }

            $rows[] = [
                'item_code' => $grnItem->item_code,
                'ean' => $grnItem->ean,
                'description' => $grnItem->description,
                'po_qty' => $ordered,
                'received_qty' => $received,
                'accepted_qty' => $accepted,
                'difference' => $ordered !== null && $accepted !== null ? $accepted - $ordered : null,
                'status' => $status,
            ];
        }

        foreach ($po->items as $poItem) {
            if (in_array($poItem->id, $usedPoIds, true)) {
                continue;
            }

            $rows[] = [
                'item_code' => $poItem->item_code,
                'ean' => $poItem->ean,
                'description' => $poItem->description,
                'po_qty' => (float) $poItem->quantity,
                'received_qty' => null,
                'accepted_qty' => null,
                'difference' => null,
                'status' => 'No matching GRN item',
            ];
        }

        return [
            'grn' => $grn,
            'po' => $po,
            'rows' => $rows,
            'message' => 'Comparison is for this GRN only, not cumulative across multiple receipts.',
        ];
    }

    private function normalize(string $value): string
    {
        return strtoupper(trim($value));
    }

    public function show(Grn $grn)
    {
        return view('admin.smart-grn.compare', ['data' => $this->comparison($grn)]);
    }

    public function excel(Grn $grn)
    {
        $data = $this->comparison($grn);

        // CSV opens in Excel. No spreadsheet library required.
        return response()->streamDownload(function () use ($data) {
            $stream = fopen('php://output', 'w');
            fputcsv($stream, ['Item Code', 'EAN', 'Description', 'PO Qty', 'GRN Received', 'GRN Accepted', 'Difference', 'Status']);

            foreach ($data['rows'] as $row) {
                fputcsv($stream, array_values($row));
            }

            fclose($stream);
        }, 'PO-GRN-' . $grn->id . '.csv', ['Content-Type' => 'text/csv']);
    }

    public function pdf(Grn $grn)
    {
        $data = $this->comparison($grn);

        return Pdf::loadView('admin.smart-grn.compare-pdf', ['data' => $data])
            ->setPaper('a4', 'landscape')
            ->download('PO-GRN-' . $grn->id . '.pdf');
    }
}
