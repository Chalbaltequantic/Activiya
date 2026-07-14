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

use App\Jobs\SendValidatedFreightMailJob;
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
			
			$today = date('Y-m-d');
			
            $rateRecord = Ratedata::where('consignor_code', $data['consignor_code'])
                ->where('consignee_code', $data['consignee_code'])
                ->where('vendor_code', $data['vendor_code'])
                ->where('t_code', $data['t_code'])
				 ->whereDate('validity_start', '<=', $data['lr_cn_date'])
				->whereDate('validity_end', '>=', $data['lr_cn_date'])
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
           /* $exists = Billdata::where('ref1', $data['ref1'])
                ->where('ref3', $data['ref3'])
                ->where('lr_no', $data['lr_no'])
                ->exists();

            if ($exists) {
                $errorRows[] = ['row' => $rowNumber, 'reason' => 'Duplicate bill entry'];
                continue;
            }*/
			
			  // Check for duplicate using ref1, ref3, lr_no
            $cleanRef1 = !empty($data['ref1']) ? (int)$data['ref1'] : null;
			$cleanRef3 = !empty($data['ref3']) ? (int)$data['ref3'] : null;
			
			if ($cleanRef1 !== null) {
				$existsref1 = Billdata::where('ref1', $cleanRef1)->exists();
				if ($existsref1) {
					$errorRows[] = [
						'row' => $rowNumber,
						'reason' => 'Duplicate Ref1 entry: ' . $cleanRef1
					];
					continue;
				}
			}

				if ($cleanRef3 !== null) {
					$existsref3 = Billdata::where('ref3', $cleanRef3)->exists();
					if ($existsref3) {
						$errorRows[] = [
							'row' => $rowNumber,
							'reason' => 'Duplicate Ref3 entry: ' . $cleanRef3
						];
						continue;
					}
				}
				
				$cleanLrNo = trim((string)($data['lr_no'] ?? ''));
				if ($cleanLrNo !== '') {
					$existslrno = Billdata::where('lr_no', $cleanLrNo)->exists();
					if ($existslrno) {
						$errorRows[] = [
							'row' => $rowNumber,
							'reason' => 'Duplicate LR NO entry: ' . $cleanLrNo
						];
						continue;
					}
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
				if(!empty($consignor_name[$i]) && !empty($consignee_name[$i]) && !empty($consignee_code[$i]) && !empty($lr_no))
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
					if(!empty($data['vendor_code']))
					{
						$vendorExists = Vendor::where('vendor_code', $data['vendor_code'])->exists();
						if (!$vendorExists) {
							$errorRows[] = ['row' => $rowNumber, 'reason' => 'Vendor code not found'];
							continue;
						}
					}

					// Check truck code
					if(!empty($data['t_code']))
					{
						$truckExists = TruckMaster::where('code', $data['t_code'])->exists();
						if (!$truckExists) {
							$errorRows[] = ['row' => $rowNumber, 'reason' => 'Truck code not found'];
							continue;
						}
					}

					// Check consignor code
					if(!empty($data['consignor_code']))
					{
						$consignorExists = Siteplant::where('plant_site_code', $data['consignor_code'])->exists();
						if (!$consignorExists) {
							$errorRows[] = ['row' => $rowNumber, 'reason' => 'Consignor code not found'];
							continue;
						}
					}

					// Check consignee code
					if(!empty($data['consignee_code']))
					{	
						$consigneeExists = Siteplant::where('plant_site_code', $data['consignee_code'])->exists();
						if (!$consigneeExists) {
							$errorRows[] = ['row' => $rowNumber, 'reason' => 'Consignee code not found'];
							continue;
						}
					}

					// Check rate master
					if(!empty($data['consignor_code']))
					{
						//$today = date('Y-m-d');
						$rateRecord = Ratedata::where('consignor_code', $data['consignor_code'])
							->where('consignee_code', $data['consignee_code'])
							->where('vendor_code', $data['vendor_code'])
							->where('t_code', $data['t_code'])
							->whereDate('validity_start', '<=', $lr_cndate)
							->whereDate('validity_end', '>=', $lr_cndate)
							->first();

						if (!$rateRecord) {
							$errorRows[] = ['row' => $rowNumber, 'reason' => 'Rate master record not found'];
							continue;
						}
					}

					// Check amount match
					if(!empty($data['a_amount']))
					{	
						if (floatval($rateRecord->a_amount) != floatval($data['a_amount'])) {
							$errorRows[] = ['row' => $rowNumber, 'reason' => 'Amount does not match rate master'];
							continue;
						}
					}
                // Check for duplicate using ref1, ref3, lr_no
				
				$cleanRef1 = !empty($data['ref1']) ? (int)$data['ref1'] : null;
				$cleanRef3 = !empty($data['ref3']) ? (int)$data['ref3'] : null;
				
				
				
                if ($cleanRef1 !== null) {
					$existsref1 = Billdata::where('ref1', $cleanRef1)->exists();
					if ($existsref1) {
						$errorRows[] = [
							'row' => $rowNumber,
							'reason' => 'Duplicate Ref1 entry: ' . $cleanRef1
						];
						continue;
					}
				}

				if ($cleanRef3 !== null) {
					$existsref3 = Billdata::where('ref3', $cleanRef3)->exists();
					if ($existsref3) {
						$errorRows[] = [
							'row' => $rowNumber,
							'reason' => 'Duplicate Ref3 entry: ' . $cleanRef3
						];
						continue;
					}
				}
				
				$cleanLrNo = trim((string)($data['lr_no'] ?? ''));
				if ($cleanLrNo !== '') {
					$existslrno = Billdata::where('lr_no', $cleanLrNo)->exists();
					if ($existslrno) {
						$errorRows[] = [
							'row' => $rowNumber,
							'reason' => 'Duplicate LR NO entry: ' . $cleanLrNo
						];
						continue;
					}
				}
					
				
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
		
       if (Auth::user()->role_id =='5' || Auth::user()->role_id =='20')  ////Account1
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
		$successCount = 0;


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
					 $successCount++;
				} 
				catch (\Exception $e) 
				{

					Log::error("Save failed for LR No: {$lr_no} — Error: " . $e->getMessage());
					
					$saveErrors[] = "Unexpected error while saving data for LR No: {$lr_no}";
				}
		} //for loop 
		
		 // Prepare messages
			$successMessage = null;

			if ($successCount > 0) {
				$successMessage = "{$successCount} record(s) updated successfully.";
			}

			if ($successCount === 0 && empty($amountMismatches) && empty($saveErrors)) {
				$successMessage = "No changes were made.";
			}
		 return redirect()->back()->with([
        'success' => $successMessage,
            'mismatches' => $amountMismatches,
            'fileErrors' => $fileErrors,
            'saveErrors' => $saveErrors,
			]);
	}
	
	///////////////////Freight data information validate 
	

	/*
	
	public function freight_info_validate_index()
	{
		$title = 'Bill Data freight details Validate';
		$pagetitle = $title.' Listing';
		$created_by = Auth::user()->role_id;

		$entries = Billdata::from('bill_data_upload as b')
			->leftJoin('rate_master as rm', function ($join) {
				$join->on('rm.consignor_code', '=', 'b.consignor_code')
					 ->on('rm.consignee_code', '=', 'b.consignee_code')
					 ->on('rm.vendor_code', '=', 'b.vendor_code')
					 ->on('rm.t_code', '=', 'b.t_code');
			})
			->select([
				'b.*',
				'rm.custom5 as rate_custom5',
			])
			->whereNotNull('b.freight_invoice_no')
			->where('b.freight_invoice_no', '!=', '')
			->where(function ($q) {
				$q->whereNull('b.submit')
				  ->orWhere('b.submit', 0);
			})
			->where(function ($q) {
				$q->whereNull('b.f_return')
				  ->orWhere('b.f_return', 0);
			})
			->orderBy('b.vendor_name', 'asc')
			->orderBy('b.created_at', 'desc')
			->get();


		$updatedentries = Billdata::from('bill_data_upload as b')
			->leftJoin('rate_master as rm', function ($join) {
				$join->on('rm.consignor_code', '=', 'b.consignor_code')
					 ->on('rm.consignee_code', '=', 'b.consignee_code')
					 ->on('rm.vendor_code', '=', 'b.vendor_code')
					 ->on('rm.t_code', '=', 'b.t_code');
			})
			->select([
				'b.id',
				'b.s5_consignor_short_name_and_location',
				'b.d5_consignor_short_name_and_location',
				'b.ref1',
				'b.truck_type',
				'b.lr_no',
				'b.lr_cn_date',
				'b.ref2',
				'b.freight_invoice_no',
				'b.freight_invoice_date',
				'b.freight_amount',
				'b.freight_invoice_file',
				'b.pod_file',
				'b.approval_file',
				'b.validated_status',
				'b.submit',
				'b.f_return',
				'b.validation_remark',
				'b.vendor_name',
				'rm.custom5 as rate_custom5',
			])
			->where('b.freight_invoice_no', '!=', '')
			->whereNotNull('b.freight_invoice_date')
			->whereNotNull('b.freight_amount')
			->where(function ($q) {
				$q->where('b.submit', 1)
				  ->orWhere('b.f_return', 1);
			})
			->orderBy('b.vendor_name', 'asc')
			->orderBy('b.created_at', 'desc')
			->get();

		return view(
			'admin.billdata.freight_detail_validate',
			compact('pagetitle', 'title', 'entries', 'updatedentries')
		);
	}*/
	
	public function freight_info_validate_index()
	{
		$title = 'Bill Data freight details Validate';
		$pagetitle = $title.' Listing';
		$created_by = Auth::user()->role_id;

		/*
		|--------------------------------------------------------------------------
		| Pending validation entries
		|--------------------------------------------------------------------------
		*/
		$entries = Billdata::from('bill_data_upload as b')
			->leftJoin('rate_master as rm', function ($join) {
				$join->on('rm.id', '=', DB::raw("
					(
						SELECT rm2.id
						FROM rate_master rm2
						WHERE rm2.consignor_code = b.consignor_code
						  AND rm2.consignee_code = b.consignee_code
						  AND rm2.vendor_code = b.vendor_code
						  AND rm2.t_code = b.t_code
						  AND DATE(rm2.validity_start) <= DATE(b.lr_cn_date)
						  AND DATE(rm2.validity_end) >= DATE(b.lr_cn_date)
						ORDER BY rm2.validity_start DESC, rm2.id DESC
						LIMIT 1
					)
				"));
			})
			->select([
				'b.*',
				'rm.custom5 as rate_custom5',
			])
			->whereNotNull('b.freight_invoice_no')
			->where('b.freight_invoice_no', '!=', '')
			->where(function ($q) {
				$q->whereNull('b.submit')
				  ->orWhere('b.submit', 0);
			})
			->where(function ($q) {
				$q->whereNull('b.f_return')
				  ->orWhere('b.f_return', 0);
			})
			->orderBy('b.vendor_name', 'asc')
			->orderBy('b.created_at', 'desc')
			->get();


		/*
		|--------------------------------------------------------------------------
		| Already submitted / returned entries
		|--------------------------------------------------------------------------
		*/
		$updatedentries = Billdata::from('bill_data_upload as b')
			->leftJoin('rate_master as rm', function ($join) {
				$join->on('rm.id', '=', DB::raw("
					(
						SELECT rm2.id
						FROM rate_master rm2
						WHERE rm2.consignor_code = b.consignor_code
						  AND rm2.consignee_code = b.consignee_code
						  AND rm2.vendor_code = b.vendor_code
						  AND rm2.t_code = b.t_code
						  AND DATE(rm2.validity_start) <= DATE(b.lr_cn_date)
						  AND DATE(rm2.validity_end) >= DATE(b.lr_cn_date)
						ORDER BY rm2.validity_start DESC, rm2.id DESC
						LIMIT 1
					)
				"));
			})
			->select([
				'b.id',
				'b.s5_consignor_short_name_and_location',
				'b.d5_consignor_short_name_and_location',
				'b.ref1',
				'b.truck_type',
				'b.lr_no',
				'b.lr_cn_date',
				'b.ref2',
				'b.freight_invoice_no',
				'b.freight_invoice_date',
				'b.freight_amount',
				'b.freight_invoice_file',
				'b.pod_file',
				'b.approval_file',
				'b.validated_status',
				'b.submit',
				'b.f_return',
				'b.validation_remark',
				'b.vendor_name',
				'rm.custom5 as rate_custom5',
			])
			->whereNotNull('b.freight_invoice_no')
			->where('b.freight_invoice_no', '!=', '')
			->whereNotNull('b.freight_invoice_date')
			->whereNotNull('b.freight_amount')
			->where(function ($q) {
				$q->where('b.submit', 1)
				  ->orWhere('b.f_return', 1);
			})
			->orderBy('b.vendor_name', 'asc')
			->orderBy('b.created_at', 'desc')
			->get();

		return view(
			'admin.billdata.freight_detail_validate',
			compact('pagetitle', 'title', 'entries', 'updatedentries')
		);
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
				//$valid = false;
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
		$validatedIds = $request->input('validated_ids', []);
		$submittedIds = $request->input('submitted_ids', []);
		$returnedIds = $request->input('returned_ids', []);
		$remarks = $request->input('remark', []);
		
		$returned_at = date('Y-m-d H:i:s');
		
		if (empty($validatedIds)) {

			return redirect()->back()->with(
				'error',
				'No records selected.'
			);
		}

		DB::beginTransaction();

		try {

			foreach ($validatedIds as $index => $id) {

				$entry = Billdata::find($id);

				if (!$entry) {
					continue;
				}

				/*
				|--------------------------------------------------------------------------
				| SUBMITTED
				|--------------------------------------------------------------------------
				*/

				if (in_array($id, $submittedIds)) {

					$entry->validated_status = 'submitted';
					$entry->submit = 1;
					$entry->f_return = 0;

					/*
					|--------------------------------------------------------------------------
					| SEND MAIL IN BACKGROUND
					|--------------------------------------------------------------------------
					*/

					SendValidatedFreightMailJob::dispatch($entry->id);
				}

				/*
				|--------------------------------------------------------------------------
				| RETURNED
				|--------------------------------------------------------------------------
				*/

				elseif (in_array($id, $returnedIds)) {

					$entry->validated_status = 'returned';
					$entry->submit = 0;
					$entry->f_return = 1;
					$entry->returned_at  = $returned_at;
				}

				$entry->validation_remark = $remarks[$index] ?? '';

				$entry->save();
			}

			DB::commit();

			return redirect()->back()->with(
				'success',
				'Records updated successfully. Emails are processing in background.'
			);

		} catch (\Exception $e) {

			DB::rollBack();

			return redirect()->back()->with(
				'error',
				$e->getMessage()
			);
		}
	}
	
	//Update ReturnedFreight//////
	
	public function updateReturnedFreightAjax(Request $request)
	{
		$request->validate([
			'id'                   => 'required|exists:bill_data_upload,id',
			'freight_invoice_no'   => 'required',
			'freight_invoice_date' => 'required|date',
			'freight_amount'       => 'required|numeric|min:1',

			'freight_invoice_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
			'pod_file'             => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',

		],[
			'freight_invoice_file.max' => 'Invoice file size cannot exceed 2 MB.',
			'freight_invoice_file.mimes' => 'Invoice file must be PDF, JPG, JPEG or PNG.',

			'pod_file.max' => 'POD file size cannot exceed 2 MB.',
			'pod_file.mimes' => 'POD file must be PDF, JPG, JPEG or PNG.',
		]);

		try {
			$entry = Billdata::findOrFail($request->id);

			$entry->freight_invoice_no = $request->freight_invoice_no;

			$entry->freight_invoice_date = $request->freight_invoice_date;

			$entry->freight_amount = $request->freight_amount;

			if ($request->hasFile('freight_invoice_file')) {
				$file = $request->file('freight_invoice_file');
				$filename = time().'_invoice_'.$file->getClientOriginalName();
				$file->move(public_path('uploads/invoice'), $filename);
				$entry->freight_invoice_file = 'uploads/invoice/'.$filename;
			}

			if ($request->hasFile('pod_file')) {
				$file = $request->file('pod_file');
				$filename = time().'_pod_'.$file->getClientOriginalName();
				$file->move(public_path('uploads/pod'), $filename);
				$entry->pod_file = 'uploads/pod/'.$filename;
			}

			

			$entry->validated_status = null;
			$entry->submit = null;
			$entry->f_return = null;
			$entry->validation_remark = null;

			$entry->save();

			return response()->json([
				'status' => true,
				'message' => 'Returned item updated successfully and moved back to validation list.'
			]);

		} catch (\Exception $e) {
			return response()->json([
				'status' => false,
				'message' => $e->getMessage()
			], 500);
		}
	}

	public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:2048',
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
	
	public function bulkDelete(Request $request)
	{
		$request->validate([
			'ids' => 'required|array',
			'ids.*' => 'integer|exists:bill_data_upload,id',
		]);

		Billdata::whereIn('id', $request->ids)->delete();

		return redirect()->back()->with('success', 'Selected freight shipment records deleted successfully.');
	}
}
