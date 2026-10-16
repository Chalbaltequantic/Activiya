<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

use App\Models\Grn;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SmartGrnController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    private function authorizeGrn(Grn $grn): void
    {
        abort_unless(
            (int) $grn->uploaded_by === (int) Auth::guard('admin')->id(),
            404
        );
    }

    public function index(Request $request)
    {
        $query = Grn::query()->where('uploaded_by', Auth::guard('admin')->id())->withCount('items')->latest();

        if ($request->filled('search')) {
            $search = $request->input('search');

            $query->where(function ($builder) use ($search) {
                $builder->where('grn_number', 'like', "%{$search}%")
                    ->orWhere('po_number', 'like', "%{$search}%");
            });
        }

        if ($request->filled('client')) {
            $query->where('client_code', $request->input('client'));
        }

        return view('admin.smart-grn.index', [
            'grns' => $query->paginate(25)->withQueryString(),
        ]);
    }

    public function create()
    {
        return view('admin.smart-grn.upload');
    }

    public function store(Request $request)
    {
        $request->validate([
            'grn_files' => 'required|array|min:1|max:20',
            'grn_files.*' => 'required|file|mimes:pdf,jpg,jpeg,png,bmp,tif,tiff|max:20480',
        ]);

        $results = [];
        foreach ($request->file('grn_files') as $file) {
            $results[] = $this->processOneFile($file);
        }

        if (count($results) === 1) {
            return redirect()->route('admin.smart-grn.show', $results[0])
                ->with('status', 'GRN upload completed. Please check the status.');
        }

        return redirect()->route('admin.smart-grn.index')
            ->with('status', count($results) . ' GRN file(s) uploaded. Review each processing status below.');
    }

    private function processOneFile(\Illuminate\Http\UploadedFile $file): Grn
    {
        $path = $file->store('grns', 'local');

        // Save the original document even if OCR later fails.
        $grn = Grn::create([
            'client_code' => 'unknown',
            'original_name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'file_sha256' => hash_file('sha256', $file->getRealPath()),
            'status' => 'processing',
            'uploaded_by' => Auth::guard('admin')->id(),
        ]);

        try {
            $url = rtrim(config('grn.api_url'), '/') . '/read-grn';
            $apiKey = config('grn.api_key');

            $response = Http::timeout(120)
                ->withHeaders(['x-api-key' => $apiKey])
                ->attach(
                    'file',
                    Storage::disk('local')->get($path),
                    $file->getClientOriginalName()
                )
                ->post($url);

            if (! $response->successful()) {
                throw new \RuntimeException(
                    'Python API HTTP ' . $response->status() . ': ' .
                    Str::limit($response->body(), 500)
                );
            }

            $data = $response->json();
            $parsed = $data['goods_receipt_note'] ?? null;

            if (empty($data['success']) || ! is_array($parsed)) {
                $grn->update([
                    'status' => 'review_required',
                    'error_message' => Str::limit(
                        $data['message'] ?? 'GRN requires manual review.',
                        1000
                    ),
                ]);

                return $grn;
            }

            DB::transaction(function () use ($grn, $parsed, $data) {
                $items = $parsed['items'] ?? [];

                $grn->update([
                    'client_code' => $parsed['client_code'] ?? 'unknown',
                    'grn_number' => $parsed['grn_number'] ?? null,
                    'grn_date' => $parsed['grn_date'] ?? null,
                    'po_number' => $parsed['po_number'] ?? null,
                    'supplier_name' => $parsed['supplier_name'] ?? null,
                    'supplier_code' => $parsed['supplier_code'] ?? null,
                    'supplier_gstin' => $parsed['supplier_gstin'] ?? null,
                    'buyer_name' => $parsed['buyer_name'] ?? null,
                    'buyer_gstin' => $parsed['buyer_gstin'] ?? null,
                    'delivery_address' => $parsed['delivery_address'] ?? null,
                    'invoice_number' => $parsed['invoice_number'] ?? null,
                    'delivery_number' => $parsed['delivery_number'] ?? null,
                    'challan_qty' => collect($items)->sum('challan_qty'),
                    'received_qty' => collect($items)->sum('received_qty'),
                    'accepted_qty' => collect($items)->sum('accepted_qty'),
                    'status' => ! empty($parsed['review_required'])
                        ? 'review_required' : 'parsed',
                    'raw_result' => $data,
                    'error_message' => null,
                ]);

                foreach ($items as $index => $item) {
                    $grn->items()->create([
                        'line_no' => $item['line_no'] ?? ($index + 1),
                        'item_code' => $item['item_code'] ?? null,
                        'ean' => $item['ean'] ?? null,
                        'description' => $item['description'] ?? null,
                        'uom' => $item['uom'] ?? null,
                        'challan_qty' => $item['challan_qty'] ?? null,
                        'received_qty' => $item['received_qty'] ?? null,
                        'accepted_qty' => $item['accepted_qty'] ?? null,
                        'mrp' => $item['mrp'] ?? null,
                        'extra' => $item,
                    ]);
                }
            });
        } catch (\Throwable $exception) {
            report($exception);

            $grn->update([
                'status' => 'failed',
                'error_message' => Str::limit($exception->getMessage(), 1000),
            ]);
        }

        return $grn;
    }

    public function show(Grn $grn)
    {
        $this->authorizeGrn($grn);
        return view('admin.smart-grn.show', [
            'grn' => $grn->load('items'),
        ]);
    }

    public function download(Grn $grn)
    {
        $this->authorizeGrn($grn);
        $disk = Storage::disk('local');
        abort_unless($disk->exists($grn->file_path), 404);

        return $disk->download($grn->file_path, $grn->original_name);
    }

    public function export(Grn $grn)
    {
        $this->authorizeGrn($grn);
        $grn->load('items');

        return response()->streamDownload(function () use ($grn) {
            $output = fopen('php://output', 'w');
            fputcsv($output, [
                'Line', 'Item Code', 'EAN', 'Description',
                'UOM', 'Challan', 'Received', 'Accepted', 'MRP',
            ]);

            foreach ($grn->items as $item) {
                fputcsv($output, [
                    $item->line_no,
                    $item->item_code,
                    $item->ean,
                    $item->description,
                    $item->uom,
                    $item->challan_qty,
                    $item->received_qty,
                    $item->accepted_qty,
                    $item->mrp,
                ]);
            }

            fclose($output);
        }, 'GRN-' . $grn->id . '.csv', [
            'Content-Type' => 'text/csv',
        ]);
    }
}
