<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\DigiwimGoodsPoUpload;
use App\Models\Siteplant;
use App\Models\Vendor;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use PhpOffice\PhpSpreadsheet\IOFactory;

class DigiwimGoodsPoUploadController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }


    public function index(Request $request)
    {
        $title = 'DigiWim Goods PO Upload';

        $pagetitle = $title . ' Listing';

        $userid = Auth::guard('admin')->id();

        return view(
            'admin.digiwim_goods_po_uploads.index',
            compact(
                'pagetitle',
                'title',
                'userid'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Excel Import
    |--------------------------------------------------------------------------
    |
    | Expected Excel column order:
    |
    | A  = Buyer Code
    | B  = Buyer Name
    | C  = Buyer Location
    | D  = Bill To Code
    | E  = Bill To Name
    | F  = Bill To Location
    | G  = Ship To Code
    | H  = Ship To Name
    | I  = Ship To Location
    | J  = Supplier Code
    | K  = Supplier Name
    | L  = Supplier Location
    | M  = PO Number
    | N  = PO Date
    | O  = Material Code
    | P  = Material Description
    | Q  = Qty Units
    | R  = Total CS
    | S  = Rate Per Unit
    | T  = Tax
    | U  = Conversion
    | V  = Discount
    | W  = Inco Terms
    | X  = Freight
    | Y  = Custom
    | Z  = Custom 1
    | AA = Custom 2
    | AB = Custom 3
    | AC = Custom 4
    |
    */

    public function import(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:xls,xlsx',
        ]);

        $file = $request->file('excel_file');

        $spreadsheet = IOFactory::load(
            $file->getPathname()
        );

        $sheet = $spreadsheet->getActiveSheet();

        $rows = $sheet->toArray(
            null,
            true,
            true,
            true
        );

        $createdBy = Auth::guard('admin')->id();

        $createdDate = now();

        $errorRows = [];

        $insertedCount = 0;

        DB::beginTransaction();

        try {

            foreach ($rows as $index => $row) {

                /* Skip Excel header. */
                if ($index == 1) {
                    continue;
                }

                $rowNumber = $index;

                /* Skip completely empty Excel row. */
                if (
                    count(
                        array_filter(
                            $row,
                            function ($value) {
                                return trim((string) $value) !== '';
                            }
                        )
                    ) === 0
                ) {
                    continue;
                }


                $buyerCode = trim( (string) ($row['A'] ?? ''));

                $billToCode = trim( (string) ($row['D'] ?? ''));

                $shipToCode = trim((string) ($row['G'] ?? ''));

                $supplierCode = trim((string) ($row['J'] ?? ''));

                $poNo = trim((string) ($row['M'] ?? ''));

                $materialCode = trim((string) ($row['O'] ?? ''));

                if ($buyerCode == '') {

                    $errorRows[] = [
                        'row' => $rowNumber,
                        'reason' => 'Buyer Code is required',
                    ];

                    continue;
                }


                if ($billToCode == '') {

                    $errorRows[] = [
                        'row' => $rowNumber,
                        'reason' => 'Bill To Code is required',
                    ];

                    continue;
                }


                if ($shipToCode == '') {

                    $errorRows[] = [
                        'row' => $rowNumber,
                        'reason' => 'Ship To Code is required',
                    ];

                    continue;
                }


                if ($supplierCode == '') {

                    $errorRows[] = [
                        'row' => $rowNumber,
                        'reason' => 'Supplier Code is required',
                    ];

                    continue;
                }


                if ($poNo == '') {

                    $errorRows[] = [
                        'row' => $rowNumber,
                        'reason' => 'PO Number is required',
                    ];

                    continue;
                }


                if ($materialCode == '') {

                    $errorRows[] = [
                        'row' => $rowNumber,
                        'reason' => 'Material Code is required',
                    ];

                    continue;
                }


                /* Fetch Buyer.*/

                $buyer = Siteplant::where(
                    'plant_site_code',
                    $buyerCode
                )->first();


                if (!$buyer) {

                    $errorRows[] = [
                        'row' => $rowNumber,
                        'reason' => 'Invalid Buyer Code: ' . $buyerCode,
                    ];

                    continue;
                }


                /* Fetch Bill To.*/

                $billTo = Siteplant::where('plant_site_code',$billToCode )->first();


                if (!$billTo) {
                    $errorRows[] = [
                        'row' => $rowNumber,
                        'reason' => 'Invalid Bill To Code: ' . $billToCode,
                    ];

                    continue;
                }


                /* Fetch Ship To. */

                $shipTo = Siteplant::where('plant_site_code', $shipToCode )->first();


                if (!$shipTo) {

                    $errorRows[] = [
                        'row' => $rowNumber,
                        'reason' => 'Invalid Ship To Code: ' . $shipToCode,
                    ];

                    continue;
                }


                /* Fetch Supplier. */

                $supplier = Vendor::where('vendor_code',$supplierCode)->first();


                if (!$supplier) {

                    $errorRows[] = [
                        'row' => $rowNumber,
                        'reason' => 'Invalid Supplier Code: ' . $supplierCode,
                    ];

                    continue;
                }


                /* Fetch Material.  */

                $material = DB::table('materials')
                    ->where( 'material_code', $materialCode)
                    ->first();


                if (!$material) {

                    $errorRows[] = [
                        'row' => $rowNumber,
                        'reason' => 'Invalid Material Code: ' . $materialCode,
                    ];

                    continue;
                }


                /* Prevent duplicate PO + Material.*/

              /*  $exists = DigiwimGoodsPoUpload::where( 'po_no', $poNo)
                    ->where(
                        'material_code',
                        $materialCode
                    )
                    ->exists();


                if ($exists) {

                    $errorRows[] = [
                        'row' => $rowNumber,
                        'reason' =>
                            'Duplicate PO and Material entry: ' .
                            $poNo .
                            ' / ' .
                            $materialCode,
                    ];

                    continue;
                }
				*/

                $poDate = null;

                if (!empty($row['N'])) {

                    try {

                        $poDate = Carbon::parse($row['N'] )->format('Y-m-d');

                    } catch (\Exception $exception) {

                        $errorRows[] = [
                            'row' => $rowNumber,
                            'reason' =>
                                'Invalid PO Date: ' .
                                $row['N'],
                        ];

                        continue;
                    }
                }


                $data = [

                    'buyer_code' => $buyerCode,
                    'buyer_name' => $this->getSiteName($buyer),
                    'buyer_location' =>  $this->getSiteLocation($buyer),
                    'bill_to_code' => $billToCode,
                    'bill_to_name' => $this->getSiteName($billTo),
                    'bill_to_location' => $this->getSiteLocation($billTo),
                    'ship_to_code' => $shipToCode,
                    'ship_to_name' => $this->getSiteName($shipTo),
                    'ship_to_location' => $this->getSiteLocation($shipTo),
                    'supplier_code' => $supplierCode,
                    'supplier_name' =>  $supplier->vendor_name ?? null,
                    'supplier_location' => $this->getVendorLocation($supplier),

                    'po_no' => $poNo,
                    'po_date' =>  $poDate,
                    'material_code' => $materialCode,
                    'material_description' =>  $material->material_description ?? null,
                    'qty_units' => $this->cleanNumber( $row['Q'] ?? null ),
                    'total_cs' => $this->cleanNumber( $row['R'] ?? null),
                    'rate_per_unit' => $this->cleanNumber($row['S'] ?? null ),
                    'tax' =>  $this->cleanNumber( $row['T'] ?? null ),
                    'conversion' => $this->cleanNumber($row['U'] ?? null ),
                    'discount' => $this->cleanNumber( $row['V'] ?? null ),
                    'inco_terms' => $row['W'] ?? null,
                    'freight' =>$this->cleanNumber($row['X'] ?? null),
                    'custom' => $row['Y'] ?? null,
                    'custom_1' =>  $row['Z'] ?? null,
                    'custom_2' =>  $row['AA'] ?? null,
                    'custom_3' =>  $row['AB'] ?? null,
                    'custom_4' =>   $row['AC'] ?? null,
                    'added_by' =>   $createdBy,
                    'updated_by' => null,
                    'created_at' =>$createdDate,
                    'updated_at' => $createdDate,
                ];


                DigiwimGoodsPoUpload::create( $data);

                $insertedCount++;
            }


            DB::commit();


            if ($insertedCount === 0) {

                return redirect()
                    ->back()
                    ->withInput()
                    ->with([
                        'error' =>
                            'No data inserted. Please correct the row errors.',

                        'errorRows' =>
                            $errorRows,
                    ]);
            }


            return redirect()
                ->back()
                ->with([
                    'success' =>
                        $insertedCount .
                        ' row(s) inserted successfully.',

                    'errorRows' =>
                        $errorRows,
                ]);

        } catch (\Exception $exception) {

            DB::rollBack();

            return redirect()
                ->back()
                ->withInput()
                ->with(
                    'error',
                    'Error: ' .
                    $exception->getMessage()
                );
        }
    }


    /*Goods PO Data List */

    public function datalist(Request $request)
    {
        $title = 'DigiWim Goods PO Data List';

        $pagetitle = $title . ' Listing';

        $userRole = Auth::guard('admin')->user()->role_id ?? null;


        $datalist = DigiwimGoodsPoUpload::orderBy(
                'created_at',
                'desc'
            )
            ->get();


        return view(
            'admin.digiwim_goods_po_uploads.datalist',
            compact(
                'pagetitle',
                'title',
                'datalist',
                'userRole'
            )
        );
    }


    /* Manual Upload Page*/

    public function manualUpload()
    {
        $userid = Auth::guard('admin')->id();

        return view(
            'admin.digiwim_goods_po_uploads.manual_upload',
            compact('userid')
        );
    }


    /* Save Manual Upload Data*/

    public function saveManualUpload(Request $request)
    {
        $createdBy = Auth::guard('admin')->id();

        $createdDate = now();


        $buyerCode = $request->input( 'buyer_code',[]);

        $buyerName = $request->input('buyer_name',[]);

        $buyerLocation = $request->input('buyer_location', []  );


        $billToCode = $request->input( 'bill_to_code', []);

        $billToName = $request->input('bill_to_name',[]);

        $billToLocation = $request->input( 'bill_to_location',[] );


        $shipToCode = $request->input('ship_to_code',[]);

        $shipToName = $request->input( 'ship_to_name',[]);

        $shipToLocation = $request->input( 'ship_to_location',[]);

        $supplierCode = $request->input('supplier_code', []);

        $supplierName = $request->input('supplier_name',[]);

        $supplierLocation = $request->input('supplier_location',[]);

        $poNo = $request->input('po_no',[]);

        $poDate = $request->input('po_date',[]);

        $materialCode = $request->input('material_code', []);

        $materialDescription = $request->input('material_description',[]);

        $qtyUnits = $request->input('qty_units',[] );

        $totalCs = $request->input('total_cs',[]);

        $ratePerUnit = $request->input('rate_per_unit',[]);

        $tax = $request->input( 'tax',[]);

        $conversion = $request->input('conversion',[] );

        $discount = $request->input( 'discount',[]);

        $incoTerms = $request->input('inco_terms',[] );

        $freight = $request->input('freight',[]);


        $custom = $request->input('custom',[]);

        $custom1 = $request->input('custom_1',[] );

        $custom2 = $request->input('custom_2',[]);

        $custom3 = $request->input('custom_3',[] );

        $custom4 = $request->input('custom_4', [] );


        /* Get maximum submitted row count.*/
        $count = max(
            count($buyerCode),
            count($billToCode),
            count($shipToCode),
            count($supplierCode),
            count($poNo),
            count($materialCode)
        );


        $insertedCount = 0;

        $errorRows = [];


        for ($i = 0; $i < $count; $i++) {

            $rowNumber = $i + 1;


            /* Skip completely empty row. */
            if (
                empty($buyerCode[$i]) &&
                empty($billToCode[$i]) &&
                empty($shipToCode[$i]) &&
                empty($supplierCode[$i]) &&
                empty($poNo[$i]) &&
                empty($materialCode[$i])
            ) {
                continue;
            }


            try {

                /* Required fields.  */

                if (empty($buyerCode[$i])) {

                    $errorRows[] = [
                        'row' => $rowNumber,
                        'reason' => 'Buyer Code is required',
                    ];

                    continue;
                }


                if (empty($billToCode[$i])) {

                    $errorRows[] = [
                        'row' => $rowNumber,
                        'reason' => 'Bill To Code is required',
                    ];

                    continue;
                }


                if (empty($shipToCode[$i])) {

                    $errorRows[] = [
                        'row' => $rowNumber,
                        'reason' => 'Ship To Code is required',
                    ];

                    continue;
                }


                if (empty($supplierCode[$i])) {

                    $errorRows[] = [
                        'row' => $rowNumber,
                        'reason' => 'Supplier Code is required',
                    ];

                    continue;
                }


                if (empty($poNo[$i])) {

                    $errorRows[] = [
                        'row' => $rowNumber,
                        'reason' => 'PO Number is required',
                    ];

                    continue;
                }


                if (empty($materialCode[$i])) {

                    $errorRows[] = [
                        'row' => $rowNumber,
                        'reason' => 'Material Code is required',
                    ];

                    continue;
                }


                /* Buyer validation. */

                $buyer = Siteplant::where(
                    'plant_site_code',
                    trim($buyerCode[$i])
                )->first();


                if (!$buyer) {

                    $errorRows[] = [
                        'row' => $rowNumber,
                        'reason' =>
                            'Invalid Buyer Code: ' .
                            $buyerCode[$i],
                    ];

                    continue;
                }


                /* Bill To validation*/

                $billTo = Siteplant::where( 'plant_site_code',trim($billToCode[$i]) )->first();


                if (!$billTo) {

                    $errorRows[] = [
                        'row' => $rowNumber,
                        'reason' =>
                            'Invalid Bill To Code: ' .
                            $billToCode[$i],
                    ];

                    continue;
                }


                /* Ship To validation*/

                $shipTo = Siteplant::where('plant_site_code', trim($shipToCode[$i]) )->first();


                if (!$shipTo) {

                    $errorRows[] = [
                        'row' => $rowNumber,
                        'reason' =>
                            'Invalid Ship To Code: ' .
                            $shipToCode[$i],
                    ];

                    continue;
                }


                /*Supplier validation*/

                $supplier = Vendor::where('vendor_code', trim($supplierCode[$i]))->first();


                if (!$supplier) {

                    $errorRows[] = [
                        'row' => $rowNumber,
                        'reason' =>
                            'Invalid Supplier Code: ' .
                            $supplierCode[$i],
                    ];

                    continue;
                }


                /* Material validation. */

                $material = DB::table('materials')
                    ->where('material_code', trim($materialCode[$i]) )
                    ->first();


                if (!$material) {

                    $errorRows[] = [
                        'row' => $rowNumber,
                        'reason' =>
                            'Invalid Material Code: ' .
                            $materialCode[$i],
                    ];

                    continue;
                }


                /* Duplicate validation. */

              /*  $exists = DigiwimGoodsPoUpload::where('po_no', trim($poNo[$i]))
                    ->where('material_code', trim($materialCode[$i])
                    )->exists();


                if ($exists) {

                    $errorRows[] = [
                        'row' => $rowNumber,
                        'reason' =>
                            'Duplicate PO and Material entry: ' .
                            $poNo[$i] .
                            ' / ' .
                            $materialCode[$i],
                    ];

                    continue;
                }
*/

                /* PO Date. */

                $formattedPoDate = null;


                if (!empty($poDate[$i])) {

                    try {

                        $formattedPoDate = Carbon::parse($poDate[$i] )->format('Y-m-d');

                    } catch (\Exception $exception) {

                        $errorRows[] = [
                            'row' => $rowNumber,
                            'reason' =>
                                'Invalid PO Date: ' .
                                $poDate[$i],
                        ];

                        continue;
                    }
                }

                $data = [

                    'buyer_code' =>trim($buyerCode[$i]),

                    'buyer_name' =>$this->getSiteName($buyer),

                    'buyer_location' =>$this->getSiteLocation($buyer),


                    'bill_to_code' => trim($billToCode[$i]),

                    'bill_to_name' =>$this->getSiteName($billTo),

                    'bill_to_location' =>$this->getSiteLocation($billTo),


                    'ship_to_code' =>trim($shipToCode[$i]),

                    'ship_to_name' =>$this->getSiteName($shipTo),

                    'ship_to_location' =>$this->getSiteLocation($shipTo),


                    'supplier_code' =>trim($supplierCode[$i]),

                    'supplier_name' =>$supplier->vendor_name ?? null,

                    'supplier_location' =>$this->getVendorLocation($supplier),


                    'po_no' =>trim($poNo[$i]),

                    'po_date' =>$formattedPoDate,


                    'material_code' =>trim($materialCode[$i]),

                    'material_description' =>$material->material_description ?? null,


                    'qty_units' =>$this->cleanNumber(    $qtyUnits[$i] ?? null),

                    'total_cs' =>$this->cleanNumber(    $totalCs[$i] ?? null),

                    'rate_per_unit' =>$this->cleanNumber(    $ratePerUnit[$i] ?? null),

                    'tax' =>$this->cleanNumber(    $tax[$i] ?? null),

                    'conversion' =>$this->cleanNumber(    $conversion[$i] ?? null),

                    'discount' =>$this->cleanNumber(    $discount[$i] ?? null),


                    'inco_terms' =>$incoTerms[$i] ?? null,


                    'freight' =>$this->cleanNumber(    $freight[$i] ?? null),


                    'custom' =>$custom[$i] ?? null,

                    'custom_1' =>$custom1[$i] ?? null,

                    'custom_2' =>$custom2[$i] ?? null,

                    'custom_3' =>$custom3[$i] ?? null,

                    'custom_4' =>$custom4[$i] ?? null,


                    'added_by' =>$createdBy,

                    'updated_by' =>null,

                    'created_at' =>$createdDate,

                    'updated_at' =>$createdDate,
                ];


                DigiwimGoodsPoUpload::create(
                    $data
                );


                $insertedCount++;

            } catch (\Exception $exception) {

                $errorRows[] = [
                    'row' => $rowNumber,
                    'reason' => $exception->getMessage(),
                ];

                continue;
            }
        }


        if ($insertedCount === 0) {

            return redirect()
                ->back()
                ->withInput()
                ->with([
                    'error' =>'No data inserted. Please correct row errors.',

                    'errorRows' =>$errorRows,
                ]);
        }


        return redirect()
            ->back()
            ->with([
                'success' =>
                    $insertedCount .
                    ' row(s) inserted successfully.',

                'errorRows' =>
                    $errorRows,
            ]);
    }


    /* Delete Goods PO Item*/

    public function deleteItem(
        Request $request,
        int $id
    ) {
        try {

            DB::transaction(function () use ($id) {

                $item = DigiwimGoodsPoUpload::whereNull('deleted_at'
                    )
                    ->lockForUpdate()
                    ->findOrFail($id);


                $item->updated_by =
                    Auth::guard('admin')->id();


                $item->save();

                /*
                 * Uses SoftDeletes when the model has SoftDeletes trait.
                 */
                $item->delete();
            });


            return response()->json([
                'status' => 'success',
                'message' => 'Goods PO item deleted successfully',
            ]);

        } catch (\Exception $exception) {

            return response()->json([
                'status' => 'error',
                'message' => $exception->getMessage(),
            ], 500);
        }
    }


    /* AJAX Fetch Dependent Data*/

    public function fetchRow(Request $request)
    {
        try {

            $buyerCode = trim(
                (string) $request->buyer_code
            );

            $billToCode = trim(
                (string) $request->bill_to_code
            );

            $shipToCode = trim(
                (string) $request->ship_to_code
            );

            $supplierCode = trim(
                (string) $request->supplier_code
            );

            $materialCode = trim(
                (string) $request->material_code
            );


            if ($buyerCode == '') {

                return response()->json([
                    'error' => 'Buyer Code is required',
                ]);
            }


            if ($billToCode == '') {

                return response()->json([
                    'error' => 'Bill To Code is required',
                ]);
            }


            if ($shipToCode == '') {

                return response()->json([
                    'error' => 'Ship To Code is required',
                ]);
            }


            if ($supplierCode == '') {

                return response()->json([
                    'error' => 'Supplier Code is required',
                ]);
            }


            if ($materialCode == '') {

                return response()->json([
                    'error' => 'Material Code is required',
                ]);
            }


            /* Buyer.*/

            $buyer = Siteplant::where(
                'plant_site_code',
                $buyerCode
            )->first();


            if (!$buyer) {

                return response()->json([
                    'error' =>'Invalid Buyer Code: ' .$buyerCode,
                ]);
            }


            /* Bill To.  */

            $billTo = Siteplant::where(
                'plant_site_code',
                $billToCode
            )->first();


            if (!$billTo) {

                return response()->json([
                    'error' =>'Invalid Bill To Code: ' .$billToCode,
                ]);
            }


            /* Ship To.*/

            $shipTo = Siteplant::where(
                'plant_site_code',
                $shipToCode
            )->first();


            if (!$shipTo) {

                return response()->json([
                    'error' =>'Invalid Ship To Code: ' .$shipToCode,
                ]);
            }


            /* Supplier. */

            $supplier = Vendor::where(
                'vendor_code',
                $supplierCode
            )->first();


            if (!$supplier) {

                return response()->json([
                    'error' =>'Invalid Supplier Code: ' .$supplierCode,
                ]);
            }


            /* Material. */

            $material = DB::table('materials')
                ->where(
                    'material_code',
                    $materialCode
                )
                ->first();


            if (!$material) {

                return response()->json([
                    'error' =>'Invalid Material Code: ' .$materialCode,
                ]);
            }


            return response()->json([

                'buyer_name' =>
                    $this->getSiteName($buyer),

                'buyer_location' =>
                    $this->getSiteLocation($buyer),


                'bill_to_name' =>
                    $this->getSiteName($billTo),

                'bill_to_location' =>
                    $this->getSiteLocation($billTo),


                'ship_to_name' =>
                    $this->getSiteName($shipTo),

                'ship_to_location' =>
                    $this->getSiteLocation($shipTo),


                'supplier_name' =>
                    $supplier->vendor_name ?? '',

                'supplier_location' =>
                    $this->getVendorLocation($supplier),


                'material_description' =>
                    $material->material_description ?? '',
            ]);

        } catch (\Exception $exception) {

            return response()->json([
                'error' => $exception->getMessage(),
            ], 500);
        }
    }


    private function getSiteName($site)
    {
        return $site->plant_site_name
            ?? $site->plant_site_location_name
            ?? $site->plant_name
            ?? $site->name
            ?? null;
    }


    private function getSiteLocation($site)
    {
        return $site->city
            ?? $site->plant_site_location
            ?? $site->site_location
            ?? $site->location
            ?? $site->address
            ?? null;
    }


    private function getVendorLocation($vendor)
    {
        return $vendor->vendor_location
            ?? $vendor->supplier_location
            ?? $vendor->city
            ?? $vendor->location
            ?? $vendor->address
            ?? null;
    }


    private function cleanNumber($value)
    {
        if ($value === null || trim((string) $value) === '') {
            return null;
        }

        $value = str_replace(',','', trim((string) $value));


        if (!is_numeric($value)) {
            return null;
        }


        return $value;
    }
}