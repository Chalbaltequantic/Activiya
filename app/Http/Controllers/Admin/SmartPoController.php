<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PoImport;
use App\Models\PoTemplate;
use App\Models\PurchaseOrder;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class SmartPoController extends Controller
{
   
	public function __construct()
    {
        $this->middleware('auth:admin');
    }
   private function userId(): int
    {
        $id = Auth::guard('admin')->id();

        abort_unless($id, 403);

        return (int) $id;
    }

    private function ownedPo(int $id): PurchaseOrder
    {
        return PurchaseOrder::with([
            'template',
            'items',
        ])
            ->where('id', $id)
            ->where('created_by', $this->userId())
            ->firstOrFail();
    }

    public function index(Request $request)
    {
        $query = PurchaseOrder::with([
            'template',
            'items',
        ])
            ->where(
                'created_by',
                $this->userId()
            );

        if ($request->filled('po_no')) {
            $query->where(
                'po_no',
                'like',
                '%' . trim($request->po_no) . '%'
            );
        }

        if ($request->filled('template')) {
            $query->whereHas(
                'template',
                function ($q) use ($request) {
                    $q->where(
                        'code',
                        $request->template
                    );
                }
            );
        }

        if ($request->filled('vendor')) {
            $query->where(
                'vendor_name',
                'like',
                '%' . trim($request->vendor) . '%'
            );
        }

        if ($request->filled('status')) {
            $query->where(
                'processing_status',
                $request->status
            );
        }

        if ($request->filled('date_from')) {
            $query->whereDate(
                'po_date',
                '>=',
                $request->date_from
            );
        }

        if ($request->filled('date_to')) {
            $query->whereDate(
                'po_date',
                '<=',
                $request->date_to
            );
        }

        $purchaseOrders = $query
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        $templates = PoTemplate::where(
            'status',
            true
        )
            ->orderBy('name')
            ->get();

        return view(
            'admin.smart-po.index',
            compact(
                'purchaseOrders',
                'templates'
            )
        );
    }

    public function upload()
    {
        return view(
            'admin.smart-po.upload'
        );
    }

    public function processUpload(Request $request)
    {
        $request->validate([
            'po_files' => [
                'required',
                'array',
                'min:1',
                'max:20',
            ],

            'po_files.*' => [
                'required',
                'file',
                'mimes:pdf,jpg,jpeg,png,bmp,tif,tiff',
                'max:10240',
            ],
        ]);

        $files = $request->file(
            'po_files'
        );

        $import = PoImport::create([
            'created_by' => $this->userId(),
            'total_files' => count($files),
            'processed_files' => 0,
            'failed_files' => 0,
            'status' => 'processing',
        ]);

        $processed = 0;
        $failed = 0;

     /*   foreach ($files as $file) {
            try {
                $this->processSingleFile(
                    $file,
                    $import
                );

                $processed++;
            } catch (\Throwable $e) {
                report($e);
                $failed++;
            }
        }*/
		
		$errors = [];

		foreach ($files as $file) {
			try {

				$this->processSingleFile(
					$file,
					$import
				);

				$processed++;

			} catch (\Throwable $e) {

				report($e);

				$failed++;

				$errors[] =
					$file->getClientOriginalName()
					. ': '
					. $e->getMessage();
			}
		}

        if ($processed > 0 && $failed === 0) {
            $status = 'completed';
        } elseif ($processed > 0 && $failed > 0) {
            $status = 'partial';
        } else {
            $status = 'failed';
        }

        $import->update([
            'processed_files' => $processed,
            'failed_files' => $failed,
            'status' => $status,
        ]);

      /*  if ($processed === 0) {
            return redirect()
                ->route('admin.smart-po.index')
                ->with(
                    'error',
                    'No PO files could be processed. Please check the document format and Python PO service.'
                );
        }*/
		if ($processed === 0) {
			$errorMessage =
				'No PO files could be processed.';

			if (!empty($errors)) {
				$errorMessage .=
					' Error: ' .
					implode(' | ', $errors);
			}

			return redirect()
				->route('admin.smart-po.index')
				->with(
					'error',
					$errorMessage
				);
		}

        $message = $processed .
            ' PO file(s) processed successfully.';

        if ($failed > 0) {
            $message .= ' ' .
                $failed .
                ' file(s) failed.';
        }

        return redirect()
            ->route('admin.smart-po.index')
            ->with(
                'success',
                $message
            );
    }

    private function processSingleFile(
        $file,
        PoImport $import
    ): void {
        $userId = $this->userId();

        $directory = public_path(
            'uploads/purchase-orders/' .
            $userId
        );

        if (!File::exists($directory)) {
            File::makeDirectory(
                $directory,
                0755,
                true
            );
        }

        $extension = strtolower(
            $file->getClientOriginalExtension()
        );

        $storedFilename =
            now()->format('YmdHis') .
            '_' .
            Str::random(12) .
            '.' .
            $extension;

        $file->move(
            $directory,
            $storedFilename
        );

        $absolutePath =
            $directory .
            DIRECTORY_SEPARATOR .
            $storedFilename;

        $relativePath =
            'uploads/purchase-orders/' .
            $userId .
            '/' .
            $storedFilename;

        try {
            $response = Http::timeout(120)
                ->withHeaders([
                    'X-API-Key' =>
                        config(
                            'services.po_reader.api_key'
                        ),
                ])
                ->attach(
                    'file',
                    file_get_contents(
                        $absolutePath
                    ),
                    $file->getClientOriginalName()
                )
                ->post(
                    rtrim(
                        config(
                            'services.po_reader.url'
                        ),
                        '/'
                    ) .
                    '/read-po'
                );

            if (!$response->successful()) {
                throw new \RuntimeException(
                    'PO reader error: ' .
                    $response->body()
                );
            }

            $result = $response->json();

            if (
                empty($result['success']) ||
                empty($result['purchase_order'])
            ) {
                $this->saveFailedDetection(
                    $import,
                    $userId,
                    $file->getClientOriginalName(),
                    $relativePath,
                    $result
                );

                return;
            }

            $detectedCode =
                $result['detected_template']
                ?? null;

            $template = PoTemplate::where(
                'code',
                $detectedCode
            )
                ->where('status', true)
                ->first();

            if (!$template) {
                throw new \RuntimeException(
                    'Detected PO template is not active: ' .
                    ($detectedCode ?? 'Unknown')
                );
            }

            $data =
                $result['purchase_order'];

            $items =
                $data['items'] ?? [];

            $processingStatus =
                'processed';

            $remarks = [];

            if (empty($data['po_no'])) {
                $processingStatus =
                    'review_required';

                $remarks[] =
                    'PO number not detected.';
            }

            if (empty($items)) {
                $processingStatus =
                    'review_required';

                $remarks[] =
                    'No PO items detected.';
            }

            DB::transaction(
                function () use (
                    $import,
                    $template,
                    $userId,
                    $file,
                    $relativePath,
                    $result,
                    $data,
                    $items,
                    $processingStatus,
                    $remarks
                ) {
                    $po = PurchaseOrder::create([
                        'po_import_id' =>
                            $import->id,

                        'po_template_id' =>
                            $template->id,

                        'created_by' =>
                            $userId,

                        'po_no' =>
                            $data['po_no']
                            ?? null,

                        'po_date' =>
                            $data['po_date']
                            ?? null,

                        'delivery_date' =>
                            $data['delivery_date']
                            ?? null,

                        'expiry_date' =>
                            $data['expiry_date']
                            ?? null,

                        'vendor_code' =>
                            $data['vendor_code']
                            ?? data_get(
                                $data,
                                'vendor.code'
                            ),

                        'vendor_name' =>
                            $data['vendor_name']
                            ?? data_get(
                                $data,
                                'vendor.name'
                            ),

                        'buyer_name' =>
                            $data['buyer_name']
                            ?? data_get(
                                $data,
                                'buyer.name'
                            ),

                        'bill_to' =>
                            $data['bill_to']
                            ?? null,

                        'ship_to' =>
                            $data['ship_to']
                            ?? null,

                        'buyer_gstin' =>
                            $data['buyer_gstin']
                            ?? data_get(
                                $data,
                                'buyer.gstin'
                            ),

                        'vendor_gstin' =>
                            $data['vendor_gstin']
                            ?? data_get(
                                $data,
                                'vendor.gstin'
                            ),

                        'basic_amount' =>
                            $data['basic_amount']
                            ?? data_get(
                                $data,
                                'amounts.basic'
                            ),

                        'tax_amount' =>
                            $data['tax_amount']
                            ?? data_get(
                                $data,
                                'amounts.tax'
                            ),

                        'total_amount' =>
                            $data['total_amount']
                            ?? data_get(
                                $data,
                                'amounts.total'
                            ),

                        'currency' =>
                            $data['currency']
                            ?? 'INR',

                        'original_filename' =>
                            $file->getClientOriginalName(),

                        'file_path' =>
                            $relativePath,

                        'raw_text' =>
                            $result['raw_text']
                            ?? null,

                        'extracted_json' =>
                            $result,

                        'processing_status' =>
                            $processingStatus,

                        'processing_remark' =>
                            empty($remarks)
                                ? null
                                : implode(
                                    ' ',
                                    $remarks
                                ),
                    ]);

                    foreach (
                        $items as $index => $item
                    ) {
                        $po->items()->create([
                            'line_no' =>
                                $item['line_no']
                                ?? ($index + 1),

                            'item_code' =>
                                $item['item_code']
                                ?? $item['article_no']
                                ?? null,

                            'description' =>
                                $item['description']
                                ?? null,

                            'hsn_code' =>
                                $item['hsn_code']
                                ?? $item['hsn']
                                ?? null,

                            'ean' =>
                                $item['ean']
                                ?? null,

                            'quantity' =>
                                $item['quantity']
                                ?? $item['qty']
                                ?? null,

                            'uom' =>
                                $item['uom']
                                ?? null,

                            'mrp' =>
                                $item['mrp']
                                ?? null,

                            'unit_cost' =>
                                $item['unit_cost']
                                ?? $item['base_cost']
                                ?? $item['cost']
                                ?? null,

                            'cgst_percent' =>
                                $item['cgst_percent']
                                ?? null,

                            'sgst_percent' =>
                                $item['sgst_percent']
                                ?? null,

                            'igst_percent' =>
                                $item['igst_percent']
                                ?? null,

                            'cess_percent' =>
                                $item['cess_percent']
                                ?? null,

                            'total_amount' =>
                                $item['total_amount']
                                ?? null,

                            'raw_data' =>
                                $item,
                        ]);
                    }
                }
            );

        } catch (\Throwable $e) {
            if (File::exists($absolutePath)) {
                File::delete($absolutePath);
            }

            throw $e;
        }
    }

    private function saveFailedDetection(
        PoImport $import,
        int $userId,
        string $originalFilename,
        string $relativePath,
        array $result
    ): void {
        PurchaseOrder::create([
            'po_import_id' =>
                $import->id,

            'po_template_id' =>
                null,

            'created_by' =>
                $userId,

            'original_filename' =>
                $originalFilename,

            'file_path' =>
                $relativePath,

            'raw_text' =>
                $result['raw_text']
                ?? null,

            'extracted_json' =>
                $result,

            'processing_status' =>
                'detection_failed',

            'processing_remark' =>
                $result['message']
                ?? 'Unable to identify PO format.',
        ]);
    }

    public function show(int $id)
    {
        $purchaseOrder =
            $this->ownedPo($id);

        return view(
            'admin.smart-po.show',
            compact('purchaseOrder')
        );
    }

    public function original(int $id)
    {
        $purchaseOrder =
            $this->ownedPo($id);

        $path = public_path(
            $purchaseOrder->file_path
        );

        abort_unless(
            File::exists($path),
            404
        );

        return response()->file(
            $path
        );
    }

    public function excel(int $id)
    {
        $po = $this->ownedPo($id);

        $spreadsheet =
            new Spreadsheet();

        $sheet =
            $spreadsheet->getActiveSheet();

        $sheet->setTitle('Purchase Order');

        $sheet->setCellValue(
            'A1',
            'Purchase Order'
        );

        $sheet->setCellValue(
            'A3',
            'PO Company'
        );

        $sheet->setCellValue(
            'B3',
            $po->template->name
            ?? 'Unknown'
        );

        $sheet->setCellValue(
            'A4',
            'PO Number'
        );

        $sheet->setCellValueExplicit(
            'B4',
            (string) $po->po_no,
            DataType::TYPE_STRING
        );

        $sheet->setCellValue(
            'A5',
            'PO Date'
        );

        $sheet->setCellValue(
            'B5',
            optional(
                $po->po_date
            )->format('d-m-Y')
        );

        $sheet->setCellValue(
            'A6',
            'Vendor'
        );

        $sheet->setCellValue(
            'B6',
            $po->vendor_name
        );

        $sheet->setCellValue(
            'A7',
            'Buyer'
        );

        $sheet->setCellValue(
            'B7',
            $po->buyer_name
        );

        $sheet->setCellValue(
            'A8',
            'Basic Amount'
        );

        $sheet->setCellValue(
            'B8',
            $po->basic_amount
        );

        $sheet->setCellValue(
            'A9',
            'Tax Amount'
        );

        $sheet->setCellValue(
            'B9',
            $po->tax_amount
        );

        $sheet->setCellValue(
            'A10',
            'Total Amount'
        );

        $sheet->setCellValue(
            'B10',
            $po->total_amount
        );

        $headers = [
            'Line',
            'Item Code',
            'Description',
            'HSN',
            'EAN',
            'Qty',
            'UOM',
            'MRP',
            'Unit Cost',
            'CGST %',
            'SGST %',
            'IGST %',
            'Cess %',
            'Total',
        ];

        $row = 12;

        foreach (
            $headers as $column => $header
        ) {
            $sheet->setCellValue(
                chr(65 + $column) . $row,
                $header
            );
        }

        $row++;

        foreach ($po->items as $item) {
            $sheet->setCellValue(
                'A' . $row,
                $item->line_no
            );

            $sheet->setCellValueExplicit(
                'B' . $row,
                (string) $item->item_code,
                DataType::TYPE_STRING
            );

            $sheet->setCellValue(
                'C' . $row,
                $item->description
            );

            $sheet->setCellValueExplicit(
                'D' . $row,
                (string) $item->hsn_code,
                DataType::TYPE_STRING
            );

            $sheet->setCellValueExplicit(
                'E' . $row,
                (string) $item->ean,
                DataType::TYPE_STRING
            );

            $sheet->setCellValue(
                'F' . $row,
                $item->quantity
            );

            $sheet->setCellValue(
                'G' . $row,
                $item->uom
            );

            $sheet->setCellValue(
                'H' . $row,
                $item->mrp
            );

            $sheet->setCellValue(
                'I' . $row,
                $item->unit_cost
            );

            $sheet->setCellValue(
                'J' . $row,
                $item->cgst_percent
            );

            $sheet->setCellValue(
                'K' . $row,
                $item->sgst_percent
            );

            $sheet->setCellValue(
                'L' . $row,
                $item->igst_percent
            );

            $sheet->setCellValue(
                'M' . $row,
                $item->cess_percent
            );

            $sheet->setCellValue(
                'N' . $row,
                $item->total_amount
            );

            $row++;
        }

        foreach (
            range('A', 'N') as $column
        ) {
            $sheet
                ->getColumnDimension($column)
                ->setAutoSize(true);
        }

        $filename =
            'PO_' .
            preg_replace(
                '/[^A-Za-z0-9_-]/',
                '_',
                $po->po_no
                ?: $po->id
            ) .
            '.xlsx';

        $tempPath =
            storage_path(
                'app/' . $filename
            );

        $writer =
            new Xlsx($spreadsheet);

        $writer->save($tempPath);

        return response()
            ->download(
                $tempPath,
                $filename
            )
            ->deleteFileAfterSend(true);
    }

    public function pdf(int $id)
    {
        $purchaseOrder =
            $this->ownedPo($id);

        $filename =
            'PO_' .
            preg_replace(
                '/[^A-Za-z0-9_-]/',
                '_',
                $purchaseOrder->po_no
                ?: $purchaseOrder->id
            ) .
            '.pdf';

        return Pdf::loadView(
            'admin.smart-po.pdf',
            compact('purchaseOrder')
        )
            ->setPaper(
                'a4',
                'landscape'
            )
            ->download($filename);
    }
}