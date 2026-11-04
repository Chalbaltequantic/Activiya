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
use App\Models\FreightBillApprovalHistory;

use App\Jobs\SendValidatedFreightMailJob;
use Auth;


class NonftlBilldataController extends Controller
{
    //
	public function __construct()
    {
        $this->middleware('auth:admin');     
    }

	
	public function import(Request $request)
    {
        $request->validate([
            'non_ftl_excel_file' => 'required|file|mimes:xls,xlsx'
        ]);

        $file = $request->file('non_ftl_excel_file');
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
					
				    $shipmentColumns = ['A', 'B', 'D', 'E', 'G', 'H', 'L', 'M', 'N', 'O', 'P'];

					$hasData = false;

					foreach ($shipmentColumns as $column) {
						$value = trim((string) ($row[$column] ?? ''));

						if ($value !== '') {
							$hasData = true;
							break;
						}
					}

					// Skip rows without shipment data
					if (!$hasData) {
						continue;
					}


				// Validate mandatory weight fields
				$chargeableWeight = trim((string) ($row['W'] ?? ''));
				$actualGrossWeight = trim((string) ($row['X'] ?? ''));

				if (!is_numeric($chargeableWeight) || (float) $chargeableWeight <= 0 ||
					!is_numeric($actualGrossWeight) || 	(float) $actualGrossWeight <= 0	) 
				{
					$errorRows[] = [
						'row' => $rowNumber,
						'reason' => 'Chargeable Weight (W) and Actual Gross Weight (X) are mandatory and must be greater than zero.'
					];

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
                    'chargeable_weight' => $chargeableWeight,
                    'actual_gross_weight' => $actualGrossWeight,
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
				->where('mode', $data['custom'])
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
	

	//manual Upload
	
	public function nonftlmanualupload()
	{
		$userid = auth()->user()->id; //get loggedin user id		
		return view('admin.billdata.nonftlmanualupload');
	}

	public function save_nonftlmanual_billdata(Request $request)
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
		$chargeable_weight = $request->input('chargeable_weight', []);
		$actual_gross_weight = $request->input('actual_gross_weight', []);
			

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
						'chargeable_weight' => $chargeable_weight[$i] ?? null,
						'actual_gross_weight' => $actual_gross_weight[$i] ?? null,
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
	
}
