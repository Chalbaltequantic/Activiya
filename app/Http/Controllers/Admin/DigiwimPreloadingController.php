<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;


use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\DB;

//use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

use Barryvdh\DomPDF\Facade\Pdf;

use App\Models\Material;
use App\Models\Admin;
use App\Models\Ratedata;
use App\Models\TruckMaster;
use App\Models\DigiWimPreloading;
use App\Models\DigiwimPreloadingOperation;
use App\Models\DigiwimPreloadingOperationItem;
use App\Models\Vendor;
use App\Models\Siteplant;

use Auth;


class DigiWimPreloadingController extends Controller
{
   

    public function __construct()
    {
       
		$this->middleware('auth:admin'); 
    }

	
	public function index(Request $request)
    {
        $title = 'Digi Wim Preloading Data Upload';
        $pagetitle = $title.' Listing';
        $userid = auth()->user()->id;    
        return view('admin.digi_wim_preloading.index',compact(['pagetitle','title']));
    }
	
	public function import(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:xls,xlsx'
        ]);

        $file = $request->file('excel_file');
        $spreadsheet = IOFactory::load($file->getPathname());
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray(null, true, true, true);
		
		$created_by = Auth::user()->id; 
		
		$createddate = date('Y-m-d H:i:s');
		$errorRows = [];
		$insertedCount = 0;
		$validData = [];
		
		//Indent ID	Consignor Code*	Consignor Name	Consignor Location	P.O.No.	Invocie/Challan No.	Inv/Challan Date.	Consignee Code*	Consignee Name	Consignee Location	Material.Code*	Material Descriptions.	Batch No.	MFG Date	Expiry Date	UOM	Qty. (Units)	BIN NO	Goods Status	Transporter Code*	Transporter Name	Truck No	LR No.	LR Date	Truck Code*	Truck Description	Custom 1 	Custom 2	Custom 3	Custom 4

		
        DB::beginTransaction();
        try {
            foreach ($rows as $index => $row) {
                if ($index == 1) continue; // skip header row				
								
				$rowNumber = $index + 1;
				if (count(array_filter($row, fn($value) => trim((string)$value) !== '')) === 0) {
						continue;
					}
				$invoice_challan_date = $row['G'];
				$invoice_challandate = Carbon::parse($invoice_challan_date)->format('Y-m-d');
				
				$mfg_date = $row['N'];
				$mfgdate = Carbon::parse($mfg_date)->format('Y-m-d');
				
				$exp_date = $row['O'];
				$expirydate = Carbon::parse($exp_date)->format('Y-m-d');
				
				$lr_cn_date = $row['X'];
				$lr_date = Carbon::parse($lr_cn_date)->format('Y-m-d');


				$supplier = Siteplant::where("plant_site_code", $row['B'])->first();
				$suppliername = $supplier->plant_site_location_name;
				$supplierlocation = $supplier->city;

				$consignee = Siteplant::where("plant_site_code", $row['H'])->first();
				$consigneename = $consignee->plant_site_location_name ?? null;
				$consigneelocation = $consignee->city ?? null;
				
				$material = Material::where("material_code", $row['K'])->first();
				$materialdesc = $material->material_description ?? null;
				
				
				$transporter = Vendor::where('vendor_code', $row['T'])->first();
				$transporter_name = $transporter->vendor_name;
				
				
				$truck = TruckMaster::where('code', $row['Y'])->first();
				$vehicle_type = $truck->description ?? null;
				//echo $row['R']; echo"<br>";

                $data = [
					'indent_id'  			=> $row['A'] ?? null,					
					'consignor_code'     	=> $row['B'] ?? null,
					'consignor_name'    		=> $suppliername ?? null,
					'consignor_location'     => $supplierlocation ?? null,
					'po_no'					=> $row['E'] ?? null,
					'invoice_challan_no'    => $row['F'] ?? null,
					'invoice_challan_date'  => $invoice_challandate ?? null,					
					'consignee_code'        => $row['H'] ?? null ,
					'consignee_name' 		=> $consigneename ,
					'consignee_location'    => $consigneelocation,		
					'material_code'                => $row['K'] ?? null,
					'material_descriptions' => $materialdesc,
					'batch_no'        		=> $row['M'] ?? null,
					'mfg_date'        		=> $mfgdate ?? null,
					'expiry_date'        	=> $expirydate ?? null,
					'uom'        			=> $row['P'] ?? null,
					'qty'        			=> $row['Q'] ?? null,
					'bin_no'        		=> $row['R'] ?? null,
					'goods_status'        	=> $row['S'] ?? null,
					'transporter_code'      => $row['T'] ?? null,
					'transporter_name'      => $transporter_name ,
					'truck_no'        		=> $row['V'] ?? null,
					'lr_no'        			=> $row['W'] ?? null, 
					'lr_date'        		=> $lr_date ,
					'truck_code'       		=> $row['Y'] ?? null,
					'truck_description'     => $vehicle_type ,
					'remarks'     			=> $row['AA'] ?? null ,
					'custom_1'        		=> $row['AB'] ?? null,
					'custom_2'        		=> $row['AC'] ?? null,
					'custom_3'        		=> $row['AD'] ?? null,
					'custom_4'        		=> $row['AE'] ?? null,
                    'created_at' 			=> $createddate,
                    'created_by' 			=> Auth::user()->id,
                    'status' 				=> '1'
                ];
				
				
				// Check vendor code
					$vendorExists = Vendor::where('vendor_code', $data['transporter_code'])->exists();
					if (!$vendorExists) {
						$errorRows[] = ['row' => $rowNumber, 'reason' => 'Transporter code not found'];
						continue;
					}

					// Check truck code
					$truckExists = TruckMaster::where('code', $data['truck_code'])->exists();
					if (!$truckExists) {
						$errorRows[] = ['row' => $rowNumber, 'reason' => 'Truck code not found'];
						continue;
					}

					// Check consignor code
					$consignorExists = Siteplant::where('plant_site_code', $data['supplier_code'])->exists();
					if (!$consignorExists) {
						$errorRows[] = ['row' => $rowNumber, 'reason' => 'Supplier code not found'];
						continue;
					}

					// Check consignee code
					$consigneeExists = Siteplant::where('plant_site_code', $data['consignee_code'])->exists();
					if (!$consigneeExists) {
						$errorRows[] = ['row' => $rowNumber, 'reason' => 'Consignee code not found'];
						continue;
					}
					

            if ($exists) {
                $errorRows[] = ['row' => $rowNumber, 'reason' => 'Duplicate invoice / lrno entry'];
                continue;
            }
					
				DigiwimPreloading::create($data); 
				$insertedCount++;
            }

            DB::commit();
			if ($insertedCount === 0) 
			{
				// No data inserted
				return back()
					->withInput()
					->with([
						'errorRows' => $errorRows,
						'error' => 'No data inserted. Please correct the highlighted errors.',
					]);					
			}
			// Success, maybe partial insert
			return redirect()->back()->with([
				'success' => "$insertedCount rows inserted successfully.",
				'failedRows' => $errorRows
			]);

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
	
	public function digiwimdatalist(Request $request)
    {
        $title = 'Digi Wim Preloading Data List';
        $pagetitle = $title.' Listing';
		$user_role = Auth::user()->role_id;
		$data = $request->all();        
	    $datalist = DigiwimPreloading::orderBy('created_at', 'desc')->get();       
        return view('admin.digi_wim_preloading.datalist',compact(['pagetitle','title','datalist','user_role']));
    }
	
	
	//manual Upload
	
	public function manualupload()
	{
		$userid = auth()->user()->id; //get loggedin user id		
		return view('admin.digi_wim_preloading.manualupload');
	}
	
	public function save_manual_data(Request $request)
	{
		$created_by  = Auth::user()->id;
		$createddate = now();

		$indent_id              = $request->input('indent_id', []);
		$supplier_code          = $request->input('supplier_code', []);   //consignor code
		$supplier_name          = $request->input('supplier_name', []); // consignor name
		$supplier_location      = $request->input('supplier_location', []); //consignor location
		$po_no                  = $request->input('po_no', []);
		$invoice_challan_no     = $request->input('invoice_challan_no', []);
		$invoice_challan_date   = $request->input('invoice_challan_date', []);
		$consignee_code         = $request->input('consignee_code', []);
		$consignee_name         = $request->input('consignee_name', []);
		$consignee_location     = $request->input('consignee_location', []);
		$m_code                 = $request->input('m_code', []);
		$material_descriptions  = $request->input('material_descriptions', []);
		$batch_no               = $request->input('batch_no', []);
		$mfg_date               = $request->input('mfg_date', []);
		$expiry_date            = $request->input('expiry_date', []);
		$qty              		= $request->input('qty', []);
		$uom               		= $request->input('uom', []);
		$bin_no               	= $request->input('bin_no', []);
		$good_status            = $request->input('good_status', []);
		$transporter_code       = $request->input('transporter_code', []);
		$transporter_name       = $request->input('transporter_name', []);
		$truck_no               = $request->input('truck_no', []);
		$lr_no                  = $request->input('lr_no', []);
		$lr_date                = $request->input('lr_date', []);
		$truck_code             = $request->input('truck_code', []);
		$vehicle_type           = $request->input('vehicle_type', []); //truck description
		$custom_1               = $request->input('custom_1', []);
		$custom_2               = $request->input('custom_2', []);
		$custom_3               = $request->input('custom_3', []);
		$custom_4               = $request->input('custom_4', []);

		$count = count($indent_id);

		$insertedCount = 0;
		$errorRows = [];

		for ($i = 0; $i < $count; $i++) {

			$rowNumber = $i + 1;

			if (
				empty($indent_id[$i]) &&
				empty($supplier_code[$i]) &&
				empty($consignee_code[$i]) &&
				empty($m_code[$i]) &&
				empty($transporter_code[$i]) &&
				empty($truck_code[$i])
			) {
				continue;
			}

			try {

				if (empty($indent_id[$i])) {
					$errorRows[] = ['row' => $rowNumber, 'reason' => 'Indent ID is required'];
					continue;
				}

				if (empty($supplier_code[$i])) {
					$errorRows[] = ['row' => $rowNumber, 'reason' => 'Consignor code is required'];
					continue;
				}

				if (empty($consignee_code[$i])) {
					$errorRows[] = ['row' => $rowNumber, 'reason' => 'Consignee code is required'];
					continue;
				}

				if (empty($m_code[$i])) {
					$errorRows[] = ['row' => $rowNumber, 'reason' => 'Material code is required'];
					continue;
				}

				if (empty($transporter_code[$i])) {
					$errorRows[] = ['row' => $rowNumber, 'reason' => 'Transporter code is required'];
					continue;
				}

				if (empty($truck_code[$i])) {
					$errorRows[] = ['row' => $rowNumber, 'reason' => 'Truck code is required'];
					continue;
				}

				$supplier = Siteplant::where('plant_site_code', trim($supplier_code[$i]))->first();

				if (!$supplier) {
					$errorRows[] = ['row' => $rowNumber, 'reason' => 'Invalid Consignor code: ' . $supplier_code[$i]];
					continue;
				}

				$consignee = Siteplant::where('plant_site_code', trim($consignee_code[$i]))->first();

				if (!$consignee) {
					$errorRows[] = ['row' => $rowNumber, 'reason' => 'Invalid consignee code: ' . $consignee_code[$i]];
					continue;
				}

				$material = DB::table('materials')
					->where('material_code', trim($m_code[$i]))
					->first();

				if (!$material) {
					$errorRows[] = ['row' => $rowNumber, 'reason' => 'Invalid material code: ' . $m_code[$i]];
					continue;
				}

				$transporter = Vendor::where('vendor_code', trim($transporter_code[$i]))->first();

				if (!$transporter) {
					$errorRows[] = ['row' => $rowNumber, 'reason' => 'Invalid transporter code: ' . $transporter_code[$i]];
					continue;
				}

				$truck = TruckMaster::where('code', trim($truck_code[$i]))->first();

				if (!$truck) {
					$errorRows[] = ['row' => $rowNumber, 'reason' => 'Invalid truck code: ' . $truck_code[$i]];
					continue;
				}

				$invoicechallan_date = null;
				if (!empty($invoice_challan_date[$i])) {
					$invoicechallan_date = Carbon::parse($invoice_challan_date[$i])->format('Y-m-d');
				}

				$mfgdatef = null;
				if (!empty($mfg_date[$i])) {
					$mfgdatef = Carbon::parse($mfg_date[$i])->format('Y-m-d');
				}

				$expirydate = null;
				if (!empty($expiry_date[$i])) {
					$expirydate = Carbon::parse($expiry_date[$i])->format('Y-m-d');
				}

				$lrdatef = null;
				if (!empty($lr_date[$i])) {
					$lrdatef = Carbon::parse($lr_date[$i])->format('Y-m-d');
				}

				$data = [
					'indent_id'             => $indent_id[$i] ?? null,
					'consignor_code'         => $supplier_code[$i] ?? null,
					'consignor_name'         => $supplier_name[$i] ?? ($supplier->plant_name ?? null),
					'consignor_location'     => $supplier_location[$i] ?? ($supplier->city ?? null),
					'po_no'                 => $po_no[$i] ?? null,
					'invoice_challan_no'    => $invoice_challan_no[$i] ?? null,
					'invoice_challan_date'  => $invoicechallan_date,
					'consignee_code'        => $consignee_code[$i] ?? null,
					'consignee_name'        => $consignee_name[$i] ?? ($consignee->plant_name ?? null),
					'consignee_location'    => $consignee_location[$i] ?? ($consignee->city ?? null),
					'm_code'                => $m_code[$i] ?? null,
					'material_descriptions' => $material_descriptions[$i] ?? ($material->material_description ?? null),
					'batch_no'              => $batch_no[$i] ?? null,
					'mfg_date'              => $mfgdatef,
					'expiry_date'           => $expirydate,
					'qty'             		=> $qty[$i] ?? null,
					'bin_no'              	=> $bin_no[$i] ?? null,
					'uom'              		=> $uom[$i] ?? null,
					'good_status'          => $good_status[$i] ?? null,
					'transporter_code'      => $transporter_code[$i] ?? null,
					'transporter_name'      => $transporter_name[$i] ?? ($transporter->vendor_name ?? null),
					'truck_no'              => $truck_no[$i] ?? null,
					'lr_no'                 => $lr_no[$i] ?? null,
					'lr_date'               => $lrdatef,
					'truck_code'            => $truck_code[$i] ?? null,
					'truck_description'     => $vehicle_type[$i] ?? null,
					'remarks'     			=> $remarks[$i] ?? null,
					'custom_1'               => $custom_1[$i] ?? null,
					'custom_2'               => $custom_2[$i] ?? null,
					'custom_3'               => $custom_3[$i] ?? null,
					'custom_4'               => $custom_4[$i] ?? null,
					'created_at'            => $createddate,
					'created_by'            => $created_by,
					'status'                => '1',
				];

				DigiwimPreloading::create($data);

				$insertedCount++;

			} catch (\Exception $e) {

				$errorRows[] = [
					'row' => $rowNumber,
					'reason' => $e->getMessage()
				];

				continue;
			}
		}

		if ($insertedCount === 0) {
			return redirect()->back()
				->withInput()
				->with([
					'error' => 'No data inserted. Please correct row errors.',
					'errorRows' => $errorRows,
				]);
		}

		return redirect()->back()->with([
			'success' => $insertedCount . ' rows inserted successfully.',
			'errorRows' => $errorRows,
		]);
	}
	

   
	////delete items
	
	public function deleteItem(Request $request, int $id)
    {
        DB::transaction(function () use ($id) {

            $item = DigiwimPreloading::whereNull('deleted_at')
                ->lockForUpdate()
                ->findOrFail($id);

            $item->delete(); // uses SoftDeletes
        });

        return response()->json([
            'status'  => 'success',
            'message' => 'Item deleted successfully'
        ]);
    }
	
	//Fetch data for AJAX
	
		
	
	//AJAX: Fetch dependent data : origin city name , desination city, sku description
   
	public function fetchRowData(Request $request)
	{
		try {

			$supplier_code    = trim((string) $request->supplier_code);
			$consignee_code   = trim((string) $request->consignee_code);
			$material_code    = trim((string) $request->material_code);
			$transporter_code = trim((string) $request->transporter_code);
			$truck_code       = trim((string) $request->truck_code);

			if ($supplier_code == '') {
				return response()->json(['error' => 'Supplier code is required']);
			}

			if ($consignee_code == '') {
				return response()->json(['error' => 'Consignee code is required']);
			}

			if ($material_code == '') {
				return response()->json(['error' => 'Material code is required']);
			}

			if ($transporter_code == '') {
				return response()->json(['error' => 'Transporter code is required']);
			}

			if ($truck_code == '') {
				return response()->json(['error' => 'Truck code is required']);
			}

			$supplier = Siteplant::where('plant_site_code', $supplier_code)->first();

			if (!$supplier) {
				return response()->json(['error' => 'Invalid supplier code: ' . $supplier_code]);
			}

			$consignee = Siteplant::where('plant_site_code', $consignee_code)->first();

			if (!$consignee) {
				return response()->json(['error' => 'Invalid consignee code: ' . $consignee_code]);
			}

			$material = DB::table('materials')
				->where('material_code', $material_code)
				->first();

			if (!$material) {
				return response()->json(['error' => 'Invalid material code: ' . $material_code]);
			}

			$transporter = Vendor::where('vendor_code', $transporter_code)->first();

			if (!$transporter) {
				return response()->json(['error' => 'Invalid transporter code: ' . $transporter_code]);
			}

			$truck = TruckMaster::where('code', $truck_code)->first();

			if (!$truck) {
				return response()->json(['error' => 'Invalid truck code: ' . $truck_code]);
			}

			return response()->json([
				'supplier_name'        => $supplier->plant_site_name ?? '',
				'supplier_location'    => $supplier->city ?? '',
				'consignee_name'       => $consignee->plant_site_name ?? '',
				'consignee_location'   => $consignee->city ?? '',
				'material_description' => $material->material_description ?? '',
				'transporter_name'     => $transporter->vendor_name ?? '',
				'vehicle_type'         => $truck->description ?? '',
			]);

		} catch (\Exception $e) {

			return response()->json([
				'error' => $e->getMessage()
			], 500);
		}
	}
		
	///UNLOADING
	
	public function createOperation()
	{
		return view('admin.digi_wim_preloading.operation_create');
	}

	public function storeOperationHeader(Request $request)
	{
		$request->validate([
			'operation_type' => 'required',
			'invoice_challan_no' => 'required',
		]);

		$header = DigiwimPreloadingOperation::create([
			'operation_type' => $request->operation_type,
			'invoice_challan_no' => $request->invoice_challan_no,
			'invoice_date' => !empty($request->invoice_date) ? Carbon::parse($request->invoice_date)->format('Y-m-d') : null,
			
			'supplier_code_name' => $request->supplier_code_name,
			'transporter_name' => $request->transporter_name,
			'truck_number' => $request->truck_number,
			'truck_type' => $request->truck_type,
			'lr_no' => $request->lr_no,
			'uom' => $request->uom,
			'created_by' => Auth::id(),
			'status' => 1,
		]);

		
		$digiRows = DigiwimPreloading::where('invoice_challan_no', $request->invoice_challan_no)->get();

		return view('admin.digi_wim_preloading.operation_create', compact('header', 'digiRows'))
		->with('headerSubmitted', true);
	}
	
	public function storeOperationItem(Request $request)
	{
		$request->validate([
			'operation_id' => 'required|exists:digiwim_preloading_operations,id',
			'material_code' => 'required',
		]);

		try {

			DigiwimPreloadingOperationItem::create([
				'operation_id' => $request->operation_id,
				'digi_wim_preloading_id' => $request->digi_wim_preloading_id,
				'invoice_challan_no' => $request->invoice_challan_no,
				'material_code' => $request->material_code,
				'material_description' => $request->material_description,
				'batch_no' => $request->batch_no,
				'mfg_date' => !empty($request->mfg_date) ? \Carbon\Carbon::parse($request->mfg_date)->format('Y-m-d') : null,
				'expiry_date' => !empty($request->expiry_date) ? \Carbon\Carbon::parse($request->expiry_date)->format('Y-m-d') : null,
				'qty' => $request->qty,
				'bin_no' => $request->bin_no,
				'goods_status' => $request->goods_status,
				'remarks' => $request->remarks,
				'created_by' => Auth::id(),
				'status' => 1,
			]);

			return response()->json([
				'status' => true,
				'message' => 'Row posted successfully.'
			]);

		} catch (\Exception $e) {

			return response()->json([
				'status' => false,
				'message' => $e->getMessage()
			], 500);
		}
	}
	 
	public function operationList()
	{
		$headers = DigiwimPreloadingOperation::with('items', 'creator')
        ->where('operation_type', 'loading')
        ->orderBy('id', 'desc')
        ->get();

		return view('admin.digi_wim_preloading.operation_list', compact('headers'));
	}
	


	public function operationPdf($id)
	{
		//$header = DigiwimOperation::with('items')->findOrFail($id);
		$header = DigiwimPreloadingOperation::with('items', 'creator')->findOrFail($id);
		$pdf = Pdf::loadView('admin.digi_wim_preloading.operation_pdf', compact('header'))
			->setPaper('a4', 'landscape');

		return $pdf->download($header->operation_type . '-' . $header->invoice_challan_no . '.pdf');
	}
	
	public function viewMaterials($id)
	{
		$header = DigiwimPreloadingOperation::with('items')->findOrFail($id);

		return view('admin.digi_wim_preloading.operation_materials', compact('header'));
	}
	
	
	

		
	
}
