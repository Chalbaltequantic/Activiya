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

use App\Models\Billdata;
use App\Models\Vendor;
use App\Models\Ratedata;
use App\Models\TruckMaster;
use App\Models\Siteplant;
use App\Models\Admin;
use App\Models\Tracking;
use Auth;


class BilldataController extends Controller
{
    //
	public function __construct()
    {
        $this->middleware('auth:admin');     
    }
	
	public function index(Request $request)
    {
        $title = 'Bill Data Upload';
        $pagetitle = $title.' Listing';
             
        return view('admin.billdata.index',compact(['pagetitle','title']));
    }
	
	public function billdatalist(Request $request)
    {
        $title = 'Bill Data Upload';
        $pagetitle = $title.' Listing';
       $user_role = Auth::user()->role_id;
		$data = $request->all();        
	    $billdatalist = Billdata::orderBy('created_at', 'desc')->get();       
        return view('admin.billdata.billdatalist',compact(['pagetitle','title','billdatalist','user_role']));
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
		
		$createddate = date('Y-m-d');

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
				$lr_cn_date = $row['M'];

				$lr_cndate = Carbon::parse($lr_cn_date)->format('Y-m-d');
				
				$a_amount = preg_replace("/,+/", "", $row['N']);
				
				$s5_consignor_short_name_location = Siteplant::where("plant_site_code", $row['B'])->first(["s5_d5_short_name"]);

				$d5_consignee_short_name_location = Siteplant::where("plant_site_code", $row['E'])->first(["s5_d5_short_name"]);
				
				/*
				custom field  will be used for mode 
				Custom1(no of cases)	Custom2(driver_number)	Custom3(truck_no)
				
				to get tracking data while uploading bill data
				*/
                $data = [
                    'consignor_name' => $row['A'] ?? null,
                    'consignor_code' => $row['B'] ?? null,
                    'consignor_location' => $row['C'] ?? null,
                    's5_consignor_short_name_and_location' => $s5_consignor_short_name_location->s5_d5_short_name ?? null,
                    'consignee_name' => $row['D'] ?? null,
                    'consignee_code' => $row['E'] ?? null,
                    'consignee_location' => $row['F'] ?? null,
                    'd5_consignor_short_name_and_location' => $d5_consignee_short_name_location->s5_d5_short_name ?? null,
                    'ref1' => $row['G'] ?? null,
                    'vendor_code' => $row['H'] ?? null,
                    'vendor_name' => $row['I'] ?? null,
                    't_code' => $row['J'] ?? null,
                    'truck_type' => $row['K'] ?? null,
                    'lr_no' => $row['L'] ?? null,
                    'lr_cn_date' => isset($row['M']) ? $lr_cndate : null,
                    'a_amount' => $a_amount ?? null,
                    'ref2' => $row['O'] ?? null,
                    'ref3' => $row['P'] ?? null,
                    'freight_type' => $row['Q'] ?? null,
                    'ap_status' => $row['R'] ?? null,
                    'custom' => $row['S'] ?? null,
                    'custom1' => $row['T'] ?? null,
                    'custom2' => $row['U'] ?? null,
                    'custom3' => $row['V'] ?? null,
                    'created_at' => $createddate,
                    'created_by' => Auth::user()->id,
                    'status' => '1'
                ];
                   // 'delivery_due_date' => $delivery_due_date,                   

				$data_tracking = [
				'indent_no' => $data['ref1'],
				'customer_po_no' => $data['ref2'],
				'origin' => $data['consignor_location'],
				'destination' => $data['consignee_location'] ,
				'vendor_name' => $data['vendor_name'],
				'vendor_code' => $data['vendor_code'],				
				'vehicle_type' => $data['truck_type'],
				'lr_no' => $data['lr_no'],								
				'cases' => $data['custom1'],
				'driver_number' => $data['custom2'],
				'truck_no' => $data['custom3'],				
				'dispatch_date' => $data['lr_cn_date'],
				'created_at' => $createddate,
				'created_by' => Auth::user()->id,
				'status' => '1'
			];
			
			/////get TAT and distance from site plant using consignor location, consignee location and mode(custom field) 
			
			$rate_master_tat_distance = Ratedata::select('tat', 'distance')
			->where('consignee_location', $data['consignee_location'])
			->where('consignor_location', $data['consignor_location'])
			->where('mode', $data['custom'])
			->first();
				
			$tat = $rate_master_tat_distance->tat ?? null;
			$distance = $rate_master_tat_distance->distance ?? null;
			
			$data_tracking['lead_time'] = $tat;
			$data_tracking['distance'] = $distance;
			
			$dispatchdate = Carbon::parse($data['lr_cn_date'])->format('Y-m-d');
				
			$data_tracking['delivery_due_date'] = date('Y-m-d', strtotime($dispatchdate . ' +' . $tat . ' days'));
			
		 
		 // Check vendor code
            $vendorExists = Vendor::where('vendor_code', $data['vendor_code'])->exists();
            if (!$vendorExists) {
                $errorRows[] = ['row' => $rowNumber, 'reason' => 'Vendor code not found'];
                continue;
            }

            // Check truck code
            $truckExists = TruckMaster::where('code', $data['t_code'])->exists();
            if (!$truckExists) {
                $errorRows[] = ['row' => $rowNumber, 'reason' => 'Truck code not found'];
                continue;
            }

            // Check consignor code
            $consignorExists = Siteplant::where('plant_site_code', $data['consignor_code'])->exists();
            if (!$consignorExists) {
                $errorRows[] = ['row' => $rowNumber, 'reason' => 'Consignor code not found'];
                continue;
            }

            // Check consignee code
            $consigneeExists = Siteplant::where('plant_site_code', $data['consignee_code'])->exists();
            if (!$consigneeExists) {
                $errorRows[] = ['row' => $rowNumber, 'reason' => 'Consignee code not found'];
                continue;
            }

            // Check rate master
            $rateRecord = Ratedata::where('consignor_code', $data['consignor_code'])
                ->where('consignee_code', $data['consignee_code'])
                ->where('vendor_code', $data['vendor_code'])
                ->where('t_code', $data['t_code'])
                ->first();

            if (!$rateRecord) {
                $errorRows[] = ['row' => $rowNumber, 'reason' => 'Rate master record not found'];
                continue;
            }

            // Check amount match
            if (floatval($rateRecord->a_amount) != floatval($data['a_amount'])) {
                $errorRows[] = ['row' => $rowNumber, 'reason' => 'Amount does not match rate master'];
                continue;
            }

            // ---- DUPLICATE CHECK ----
            $exists = Billdata::where('ref1', $data['ref1'])
                ->where('ref3', $data['ref3'])
                ->where('lr_no', $data['lr_no'])
                ->exists();

            if ($exists) {
                $errorRows[] = ['row' => $rowNumber, 'reason' => 'Duplicate bill entry'];
                continue;
            }

            // ---- INSERT ----
            $bill = Billdata::create($data);
			$insertedCount++;
			
				if ($bill) {
					 Tracking::create($data_tracking);
				}
			
            }

            DB::commit();
			
			 if ($insertedCount === 0) {
            // No data inserted
            return back()
                ->with([
                    'errorRows' => $errorRows,
                    'error' => 'Please correct the highlighted errors.',
                ]);
        }

        // Success, maybe partial insert
        return redirect()->back()->with([
            'success' => "$insertedCount rows inserted successfully.",
            'failedRows' => $errorRows
        ]);
			
			
           // return redirect()->back()->with('success', 'Excel imported successfully!');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
	
	public function getBilldataDetails($id)
	{
		$billdata = Billdata::find($id);
		$userid = auth()->user()->id; //get loggedin user id		
		return view('admin.billdata.editbilldata', compact('billdata'));
	}

	public function save_billdata(Request $request)
	{
		$validatedData = $request->validate(
			[
				'consignor_name' => 'required',
				'consignor_code' => 'required',
				'status' => 'required',
			],
			[
				'consignor_name.required' => 'Please enter title',
				'consignor_code.required' => 'Please enter title',
				'status.required' => 'Please Select status',
			]
		);
			$id = $request->id;
			
			$s5_consignor_short_name_location = Siteplant::where("plant_site_code", $request->consignor_code)->first(["s5_d5_short_name"]);

			$d5_consignee_short_name_location = Siteplant::where("plant_site_code", $request->consignee_code)->first(["s5_d5_short_name"]);
			
			Billdata::find($id)->update([
				'consignor_name' => $request->consignor_name,
				'consignor_code' => $request->consignor_code,
				'consignor_location' => $request->consignor_location,
				's5_consignor_short_name_and_location' => $s5_consignor_short_name_location->s5_d5_short_name,
				'consignee_name' => $request->consignee_name,
				'consignee_code' => $request->consignee_code,
				'consignee_location' => $request->consignee_location,
				'd5_consignor_short_name_and_location' => $d5_consignee_short_name_location->s5_d5_short_name,
				't_code' => $request->t_code,
				'truck_type' => $request->truck_type,
				'lr_no' => $request->lr_no,
				'lr_cn_date' => $request->lr_cn_date,
				'a_amount' => $request->a_amount,
				'ref2' => $request->ref2,
				'ref3' => $request->ref3,
				'freight_type' => $request->freight_type,
				'ap_status' => $request->ap_status,
				'custom' => $request->custom,
				'updated_at' => Carbon::now(),
				'status' => $request->status,
			]);
			return Redirect('/admin/freight-shipment-history')->with('success', 'DSata updated successfully!');
		
	}
	
	
	public function DeleteBillData($id)
	{
		$billdata = Billdata::find($id);		
		Billdata::find($id)->delete();
		return Redirect('/admin/freight-shipment-history')->with('success', 'Data deleted successfully!');
	}
	
	
	//manual Upload
	
	public function manualupload()
	{
		$userid = auth()->user()->id; //get loggedin user id		
		return view('admin.billdata.manualupload');
	}

	public function save_manual_billdata(Request $request)
	{
		
		$created_by = Auth::user()->id; 
		
		$createddate = date('Y-m-d');
		$consignor_name     = $request->input('consignor_name', []);
		
		//print_r($consignor_name); exit;
		$consignor_code     = $request->input('consignor_code', []);
		$consignor_location = $request->input('consignor_location', []);
		$s5_consignor_short_name_and_location = $request->input('s5_consignor_short_name_and_location', []);
		$consignee_name = $request->input('consignee_name', []);
		$consignee_code = $request->input('consignee_code', []);
		$consignee_location = $request->input('consignee_location', []);
		$d5_consignor_short_name_and_location = $request->input('d5_consignor_short_name_and_location', []);
		$ref1 = $request->input('ref1', []);
		$vendor_code = $request->input('vendor_code', []);
		$vendor_name = $request->input('vendor_name', []);
		$t_code = $request->input('t_code', []);
		$truck_type = $request->input('truck_type', []);
		$lr_no = $request->input('lr_no', []);
		$lr_cn_date = $request->input('lr_cn_date', []);
		$amount = $request->input('a_amount', []);
		$ref2 = $request->input('ref2', []);
		$ref3 = $request->input('ref3', []);
		$freight_type = $request->input('freight_type', []);
		$ap_status = $request->input('ap_status', []);
		$custom = $request->input('custom', []);
		$cases = $request->input('custom1', []);
		$driver_number = $request->input('custom2', []);
		$truck_no = $request->input('custom3', []);
			

		$count = count($consignor_name);
		
		$errorRows = [];
		$insertedCount = 0;
		$validData = [];

        DB::beginTransaction();
        try {
            for ($i = 0; $i < $count; $i++) {

                $rowNumber = $i + 1;
				$lrcndate = $lr_cn_date[$i];

				$lr_cndate = Carbon::parse($lrcndate)->format('Y-m-d');
				
				$a_amount = preg_replace("/,+/", "", $amount[$i]);
				if(!empty($consignor_name[$i]))
				{
					$s5_consignor_short_name_location = Siteplant::where("plant_site_code", $consignor_code[$i])->first(["s5_d5_short_name"]);

					$d5_consignee_short_name_location = Siteplant::where("plant_site_code", $consignee_code[$i])->first(["s5_d5_short_name"]);
					
					
					$data = [
						'consignor_name' => $consignor_name[$i] ?? null,
						'consignor_code' => $consignor_code[$i] ?? null,
						'consignor_location' => $consignor_location[$i] ?? null,
						's5_consignor_short_name_and_location' => $s5_consignor_short_name_location->s5_d5_short_name ?? null,
						'consignee_name' => $consignee_name[$i] ?? null,
						'consignee_code' => $consignee_code[$i] ?? null,
						'consignee_location' => $consignee_location[$i] ?? null,
						'd5_consignor_short_name_and_location' => $d5_consignee_short_name_location->s5_d5_short_name ?? null,
						'ref1' => $ref1[$i] ?? null,
						'vendor_code' => $vendor_code[$i] ?? null,
						'vendor_name' => $vendor_name[$i] ?? null,
						't_code' => $t_code[$i] ?? null,
						'truck_type' => $truck_type[$i] ?? null,
						'lr_no' => $lr_no[$i] ?? null,
						'lr_cn_date' =>  $lr_cndate ?? null,
						'a_amount' => $a_amount ?? null,
						'ref2' => $ref2[$i] ?? null,
						'ref3' => $ref3[$i] ?? null,
						'freight_type' => $freight_type[$i] ?? null,
						'ap_status' => $ap_status[$i] ?? null,
						'custom' => $custom[$i] ?? null,
						'custom1' => $cases[$i] ?? null,
						'custom2' => $driver_number[$i] ?? null,
						'custom3' => $custom[$i] ?? null,
						'created_at' => $createddate,
						'created_by' => Auth::user()->id,
						'status' => '1'
					];
					
					$data_tracking = [
						'indent_no' => $data['ref1'],
						'customer_po_no' => $data['ref2'],
						'origin' => $data['consignor_location'],
						'destination' => $data['consignee_location'] ,
						'vendor_name' => $data['vendor_name'],
						'vendor_code' => $data['vendor_code'],				
						'vehicle_type' => $data['truck_type'],
						'lr_no' => $data['lr_no'],								
						'cases' => $data['custom1'],
						'driver_number' => $data['custom2'],
						'truck_no' => $data['custom3'],				
						'dispatch_date' => $data['lr_cn_date'],
						'created_at' => $createddate,
						'created_by' => Auth::user()->id,
						'status' => '1'
					];
			
			/////get TAT and distance from site plant using consignor location, consignee location and mode(custom field) 
			
			$rate_master_tat_distance = Ratedata::select('tat', 'distance')
			->where('consignee_location', $data['consignee_location'])
			->where('consignor_location', $data['consignor_location'])
			->where('mode', $data['custom'])
			->first();
				
			$tat = $rate_master_tat_distance->tat ?? null;
			$distance = $rate_master_tat_distance->distance ?? null;
			
			$data_tracking['lead_time'] = $tat;
			$data_tracking['distance'] = $distance;
			
			$dispatchdate = Carbon::parse($data['lr_cn_date'])->format('Y-m-d');
				
			$data_tracking['delivery_due_date'] = date('Y-m-d', strtotime($dispatchdate . ' +' . $tat . ' days'));
			

						// Check vendor code
					$vendorExists = Vendor::where('vendor_code', $data['vendor_code'])->exists();
					if (!$vendorExists) {
						$errorRows[] = ['row' => $rowNumber, 'reason' => 'Vendor code not found'];
						continue;
					}

					// Check truck code
					$truckExists = TruckMaster::where('code', $data['t_code'])->exists();
					if (!$truckExists) {
						$errorRows[] = ['row' => $rowNumber, 'reason' => 'Truck code not found'];
						continue;
					}

					// Check consignor code
					$consignorExists = Siteplant::where('plant_site_code', $data['consignor_code'])->exists();
					if (!$consignorExists) {
						$errorRows[] = ['row' => $rowNumber, 'reason' => 'Consignor code not found'];
						continue;
					}

					// Check consignee code
					$consigneeExists = Siteplant::where('plant_site_code', $data['consignee_code'])->exists();
					if (!$consigneeExists) {
						$errorRows[] = ['row' => $rowNumber, 'reason' => 'Consignee code not found'];
						continue;
					}

					// Check rate master
					$rateRecord = Ratedata::where('consignor_code', $data['consignor_code'])
						->where('consignee_code', $data['consignee_code'])
						->where('vendor_code', $data['vendor_code'])
						->where('t_code', $data['t_code'])
						->first();

					if (!$rateRecord) {
						$errorRows[] = ['row' => $rowNumber, 'reason' => 'Rate master record not found'];
						continue;
					}

					// Check amount match
					if (floatval($rateRecord->a_amount) != floatval($data['a_amount'])) {
						$errorRows[] = ['row' => $rowNumber, 'reason' => 'Amount does not match rate master'];
						continue;
					}
                // Check for duplicate using ref1, ref3, lr_no
                $exists = Billdata::where('ref1', $data['ref1'])
                    ->where('ref3', $data['ref3'])
                    ->where('lr_no', $data['lr_no'])
                    ->exists();
					
				
				if ($exists) {
					$errorRows[] = ['row' => $rowNumber, 'reason' => 'Duplicate bill entry'];
					continue;
				}
					
					  /*  if ($exists) {
							$duplicateRows[] = [
								'Row' => $data['ref1'],
								'ref1' => $ref1,
								'ref3' => $data['ref3'],
								'lr_no' => $data['lr_no'],
							];
							continue;
						}	*/

					// ---- INSERT ----
				$bill = Billdata::create($data);
					$insertedCount++;
					if($bill) 
					{
						Tracking::create($data_tracking);
					}
				}
            }
			//print_r($errorRows); exit;
            DB::commit();
			
			if ($insertedCount === 0) {
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
           // return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
			return redirect()->back()->with('error', 'Error: Something went wrong.');
        }
		
	}
	
	/// Freight details update
	public function bill_data_freight_index()
    { 
        $title = 'Bill Data freight details Upload';
        $pagetitle = $title.' Listing';
		$created_by = Auth::user()->role_id;
		
       if (Auth::user()->role_id =='5')  ////Account1
	   { 
        // Filter records for account1
		
			$usr_id = Auth::user()->id;
			$vendorCode = Auth::user()->vendor_code;
			$entries = Billdata::select([
				'id', 's5_consignor_short_name_and_location', 'd5_consignor_short_name_and_location', 
				'ref1', 'truck_type', 'lr_no', 'lr_cn_date',
				'ref2', 'ref3', 'a_amount', 'vendor_code'
			])
			->whereNull('freight_invoice_no')
			->whereNull('freight_invoice_date')
			->whereNull('freight_amount')
			->where('vendor_code', $vendorCode)
			//->whereDate('created_at', Carbon::today())
			->get();
			
			$updatedentries = Billdata::select([
				'id', 's5_consignor_short_name_and_location', 'd5_consignor_short_name_and_location', 
				'ref1', 'truck_type', 'lr_no', 'lr_cn_date',
				'ref2', 'ref3', 'freight_invoice_no', 'freight_invoice_date', 'freight_amount',
				'freight_invoice_file', 'pod_file', 'approval_file', 'validated_status','submit', 'f_return', 'validation_remark','vendor_code'
			])
			->where('freight_invoice_no','<>','')
			->whereNotNull('freight_invoice_date')
			->where('freight_amount', '<>','')
			->where('vendor_code', $vendorCode)
			->get();
		} 
		else 
		{        
			$entries = Billdata::orderBy('updated_at', 'desc')->get(); 
			$updatedentries = Billdata::select([
				'id', 's5_consignor_short_name_and_location', 'd5_consignor_short_name_and_location', 
				'ref1', 'truck_type', 'lr_no', 'lr_cn_date',
				'ref1', 'ref2', 'freight_invoice_no', 'freight_invoice_date', 'freight_amount',
				'freight_invoice_file', 'pod_file', 'approval_file', 'validated_status','submit', 'f_return', 'validation_remark', 'vendor_code'
			])
			->where('freight_invoice_no','<>','')
			->where('freight_invoice_date', '<>','')
			->where('freight_amount', '<>','')
			//->whereDate('created_at', Carbon::today())
			->get();
		}  
		
        return view('admin.billdata.freightupdate',compact(['pagetitle','title','entries','updatedentries']));
    }
	
	public function updateMultiple(Request $request)
	{
		$amountMismatches = [];
        $fileErrors = [];
        $saveErrors = [];


		foreach ($request->data as $row) 
		{
			$entry = Billdata::find($row['id']);
			if (!$entry) {
                continue; // skip if entry not found
            }

			// Normalize entered amount (remove commas)
			//$enteredAmount = (int)str_replace(',', '', $row['amount']);
			$enteredAmount = (int)preg_replace("/,+/", "", $row['freight_amount']);
			$expectedAmount = (int)$entry->a_amount;
			
			$freight_inv_dt = $row['freight_invoice_date'];
			$freightinv_date = Carbon::parse($freight_inv_dt)->format('Y-m-d');
			
			$lr_no = $entry->lr_no;
			
			 if ($enteredAmount !== $expectedAmount) {
                $amountMismatches[] = [
                    'id' => $entry->id,
                    'order_ref_no' => $entry->ref1 ?? 'N/A',
                    'entered' => $row['freight_amount'],
                    'expected' => number_format($expectedAmount)
                ];
                continue;
            }
			
			
			$createddate = date('Y-m-d');
			 try{
					$entry->freight_invoice_no = $row['freight_invoice_no'];
					$entry->freight_invoice_date = $freightinv_date;
					$entry->freight_amount = $enteredAmount;
					$entry->freight_info_updated_by = Auth::user()->id;				
					$entry->freight_info_updated_at = $createddate;				
					$entry->save();
				} 
				catch (\Exception $e) 
				{

					Log::error("Save failed for LR No: {$lr_no} — Error: " . $e->getMessage());
					
					$saveErrors[] = "Unexpected error while saving data for LR No: {$lr_no}";
				}
		} //for loop 
		return back()->with([
            'mismatches' => $amountMismatches,
            'fileErrors' => $fileErrors,
            'saveErrors' => $saveErrors,
			]);
	}
	
	///////////////////Freight data information validate 
	
	public function freight_info_validate_index()
    {
        $title = 'Bill Data freight details Validate';
        $pagetitle = $title.' Listing';
		$created_by = Auth::user()->role_id;
		$entries = Billdata::where('freight_invoice_no','<>','')
						->where('freight_invoice_date', '<>','')
						->where('freight_amount', '<>','')
						//->whereNull('validated_status')
					   ->get();
					   
		$updatedentries[] = ''; 
      // if (Auth::user()->role_id === '4' || Auth::user()->role_id === '1')  ////Account
	 //  { 
        
		$updatedentries = Billdata::select([
				'id', 's5_consignor_short_name_and_location', 'd5_consignor_short_name_and_location', 
				'ref1', 'truck_type', 'lr_no', 'lr_cn_date',
				'ref1', 'ref2', 'freight_invoice_no', 'freight_invoice_date', 'freight_amount',
				'freight_invoice_file', 'pod_file', 'approval_file', 'validated_status','submit', 'f_return', 'validation_remark'
			])
			->where('freight_invoice_no','<>','')
			->where('freight_invoice_date', '<>','')
			->where('freight_amount', '<>','')
			->get();
		//} 
		
		//print_r($updatedentries);
        return view('admin.billdata.freight_detail_validate',compact(['pagetitle','title','entries','updatedentries']));
    }
	
	public function validateAjax(Request $request)
	{
		$results = [];

		foreach ($request->rows as $row) {
			$entry = Billdata::find($row['id']);
			if (!$entry) continue;

			$valid = true;

			if ((float) preg_replace('/[^0-9.]/', '', $entry->a_amount) !== (float) preg_replace('/[^0-9.]/', '', $entry['freight_amount'])) {
				$valid = false;
			}

			if (empty($entry->freight_invoice_file) || empty($entry->pod_file)) {
				$valid = false;
			}

			if ($entry->freight_type === 'ADHOC' && empty($entry->approval_file)) {
				$valid = false;
			}

			$results[] = [
				'id' => $entry->id,
				'valid' => $valid,
			];
		}

		return response()->json($results);
	}
	
	public function storeValidatedData(Request $request)
	{
		//print_r($request); exit;
		
		$validatedIds = $request->input('validated_ids', []);
		//print_r($validatedIds); exit;
		
		$submittedIds = $request->input('submitted_ids', []);
		$returnedIds = $request->input('returned_ids', []);
		$remarks = $request->input('remark', []);



		foreach ($validatedIds as $index => $id) {
			$entry = Billdata::find($id);
			if (!$entry) continue;
			
			$vendor_code = 	$entry->vendor_code;
			$vendor_name = 	$entry->vendor_name;
			$source_name = 	$entry->s5_consignor_short_name_and_location;
			$destination_name = 	$entry->d5_consignor_short_name_and_location;
			$truck_type = 	$entry->truck_type;
			$ref1 = 	$entry->ref1;
			$ref2 = 	$entry->ref2;
			$ref3 = 	$entry->ref3;
			$lr_no =	$entry->lr_no;
			$amount = 	$entry->a_amount;
			$destination_name = 	$entry->vendor_name;
			$freight_info_updated_at = $entry->freight_info_updated_at;
			$freight_invoice_no = 	$entry->freight_invoice_no;
			
			$freight_invoice_file = !empty($entry->freight_invoice_file)?$entry->freight_invoice_file:'';
			$pod_file = !empty($entry->pod_file)?$entry->pod_file:'';
			$approval_file = !empty($entry->approval_file)?$entry->approval_file:'';
			
			 $files = [];
			 if(!empty($freight_invoice_file))
			 {
				 $files[] = public_path($freight_invoice_file);
			 }
			 if(!empty($pod_file))
			 {
				 $files[] = public_path($pod_file);
			 }
			 if(!empty($approval_file))
			 {
				 $files[] = public_path($approval_file);
			 }
			 

			if (in_array($id, $submittedIds)) {
				$entry->validated_status = 'submitted';
				$entry->submit = 1;
				$entry->f_return = 0;
				
				//send email to user on alpply success//////////////////////
					$to_name = 'Roshan Jha';
					$to_email = 'roshan.scm@gmail.com';
					
					$subject = "Vendor name : $vendor_name & freight invoice no : $freight_invoice_no";
					///////////////////email content
					$body = '<div class="table-responsive-fixed border rounded shadow-sm bg-white consign-data-table">
						 
						<table class="table table-bordered border-dark table-hover">
							<thead>
							<tr>
								<th style="background: #fce4d6; color: #0070c0;" class="col-width">Vendor Code</th>
								<th style="background: #fce4d6; color: #0070c0;" class="col-width">Vendor Name</th>
								<th style="background: #fce4d6; color: #0070c0;" class="col-width">Source Name
								</th>
								<th style="background: #fce4d6; color: #0070c0;" class="col-width">Destination Name</th>						  
								<th style="background: #fce4d6; color: #0070c0;" class="col-width">Truck Type</th>
								<th style="background: #fce4d6; color: #0070c0;" class="col-width">Ref1</th>
								<th style="background: #fce4d6; color: #0070c0;" class="col-width">Ref 2</th>
								<th style="background: #fce4d6; color: #0070c0;" class="col-width">Ref 3</th>
								<th style="background: #fce4d6; color: #0070c0;" class="col-width">LR/CN No.</th>
								<th style="background: #fce4d6; color: #0070c0;" class="col-width">Invoice No.</th>
								<th style="background: #fce4d6; color: #0070c0;" class="col-width">Amount</th>
								<th style="background: #fce4d6; color: #0070c0;" class="col-width">Bill to<br> GST No</th>
								<th style="background: #fce4d6; color: #0070c0;" class="col-width">Invoice Receive<br>Date from Vendor</th>
							</tr>
						  </thead>
						<tbody>
										  
						<tr>
							<td>'.$vendor_code.'</td>
							<td>'.$vendor_name.'</td>
							<td>'.$source_name.'</td>
							<td>'.$destination_name.'</td>
							<td>'.$truck_type.'</td>
							<td>'.$ref1.'</td>
							<td>'.$ref2.'</td>
							<td>'.$ref3.'</td>
							<td>'.$lr_no.'</td>
							<td>'.$freight_invoice_no.'</td>
							<td>'.$amount.'</td>
							<td></td>
							<td>'.$freight_info_updated_at.'</td>	
						</tr>
						
					   </tbody>
					</table>
					</div>
					';
				////// end email content	
					/*$data = array('name'=>$to_name, "body" => $body );
					
					//print_r($data);
					
					Mail::send('mail.freight_info_mail', $data, function($message) use ($to_email, $subject, $files) {
								$message->to($to_email)->subject($subject);
								$message->from(env("MAIL_USERNAME"),'Activiya.com');
					
					foreach ($files as $file){
								$message->attach($file);
					}
					});	 */
					
					$admins = Admin::whereIn('role_id', [4, 6])->where('status','1')->get();

					foreach ($admins as $admin) {
						$to_email = $admin->email;
						$to_name = $admin->name; // assuming 'name' column exists
						$data = [
							'name' => $to_name,
							'body' => $body, // assuming $body is already defined
						];

						
						Mail::send('mail.freight_info_mail', $data, function($message) use ($to_email, $subject, $files) {
							$message->to($to_email)->subject($subject);
							$message->from(env("MAIL_USERNAME"), 'Activiya.com');

							foreach ($files as $file) {
								$message->attach($file);
							}
						});
					}
				
			} 
			elseif (in_array($id, $returnedIds)) {
				$entry->validated_status = 'returned';
				$entry->submit = 0;
				$entry->f_return = 1;
			}
			
			$entry->validation_remark = $remarks[$index] ?? '';
			$entry->save();
		}

		return redirect()->back()->with('success', 'Records updated successfully.');
	}

	public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:20480',
            'lr_no' => 'required|string',
            'type' => 'required|in:invoice,pod,approval'
        ]);

		 $id = $request->id;

		$bill = Billdata::find($id);
		if (!$bill) {
			return response()->json(['status' => 'not_found'], 404);
		}
		
        $lr_no = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $request->lr_no); // Sanitize
        $type = $request->type;

        $uploadPath = public_path("uploads/{$type}");
        $file = $request->file('file');
        $originalName = $file->getClientOriginalName();

        $filename = $lr_no . '_' . time() . '_' . $originalName;
        $file->move($uploadPath, $filename);
		
		$fieldMap = [
        'invoice' => 'freight_invoice_file',
        'pod' => 'pod_file',
        'approval' => 'approval_file',
		];
		$field = $fieldMap[$type];

		// Delete old file if exists
		if (!empty($bill->$field)) {
			$oldPath = public_path("uploads/{$type}/" . $bill->$field);
			if (file_exists($oldPath)) {
				unlink($oldPath);
			}
		}

		$bill->$field = "uploads/{$type}/".$filename;
		$bill->save();


        return response()->json(['status' => 'success', 'filename' => $filename]);
    }

    public function delete(Request $request)
    {
        $request->validate([
			'id'=>'required',
            'filename' => 'required',
            'type' => 'required|in:invoice,pod,approval'
        ]);
		

		$type = $request->type;
		$id = $request->id;

		$bill = Billdata::find($id);
		
		if (!$bill) {
			return response()->json(['status' => 'not_found'], 404);
		}
		
		 $fieldMap = [
        'invoice' => 'freight_invoice_file',
        'pod' => 'pod_file',
        'approval' => 'approval_file',
		];
		$field = $fieldMap[$type];

        $filePath = public_path($request->filename);

        if (file_exists($filePath)) {
			
            unlink($filePath);
			$bill->$field = null;
			$bill->save();
            return response()->json(['status' => 'deleted']);
        }

       // return response()->json(['status' => 'not_found'], 404);
    }

}
