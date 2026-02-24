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

use App\Models\Tracking;
use App\Models\Vendor;
use App\Models\Ratedata;
use App\Models\TruckMaster;
use App\Models\Siteplant;
use App\Models\Admin;

use Auth;


class TrackingController extends Controller
{
    //
	public function __construct()
    {
        $this->middleware('auth:admin');     
    }
	
	public function index(Request $request)
    {
        $title = 'Tracking Data Upload';
        $pagetitle = $title.' Listing';
             
        return view('admin.tracking.index',compact(['pagetitle','title']));
    }
	
	public function trackingdatalist(Request $request)
    {
        $title = 'Tracking Data Upload';
        $pagetitle = $title.' Listing';
		$vendorCode = Auth::user()->vendor_code ?? '';

		$user_role = Auth::user()->role_id;
		$data = $request->all();        
	    $trackingdatalist = Tracking::orderBy('created_at', 'desc')
		->when(!empty($vendorCode), function ($query) use ($vendorCode) {
            $query->where('vendor_code', $vendorCode);
        })->get();       
        return view('admin.tracking.trackingdatalist',compact(['pagetitle','title','trackingdatalist','user_role']));
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
				$lead_time = $row['N'];
				$dispatch_date = $row['L'];

				$dispatchdate = Carbon::parse($dispatch_date)->format('Y-m-d');
				
				$delivery_due_date = date('Y-m-d', strtotime($dispatchdate . ' +' . $lead_time . ' days'));
			
                $data = [
                    'indent_no' => $row['A'] ?? null,
                    'customer_po_no' => $row['B'] ?? null,
                    'origin' => $row['C'] ?? null,
                    'destination' => $row['D'] ?? null,
                    'vendor_name' => $row['E'] ?? null,
                    'vendor_code' => $row['F'] ?? null,
                    'vehicle_type' => $row['G'] ?? null,
                    'lr_no' => $row['H'] ?? null,
                    'cases' => $row['I'] ?? null,
                    'truck_no' => $row['J'] ?? null,
                    'driver_number' => $row['K'] ?? null,
                    'dispatch_date' => $dispatchdate ?? null,
                    'dispatch_time' => $row['M'] ?? null,
                    'lead_time' => $row['N'] ?? null,
                    'distance' => $row['O'] ??  null,
                    'delivery_due_date' => $delivery_due_date,                   
                    'created_at' => $createddate,
                    'created_by' => Auth::user()->id,
                    'status' => '1'
                ];

/*
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
*/
          

            // ---- INSERT ----
            Tracking::create($data);
            $insertedCount++;
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
	
	public function getTrackingdataDetails($id)
	{
		$trackingdata = Tracking::find($id);
		$userid = auth()->user()->id; //get loggedin user id		
		return view('admin.tracking.edittrackingdata', compact('trackingdata'));
	}

	public function save_trackingdata(Request $request)
	{
		$validatedData = $request->validate(
			[
				'indent_no' => 'required',
				'customer_po_no' => 'required',
			],
			[
				'indent_no.required' => 'Please enter indent number',
				'customer_po_no.required' => 'Please enter customer po no',
			]
		);
			$id = $request->id;
			$created_by = Auth::user()->id; 	
			Tracking::find($id)->update([
				'indent_no' => $request->indent_no,
				'customer_po_no' => $request->customer_po_no,
				'origin' => $request->origin,
				'destination' => $request->destination,
				'vendor_name' => $request->vendor_name,
				'vendor_code' => $request->vendor_code,
				'vehicle_type' => $request->vehicle_type,
				'lr_no' => $request->lr_no,
				'cases' => $request->cases,
				'truck_no' => $request->truck_no,
				'driver_number' => $request->driver_number,
				'dispatch_date' => $request->dispatch_date,
				'dispatch_time' => $request->dispatch_time,
				'lead_time' => $request->lead_time,
				'distance' => $request->distance,
				'delivery_due_date' => $request->delivery_due_date,
				'shipment_status' => $request->shipment_status,
				'transit_status' => $request->transit_status,
				'distance_covered' => $request->distance_covered,
				'current_location' => $request->current_location,
				'distance_to_cover' => $request->distance_to_cover,
				'tracking_link' => $request->tracking_link,
				'reporting_date' => $request->reporting_date,
				'reporting_time' => $request->reporting_time,
				'release_date' => $request->release_date,
				'release_time' => $request->release_time,
				'detention_days' => $request->detention_days,
				'updated_at' => Carbon::now(),
				'updated_by' => $created_by,
				'status' => 1,
			]);
			return Redirect('/admin/trackingdata/tracking-history')->with('success', 'Data updated successfully!');
		
	}
	
	
	public function DeleteTrackingData($id)
	{
		Tracking::find($id)->delete();
		return Redirect('/admin/trackingdata/tracking-history')->with('success', 'Data deleted successfully!');
	}
	
	
	//manual Upload Consignee
	
	public function manualupload()
	{
		$userid = auth()->user()->id; //get loggedin user id		
		return view('admin.tracking.manualupload');
	}

	public function save_manual_trackingdata(Request $request)
	{
		
		$created_by = Auth::user()->id; 
		
		$createddate = date('Y-m-d H:i:s');
		
		$indent_no  = $request->input('indent_no', []);
		$customer_po_no  = $request->input('customer_po_no', []);
		$origin  = $request->input('origin', []);
		$destination  = $request->input('destination', []);
		$vendor_name  = $request->input('vendor_name', []);
		$vendor_code  = $request->input('vendor_code', []);
		$vehicle_type  = $request->input('vehicle_type', []);
		$lr_no  = $request->input('lr_no', []);
		$cases  = $request->input('cases', []);
		$truck_no = $request->input('truck_no', []);
		$driver_number  = $request->input('driver_number', []);
		$dispatch_date  = $request->input('dispatch_date', []);
		$dispatch_time  = $request->input('dispatch_time', []);
		$lead_time  = $request->input('lead_time', []);
		$distance  = $request->input('distance', []);		
		$created_at = $createddate;
		$status = 1;

		$count = count($indent_no);
		
		$errorRows = [];
		$insertedCount = 0;
		$validData = [];

        DB::beginTransaction();
        try {
            for ($i = 0; $i < $count; $i++) {

                $rowNumber = $i + 1;
			
				$lead_time_val = $lead_time[$i];
				$dispatchdate = Carbon::parse($dispatch_date[$i])->format('Y-m-d');
				
				$delivery_due_date = date('Y-m-d', strtotime($dispatchdate . ' +' . $lead_time_val . ' days'));
				
				// Skip if mandatory fields are missing
            if (empty($indent_no[$i]) || empty($customer_po_no[$i])) {
                $errorRows[] = ['row' => $rowNumber, 'reason' => 'Mandatory fields missing'];
                continue;
            }
			
                $data = [
							'indent_no' => $indent_no[$i] ?? null,
							'customer_po_no' => $customer_po_no[$i] ?? null,
							'origin' => $origin[$i] ?? null,
							'destination' => $destination[$i] ?? null,
							'vendor_name' => $vendor_name[$i] ?? null,
							'vendor_code' => $vendor_code[$i] ?? null,
							'vehicle_type' => $vehicle_type[$i] ?? null,
							'lr_no' => $lr_no[$i] ?? null,
							'cases' => $cases[$i] ?? null,
							'truck_no' => $truck_no[$i] ?? null,
							'driver_number' => $driver_number[$i] ?? null,
							'dispatch_date' => $dispatchdate ?? null,
							'dispatch_time' => $dispatch_time[$i] ?? null,
							'lead_time' => $lead_time[$i] ?? null,
							'distance' => $distance[$i] ??  null,
							'delivery_due_date' => $delivery_due_date,                   
							'created_at' => $createddate,
							'create_by' => $created_by,
							'status' => '1'
						];
				//print_r($data); exit;
			$exists = Tracking::where('indent_no', $data['indent_no'])
                ->where('customer_po_no', $data['customer_po_no'])
                ->exists();

            if ($exists) {
                $errorRows[] = ['row' => $rowNumber, 'reason' => 'Duplicate entry'];
                continue;
            }
				
				
				Tracking::create($data);
                $insertedCount++;
			}
			//print_r($errorRows); exit;
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
			//return redirect()->back()->with('error', 'Error: Something went wrong.');
        }
		
	}
	
	
	//manual Upload Vendor
	
	public function manualupload_by_vendor()
	{
		$userid = auth()->user()->id; //get loggedin user id	
		$vendorCode = Auth::user()->vendor_code ?? '';
		
		$trackingdatalist = Tracking::whereNull('shipment_status')
		->when(!empty($vendorCode), function ($query) use ($vendorCode) {
            $query->where('vendor_code', $vendorCode);
        })
		->orderBy('created_at', 'desc')->get();
		$updatedtrackingdatalist = Tracking::whereNotNull('shipment_status')
		->when(!empty($vendorCode), function ($query) use ($vendorCode) {
            $query->where('vendor_code', $vendorCode);
        })
		->orderBy('created_at', 'desc')->get();
		return view('admin.tracking.manualupload_by_vendor', compact(['trackingdatalist', 'updatedtrackingdatalist']));
	}
	
	public function save_manual_trackingdata_by_vendor(Request $request)
	{
        
        $saveErrors = [];		
		foreach ($request->data as $row) 
		{
			$entry = Tracking::find($row['id']);
			
			$distance_covered = $row['distance_covered'];
			if($distance_covered <=300)
			{
				$transit_status = 'Off Track';
			}
			else
			{
				$transit_status = 'On Track';
			}
			
			$indent_no = $row['indent_no'];
			if (!$entry) {
                continue; // skip if entry not found
            }
						
			$createddate = date('Y-m-d H:i:s');
			 try{
					if(!empty($row['shipment_status']) && !empty($row['distance_covered']))
					{
						$entry->shipment_status = $row['shipment_status'];
						$entry->transit_status = $transit_status;
						$entry->distance_covered = $row['distance_covered'];
						$entry->current_location = $row['current_location'];
						$entry->distance_to_cover = $row['distance_to_cover'];
						$entry->tracking_link = $row['tracking_link'];
						$entry->updatedby_vendor = Auth::user()->id;				
						$entry->updatedby_vendor_at = $createddate;				
						$entry->save();
					}
				} 
				catch (\Exception $e) 
				{
					Log::error("Save failed for Indent No: {$indent_no} — Error: " . $e->getMessage());
					
					$saveErrors[] = "Unexpected error while saving data for Indent No: {$indent_no}";
				}
		} //for loop 
		return back()->with([
                 'saveErrors' => $saveErrors,
			]);
	}
	
	
	public function update_by_vendor_consign()
	{
		$userid = auth()->user()->id; //get loggedin user id	
		$vendorCode = Auth::user()->vendor_code ?? '';
		
		$trackingdatalist = Tracking::whereNull('reporting_date')
		->whereNull('reporting_time')->whereNotNull('shipment_status')
		->when(!empty($vendorCode), function ($query) use ($vendorCode) {
            $query->where('vendor_code', $vendorCode);
        })
		->orderBy('created_at', 'desc')->get();
		$updatedtrackingdatalist = Tracking::whereNotNull('reporting_date')		
		->whereNotNull('reporting_time')
		->when(!empty($vendorCode), function ($query) use ($vendorCode) {
            $query->where('vendor_code', $vendorCode);
        })
		->orderBy('created_at', 'desc')->get();
		return view('admin.tracking.manualupload_by_consignor_consignee_vendor', compact(['trackingdatalist', 'updatedtrackingdatalist']));
	}
	
	public function save_trackingdata_by_vendor_consign(Request $request)
	{
        
        $saveErrors = [];		
		foreach ($request->data as $row) 
		{
			$entry = Tracking::find($row['id']);
					
			$indent_no = $row['indent_no'];
			if (!$entry) {
                continue; // skip if entry not found
            }
			
			$reporting_date = Carbon::parse($row['reporting_date'])->format('Y-m-d');
			$release_date = Carbon::parse($row['release_date'])->format('Y-m-d');
			
			$diff = abs(strtotime($release_date) - strtotime($reporting_date));
			
			$years = floor($diff / (365*60*60*24));
			$months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
			$detention_days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
			
			$createddate = date('Y-m-d H:i:s');
			
			 try{
					if(!empty($row['reporting_date']) && !empty($row['reporting_time']))
					{
						$entry->reporting_date = $reporting_date;
						$entry->reporting_time = $reporting_time;
						$entry->release_date = $release_date;
						$entry->release_time = $row['release_time'];
						$entry->detention_days = $detention_days;
						$entry->updatedby_vendor_consign = Auth::user()->id;				
						$entry->updatedby_vendor_consign_at = $createddate;				
						$entry->save();
					}
				} 
				catch (\Exception $e) 
				{
					Log::error("Save failed for Indent No: {$indent_no} — Error: " . $e->getMessage());
					
					$saveErrors[] = "Unexpected error while saving data for Indent No: {$indent_no}";
				}
		} //for loop 
		return back()->with([
                 'saveErrors' => $saveErrors,
			]);
	}
}
