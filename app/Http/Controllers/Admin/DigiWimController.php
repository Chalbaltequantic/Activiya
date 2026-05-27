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

use App\Models\Material;
use App\Models\Admin;
use App\Models\Ratedata;
use App\Models\TruckMaster;
use App\Models\DigiWim;
use App\Models\Vendor;
use App\Models\Siteplant;

use Auth;


class DigiWimController extends Controller
{
   

    public function __construct()
    {
       
		$this->middleware('auth:admin'); 
    }

	
	public function index(Request $request)
    {
        $title = 'Digi Wim Data Upload';
        $pagetitle = $title.' Listing';
        $userid = auth()->user()->id;    
        return view('admin.digi_wim.index',compact(['pagetitle','title']));
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
				
				$lr_cn_date = $row['U'];
				$lr_date = Carbon::parse($lr_cn_date)->format('Y-m-d');
								
				$shipment_inv_value = preg_replace("/,+/", "", $row['Q']);
				$delivery_gross_weight = $row['R'];

				$supplier = Siteplant::where("plant_site_code", $row['B'])->first();
				$suppliername = $supplier->plant_site_location_name;
				$supplierlocation = $supplier->city;

				$consignee = Siteplant::where("plant_site_code", $row['H'])->first();
				$consigneename = $consignee->plant_site_location_name ?? null;
				$consigneelocation = $consignee->city ?? null;
				
				$material = Material::where("material_code", $row['K'])->first();
				$materialdesc = $material->material_description ?? null;
				
				
				$transporter = Vendor::where('vendor_code', $row['R'])->first();
				$transporter_name = $transporter->vendor_name;
				
				
				$truck = TruckMaster::where('code', $row['X'])->first();
				
				//echo $row['R']; echo"<br>";

                $data = [
					'indent_id'  			=> $row['A'] ?? null,
					'supplier_code'     	=> $row['B'] ?? null,
					'supplier_name'    		=> $suppliername ?? null,
					'supplier_location'     => $supplierlocation ?? null,
					'po_no'              	=> $row['E'] ?? null,
					'invoice_challan_no'    => $row['F'] ?? null,
					'invoice_challan_date'  => $invoice_challandate ?? null,
					'consignee_code'        => $row['H'] ?? null ,
					'consignee_name' 		=> $consigneename ,
					'consignee_location'    => $consigneelocation,		
					'm_code'                => $row['K'] ?? null,
					'material_descriptions' => $materialdesc,
					'batch_no'        		=> $row['M'] ?? null,
					'mfg_date'        		=> $mfgdate ?? null,
					'expiry_date'        	=> $expirydate ?? null,
					'qty_units'        		=> $row['P'] ?? null,
					'total_cs'        		=> $row['Q'] ?? null,
					'transporter_code'      => $row['R'] ?? null,
					'transporter_name'      => $transporter_name ,
					'truck_no'        		=> $row['T'] ?? null,
					'lr_no'        			=> $row['U'] ?? null, 
					'lr_date'        		=> $lr_date ,
					'ewaybill_no'        	=> $row['W'] ?? null, 
					'truck_code'       		=> $row['X'] ?? null,
					'vehicle_type'        	=> $vehicle_type ,
					'custom'        		=> $row['Z'] ?? null,
					'custom_1'        		=> $row['AA'] ?? null,
					'custom_2'        		=> $row['AB'] ?? null,
					'custom_3'        		=> $row['AC'] ?? null,
					'custom_4'        		=> $row['AD'] ?? null,
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
					
					DigiWim::create($data); 
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
        $title = 'Digi Wim Data List';
        $pagetitle = $title.' Listing';
		$user_role = Auth::user()->role_id;
		$data = $request->all();        
	    $datalist = Digiwim::orderBy('created_at', 'desc')->get();       
        return view('admin.digi_wim.datalist',compact(['pagetitle','title','datalist','user_role']));
    }
	
	
	//manual Upload
	
	public function manualupload()
	{
		$userid = auth()->user()->id; //get loggedin user id		
		return view('admin.digi_wim.manualupload');
	}
	
	public function save_manual_data(Request $request)
	{
		$created_by  = Auth::user()->id;
		$createddate = now();

		$indent_id              = $request->input('indent_id', []);
		$supplier_code          = $request->input('supplier_code', []);
		$supplier_name          = $request->input('supplier_name', []);
		$supplier_location      = $request->input('supplier_location', []);
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
		$qty_units              = $request->input('qty_units', []);
		$total_cs               = $request->input('total_cs', []);
		$transporter_code       = $request->input('transporter_code', []);
		$transporter_name       = $request->input('transporter_name', []);
		$truck_no               = $request->input('truck_no', []);
		$lr_no                  = $request->input('lr_no', []);
		$lr_date                = $request->input('lr_date', []);
		$ewaybill_no            = $request->input('ewaybill_no', []);
		$truck_code             = $request->input('truck_code', []);
		$vehicle_type           = $request->input('vehicle_type', []);
		$custom                 = $request->input('custom', []);
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
					$errorRows[] = ['row' => $rowNumber, 'reason' => 'Supplier code is required'];
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
					$errorRows[] = ['row' => $rowNumber, 'reason' => 'Invalid supplier code: ' . $supplier_code[$i]];
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
					'supplier_code'         => $supplier_code[$i] ?? null,
					'supplier_name'         => $supplier_name[$i] ?? ($supplier->plant_name ?? null),
					'supplier_location'     => $supplier_location[$i] ?? ($supplier->city ?? null),
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
					'qty_units'             => $qty_units[$i] ?? null,
					'total_cs'              => $total_cs[$i] ?? null,
					'transporter_code'      => $transporter_code[$i] ?? null,
					'transporter_name'      => $transporter_name[$i] ?? ($transporter->vendor_name ?? null),
					'truck_no'              => $truck_no[$i] ?? null,
					'lr_no'                 => $lr_no[$i] ?? null,
					'lr_date'               => $lrdatef,
					'ewaybill_no'           => $ewaybill_no[$i] ?? null,
					'truck_code'            => $truck_code[$i] ?? null,
					'vehicle_type'          => $vehicle_type[$i] ?? ($truck->description ?? null),
					'custom'                => $custom[$i] ?? null,
					'custom1'               => $custom_1[$i] ?? null,
					'custom2'               => $custom_2[$i] ?? null,
					'custom3'               => $custom_3[$i] ?? null,
					'custom4'               => $custom_4[$i] ?? null,
					'created_at'            => $createddate,
					'updated_at'            => $createddate,
					'created_by'            => $created_by,
					'status'                => '1',
				];

				DigiWim::create($data);

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
	
/*	public function save_manual_data(Request $request)
	{
		$created_by   = Auth::user()->id;
		$createddate  = now();

		$indent_id      = $request->indent_id ?? [];
		$supplier_code           = $request->supplier_code ?? [];
		$supplier_name     = $request->supplier_name ?? [];
		$supplier_location      = $request->supplier_location ?? [];
		$po_no              = $request->po_no ?? [];
		$invoice_challan_no       = $request->invoice_challan_no ?? [];
		$invoice_challan_date              = $request->invoice_challan_date ?? [];
		$consignee_code             = $request->consignee_code ?? [];
		$consignee_name = $request->consignee_name ?? [];
		$consignee_location    = $request->consignee_location ?? [];		
		$m_code                   = $request->m_code ?? [];
		$material_descriptions        = $request->material_descriptions ?? [];
		$batch_no        = $request->batch_no ?? [];
		$mfg_date        = $request->mfg_date ?? [];
		$expiry_date        = $request->expiry_date ?? [];
		$qty_units        = $request->qty_units ?? [];
		$total_cs        = $request->total_cs ?? [];
		$transporter_code        = $request->transporter_code ?? [];
		$transporter_name        = $request->transporter_name ?? [];
		$truck_no        = $request->truck_no ?? [];
		$lr_no        = $request->lr_no ?? [];
		$lr_date        = $request->lr_date ?? [];
		$ewaybill_no        = $request->ewaybill_no ?? [];
		$truck_code        = $request->truck_code ?? [];
		$vehicle_type        = $request->vehicle_type ?? [];
		$custom        = $request->custom ?? [];
		$custom_1        = $request->custom_1 ?? [];
		$custom_2        = $request->custom_2 ?? [];
		$custom_3        = $request->custom_3 ?? [];
		$custom_4        = $request->custom_4 ?? [];

		$count = count($indent_id);
		$insertedCount = 0;

		DB::beginTransaction();
			  try {
            for ($i = 0; $i < $count; $i++) {

               
				$invoicechallandate = $invoice_challan_date[$i];
				
				$invoicechallan_date = Carbon::parse($invoicechallandate)->format('Y-m-d');
				
				$mfgdate = $mfg_date[$i];
				
				$mfgdatef = Carbon::parse($mfgdate)->format('Y-m-d');
				
				$expdate = $expiry_date[$i];
				$expirydate = Carbon::parse($expdate)->format('Y-m-d');
				
				$lrdate = $lr_date[$i];
				$lrdatef = Carbon::parse($lrdate)->format('Y-m-d');
				
				
				if(!empty($indent_id[$i]) && !empty($supplier_code[$i]))
				{
				
					 $supplier = Siteplant::where('plant_site_code', $supplier_code)->first();

							if (!$supplier) {

								return response()->json([
									'status' => false,
									'message' => 'Supplier not found'
								]);
							}
							
					$supplier_name =  $supplier;
					
					$data = [
						'indent_id' => $indent_id[$i] ?? null,
						'supplier_code' => $supplier_code[$i] ?? null,
						'supplier_name' => $supplier_name[$i] ?? null,
						'supplier_location' => $supplier_location[$i] ?? null,
						'po_no' => $po_no[$i] ?? null,
						'invoice_challan_no' => $invoice_challan_no[$i] ?? null,
						'invoice_challan_date' => $invoicechallan_date ?? null,
						'consignee_code' => $consignee_code[$i] ?? null,
						'consignee_name' => $consignee_name[$i] ?? null,
						'consignee_location' => $consignee_location[$i] ?? null,
						'm_code' => $m_code[$i] ?? null,
						'material_descriptions' => $material_descriptions[$i] ?? null,
						'batch_no' => $batch_no[$i] ?? null,						
						'mfg_date' => $mfgdatef ?? null,
						'expiry_date' =>  $expirydate ?? null,
						'qty_units' => $qty_units[$i] ?? null,
						'total_cs' => $total_cs[$i] ?? null,
						'transporter_code' => $transporter_code[$i] ?? null,
						'transporter_name' => $transporter_name[$i] ?? null,
						'truck_no' => $truck_no[$i] ?? null,
						'lr_no' => $lr_no[$i] ?? null,
						'lr_date' => lrdatef ?? null,
						'ewaybill_no' => $ewaybill_no[$i] ?? null,
						'truck_code' => $truck_code[$i] ?? null,
						'vehicle_type' => $vehicle_type[$i] ?? null,
						'custom' => $custom[$i] ?? null,
						'custom1' => $custom1[$i] ?? null,
						'custom2' => $custom2[$i] ?? null,
						'custom3' => $custom3[$i] ?? null,
						'custom4' => $custom4[$i] ?? null,
						
						'created_at' => $createddate,
						'created_by' => Auth::user()->id,
						'status' => '1'
					];
					DigiWim::create($data);
					
				}
            }

            DB::commit();
            return redirect()->back()->with('success', 'Data Updated successfully!');			
			
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
		

		
	}

	*/
   
	////delete items
	
	public function deleteItem(Request $request, int $id)
    {
        DB::transaction(function () use ($id) {

            $item = DigiWim::whereNull('deleted_at')
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
		

	
}
