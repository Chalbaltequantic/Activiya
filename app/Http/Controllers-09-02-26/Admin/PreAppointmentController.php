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
use App\Models\Vendor;
use App\Models\Ratedata;
use App\Models\TruckMaster;
use App\Models\Siteplant;
use App\Models\Admin;
use App\Models\PreAppointment;
use App\Models\AppointmentStatusLog;
use App\Models\PreappointmentDeliveryStatusHistory;
use App\Models\ConsigneeReturnDuration;
use App\Models\PreappointmentPodFile;

use Auth;

class PreAppointmentController extends Controller
{
    //
	public function __construct()
    {
        $this->middleware('auth:admin');     
    }
	
	public function index(Request $request)
    {
        $title = 'Appointment Data Upload';
        $pagetitle = $title.' Listing';       
		$data = $request->all();        
        return view('admin.preappointment.index',compact(['pagetitle','title']));
    }
	
	public function appointmentdatalist(Request $request)
    {
        $title = 'Pre Appintment Data Upload';
        $pagetitle = $title.' Listing';
       
	    $appointmentdatalist = PreAppointment::orderBy('created_at', 'desc')->get();       
        return view('admin.preappointment.appointmentdatalist',compact(['pagetitle','title','appointmentdatalist']));
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
				$invdocdate = $row['B'];
				$inv_doc_date = Carbon::parse($invdocdate)->format('Y-m-d');
				
				$po_no_date = $row['D'];
				$po_date = Carbon::parse($po_no_date)->format('Y-m-d');
								
				$shipment_inv_value = preg_replace("/,+/", "", $row['N']);
				

				$s5_consignor_short_name_location = Siteplant::where("plant_site_code", $row['E'])->first(["s5_d5_short_name"]);

				$d5_consignee_short_name_location = Siteplant::where("plant_site_code", $row['H'])->first(["s5_d5_short_name"]);

                $data = [
					'inv_number'  => $row['A'] ?? null,
					'inv_doc_date' => $inv_doc_date ?? null,
					'po_no' => $row['C'] ?? null,
					'po_date' => $po_date ?? null,
					'consignor_code' => $row['E'] ?? null,
					'consignor_name' => $row['F'] ?? null,
					'consignor_location' => $row['G'] ?? null,
					'consignor_short_location' => $s5_consignor_short_name_location->s5_d5_short_name ?? null,
					'consignee_code' => $row['H'] ?? null,
					'consignee_name' => $row['I'] ?? null,
					'consignee_location' => $row['J'] ?? null,
					'consignee_short_location' => $d5_consignee_short_name_location->s5_d5_short_name ?? null,
					'v_code' => $row['K'] ?? null,
					'vendor_name' => $row['L'] ?? null,
					'no_of_cases_sale' => $row['M'] ?? null,
					'shipment_inv_value' => $shipment_inv_value ?? null,
					'delivery_gross_weight' => $row['O'] ?? null,
					'company_code' => $row['P'] ?? null,
					'remarks' => $row['Q'] ?? null,
                    'created_at' => $createddate,
                    'created_by' => Auth::user()->id,
                    'status' => '1'
                ];
				
				
				// Check vendor code
					$vendorExists = Vendor::where('vendor_code', $data['v_code'])->exists();
					if (!$vendorExists) {
						$errorRows[] = ['row' => $rowNumber, 'reason' => 'Vendor code not found'];
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
					 // Duplicate check
            $exists = PreAppointment::where('inv_number', $data['inv_number'])->exists();

            if ($exists) {
                $errorRows[] = ['row' => $rowNumber, 'reason' => 'Duplicate invoice'];
                continue;
            }
					
				PreAppointment::create($data); 
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
	
	public function getAppointmentdataDetails($id)
	{
		$appointmentdata = PreAppointment::find($id);
		$userid = auth()->user()->id; //get loggedin user id		
		return view('admin.preappointment.editappointmentdata', compact('appointmentdata'));
	}

	public function save_appointmentdata(Request $request)
	{
		$validatedData = $request->validate(
			[
				'inv_number' => 'required',
				'inv_doc_date' => 'required',
				'po_no' => 'required',
				'po_date' => 'required',
				'consignor_code' => 'required',
				'consignor_name' => 'required',
				'consignor_location' => 'required',
				'consignor_short_location' => 'required',
				'consignee_code' => 'required',
			],
			[
				'inv_number.required' => 'Please enter inv number',
				'inv_doc_date.required' => 'Please enter inv doc date',
				'po_no.required' => 'Please enter po number',
				'po_date.required' => 'Please enter po date',
				'consignor_code.required' => 'Please enter consignor code',
				'consignor_name.required' => 'Please enter consignor name',
				'consignor_location.required' => 'Please enter consignor location',
				'consignor_location.required' => 'Please enter consignor location',
				'consignor_short_location.required' => 'Please enter consignor short location',
				'consignee_code.required' => 'Please enter consignee code',
			]
		);
			$id = $request->id;
			PreAppointment::find($id)->update([
					'inv_number'  => $request->inv_number,
					'inv_doc_date' => $request->inv_doc_date,					
					'po_no' => $request->po,					
					'po_date' => $request->po_date,					
					'consignor_code' => $request->consignor_code,
					'consignor_name' => $request->consignor_name,
					'consignor_location' => $request->consignor_location,
					'consignor_short_location' => $request->consignor_short_location,
					'consignee_code' => $request->consignee_code,
					'consignee_name' => $request->consignee_name,
					'consignee_location' => $request->consignee_location,
					'consignee_short_location' => $request->consignee_short_location,
					'v_code' => $request->v_code,
					'vendor_name' => $request->vendor_name,
					'no_of_cases_sale' => $request->no_of_cases_sale,
					'shipment_inv_value' => $request->shipment_inv_value,
					'delivery_gross_weight' => $request->delivery_gross_weight,
					'company_code' => $request->company_code,
					'remarks' => $request->remarks,
					'updated_at' => Carbon::now(),
					'status' => $request->status,
					'updated_by	' => Auth::user()->id	
					]);
			return Redirect('/admin/preappointmentdata/appointment-history')->with('success', 'Appointment data updated successfully!');
		
	}
	
	
	public function DeleteAppointmentData($id)
	{
		PreAppointment::find($id)->delete();
		return Redirect('/admin/preappointment')->with('success', 'Appointment date deleted successfully!');
	}
	
	
	//manual Upload
	public function manualupload()
	{
		$userid = auth()->user()->id; //get loggedin user id		
		return view('admin.preappointment.manualupload');
	}

	public function save_manual_appointmentdata(Request $request)
	{
    $created_by = Auth::id();
    $createddate = date('Y-m-d H:i:s');

    $inv_number = $request->input('inv_number', []);
    $inv_doc_date = $request->input('inv_doc_date', []);
    $po_no = $request->input('po_no', []);
    $po_date = $request->input('po_date', []);
    $consignor_code = $request->input('consignor_code', []);
    $consignor_name = $request->input('consignor_name', []);
    $consignor_location = $request->input('consignor_location', []);
	$consignee_code = $request->input('consignee_code', []);
    $consignee_name = $request->input('consignee_name', []);
    $consignee_location = $request->input('consignee_location', []);
    $v_code = $request->input('v_code', []);
    $vendor_name = $request->input('vendor_name', []);
    $no_of_cases_sale = $request->input('no_of_cases_sale', []);
    $shipment_inv_value = $request->input('shipment_inv_value', []);
    $delivery_gross_weight = $request->input('delivery_gross_weight', []);
    $company_code = $request->input('company_code', []);
    $remarks = $request->input('remarks', []);

    $count = count($inv_number);
    $insertedCount = 0;
    $errorRows = [];

    DB::beginTransaction();

    try {
        for ($i = 0; $i < $count; $i++) {
            $rowNumber = $i + 1;

            // Format dates
            $invdocdate_formatted = null;
            if (!empty($inv_doc_date[$i])) {
                try {
                    $invdocdate_formatted = Carbon::parse($inv_doc_date[$i])->format('Y-m-d');
                } catch (\Exception $e) {
                    $errorRows[] = ['row' => $rowNumber, 'reason' => 'Invalid Invoice Date'];
                    continue;
                }
            }

          $podate_formatted = Carbon::parse($po_date[$i])->format('Y-m-d');

            // Clean numeric fields
            $shipment_value = isset($shipment_inv_value[$i]) ? preg_replace("/,+/", "", $shipment_inv_value[$i]) : null;
            $gross_weight = isset($delivery_gross_weight[$i]) ? preg_replace("/,+/", "", $delivery_gross_weight[$i]) : null;
            // Skip if mandatory fields are missing
            if (empty($inv_number[$i]) || empty($inv_doc_date[$i]) ) {
                $errorRows[] = ['row' => $rowNumber, 'reason' => 'Mandatory fields missing'];
                continue;
            }

            // Get short names
            $s5 = Siteplant::where("plant_site_code", $consignor_code[$i])
               ->select("s5_d5_short_name")
               ->first();
            $d5 = Siteplant::where("plant_site_code", $consignee_code[$i])
               ->select("s5_d5_short_name")
               ->first();
			
            // Prepare data
            $data = [
                'inv_number' => $inv_number[$i] ?? null,
                'inv_doc_date' => $invdocdate_formatted,
                'po_no' => $po_no[$i] ?? null,
                'po_date' => $podate_formatted,
                'consignor_code' => $consignor_code[$i] ?? null,
                'consignor_name' => $consignor_name[$i] ?? null,
                'consignor_location' => $consignor_location[$i] ?? null,
                'consignor_short_location' => $s5->s5_d5_short_name ?? null,
                'consignee_code' => $consignee_code[$i] ?? null,
                'consignee_name' => $consignee_name[$i] ?? null,
                'consignee_location' => $consignee_location[$i] ?? null,
                'consignee_short_location' => $d5->s5_d5_short_name ?? null,
                'v_code' => $v_code[$i] ?? null,
                'vendor_name' => $vendor_name[$i] ?? null,
                'no_of_cases_sale' => $no_of_cases_sale[$i] ?? null,
                'shipment_inv_value' => $shipment_value,
                'delivery_gross_weight' => $gross_weight,
                'company_code' => $company_code[$i] ?? null,
                'remarks' => $remarks[$i] ?? null,
                'created_at' => $createddate,
                'created_by' => $created_by,
                'status' => '1'
            ];
            // Validation checks
            if (!Vendor::where('vendor_code', $data['v_code'])->exists()) {
                $errorRows[] = ['row' => $rowNumber, 'reason' => 'Vendor code not found'];
                continue;
            }

            // Duplicate check
            $exists = PreAppointment::where('inv_number', $data['inv_number'])->exists();

            if ($exists) {
                $errorRows[] = ['row' => $rowNumber, 'reason' => 'Duplicate invoice'];
                continue;
            }
			
            PreAppointment::create($data);
		  
            $insertedCount++;
        }

        DB::commit();

        if ($insertedCount === 0) {
            return back()
                ->withInput()
                ->with([
                    'errorRows' => $errorRows,
                    'error' => 'No data inserted. Please correct the highlighted errors.',
                ]);
        }

        return redirect('/admin/preappointmentdata/appointment-history')
            ->with('success', "$insertedCount appointments saved successfully.")
            ->with('errorRows', $errorRows);

    } catch (\Exception $e) {
        DB::rollback();
        return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
		//return redirect()->back()->with('error', 'Something went wrong');
    }
}



/*****  Appointment Request Board ******/
	public function pre_appointment_request_boards()
    { 
        $title = 'Appointment request board';
        $pagetitle = $title.' Listing';
		$created_by = Auth::user()->role_id;
	
      
		$usr_id = Auth::user()->id;
		$vendorCode = Auth::user()->vendor_code;
		$entries = PreAppointment::select([
			'id', 'inv_number', 'inv_doc_date',	'po_no', 'po_date',
			'consignor_name', 'consignor_location', 'consignee_code','consignee_name', 
			'consignee_location', 'vendor_name', 'no_of_cases_sale',
			'appointment_status'

		])
		->where(function($query) {
				$query->whereNull('appointment_date')
				->orWhere('appointment_date', '');
				})
		
					
		//->where('v_code', $vendorCode)
		->orderBy('created_at', 'desc')
		->get();
		
		$updatedentries = PreAppointment::select([
			'id', 'inv_number', 'inv_doc_date',	'po_no', 'po_date',
			'consignor_name', 'consignor_location', 'consignee_code','consignee_name', 
			'consignee_location', 'vendor_name','no_of_cases_sale',
			'appointment_status', 'appointment_date'
		])
		->where('appointment_date','<>','')
		
		//->where('v_code', $vendorCode)
		->orderBy('created_at', 'desc')
		->get();
	
		
        return view('admin.preappointment.assign_appointment_date_time',compact(['pagetitle','title','entries','updatedentries']));
    }
	

	public function updateDateTime(Request $request)
    {
        $request->validate([
            'id' => 'required|integer',
            'appointment_date' => 'required|date_format:Y-m-d H:i'
        ]);
		$updated_by = Auth::user()->role_id;
		$updated_at = date('Y-m-d H:i:s');
        $preappointment = Preappointment::find($request->id);
        $preappointment->appointment_date = $request->appointment_date;
        $preappointment->appointment_date_updated_at = $updated_at;
        $preappointment->appointment_date_updated_by = $updated_by;
		
        $preappointment->save();

        return response()->json(['success' => true]);
    }



/// Appointment update
	public function appointment_lr_detail_update()
    { 
        $title = 'Appointment Update';
        $pagetitle = $title.' Listing';
		$created_by = Auth::user()->role_id;
	
        // Filter records for account1
		
			$usr_id = Auth::user()->id;
			$vendorCode = Auth::user()->vendor_code;
			$entries = PreAppointment::select([
				'id', 'inv_number', 'inv_doc_date',	'po_no', 'po_date','lr_no', 'lr_date',
				'consignor_name', 'consignor_location', 'consignee_code','consignee_name', 
				'consignee_location', 'vendor_name', 'no_of_cases_sale', 'appointment_date'
				

			])
			->where(function($query) {
					$query->whereNotNull('appointment_date')
					->orWhere('appointment_date', '<>','');
					})
			->where(function($query) {
				$query->whereNull('lr_no')
				->orWhere('lr_no', '');
				})
			//->where('v_code', $vendorCode)
			->get();
			
			$updatedentries = PreAppointment::select([
				'id', 'inv_number', 'inv_doc_date','po_no', 'po_date','lr_no', 'lr_date',
				'consignor_name', 'consignor_location', 'consignee_code','consignee_name', 
				'consignee_location', 'vendor_name','no_of_cases_sale','appointment_date',
				'truck_type', 'vehicle_no', 'driver_name','driver_no','arrival_date', 't_code', 'remarks', 'appointment_status'
			])
			->where('lr_no','<>','')
			->where('lr_date', '<>','')
			//->where('v_code', $vendorCode)
			->get();
	
		
        return view('admin.preappointment.appointment_lrdetail_update',compact(['pagetitle','title','entries','updatedentries']));
    }
	///update lr detail & vehicle detail and driver detail
	
	public function updateAppointment(Request $request)
    {
        $request->validate([
            'id'     => 'required|integer',
            'action' => 'required|in:supply,reschedule,close',
        ]);

        if ($request->action === 'supply') {

            $request->validate([
                'lr_no'       => 'required',
                'lr_date'     => 'required|date',
                't_code'      => 'required',
                'truck_type'  => 'required',
                'vehicle_no'  => 'required',
            ]);
            $data = [
                'lr_no'      => $request->lr_no,
                'lr_date'    => $request->lr_date,
                't_code'     => $request->t_code,
                'truck_type' => $request->truck_type,
                'vehicle_no' => $request->vehicle_no,
                'driver_name' => $request->driver_name,
                'driver_no' => $request->driver_no,
                'appointment_status'     => 'SUPPLIED',
				'remarks'     => $request->remark,
				'appointment_status_updated_at' => now(),
				'appointment_status_updated_by' => Auth::user()->id
            ];
			
			
        }

        if ($request->action === 'reschedule') {
            $data = [
            'remarks'     => $request->remark,
			'appointment_status'	=>	'RESCHEDULED',
            'appointment_status_updated_at' => now(),
			'appointment_status_updated_by' => Auth::user()->id
			];
			
        }

        if ($request->action === 'close') {
			$data = [
            'remarks'     => $request->remark,
			'appointment_status'	=>	'CLOSED',
            'appointment_status_updated_at' => now(),
			'appointment_status_updated_by' => Auth::user()->id
			];
           
        }

        $update = DB::table('preappointments')
            ->where('id', $request->id)
            ->update($data);
		if($update)
		{
			
			 DB::table('preappointment_status_histories')->insert([
                'preappointment_id' => $request->id,
                'appointment_status'=> $request->action,
                'remark'      => $request->remark,
                'updated_by'  => Auth::user()->id,
                'updated_by_name'  => Auth::user()->name,
                'created_at'  => now(),
            ]);			
			
			return response()->json([
				'success' => true,
				'message' => 'Appointment updated successfully'
			]);
		}
		else{
			return response()->json([
				'success' => false,
				'message' => 'Appointment not updated. Please try again!'
			]);
			
		}		
    }
	
	/////////////////////////////////////////////////////////////////////////////////////
	
	
	public function Appointment_delivery_status()
	{
		$title = 'Appointment Delivery Status';
		$pagetitle = $title . ' Listing';

		$user = Auth::user();   // Logged-in user
		$mobile = $user->mobile;
		$role = $user->role_id; // 1 = Admin, 12 = Driver, 8 = Consignee

		// -------------------------
		// BASE QUERY FOR PENDING ENTRIES
		// -------------------------
		$entries = PreAppointment::with([        'latestDeliveryStatus:preappointment_delivery_status_histories.appointment_id,delivery_status,remarks',
        'histories:id,appointment_id,delivery_status,delivery_otp,remarks,created_at'
		])
			
		->leftJoin('site_plants', 'preappointments.consignee_code', '=', 'site_plants.plant_site_code')
		->select([
			'preappointments.id','inv_number','inv_doc_date','lr_no','lr_date','po_no','po_date',
			'appointment_date','consignor_name','consignor_location','consignee_code','consignee_name',
			'consignee_location','vendor_name','no_of_cases_sale',
			'truck_type','vehicle_no','driver_name','driver_no','arrival_date',
			'shipment_inv_value','appointment_status','remarks as apptremarks',
			'reason_not_accepting','reschedule_date','site_plants.site_incharge_contact_no'
		])
		->where('appointment_status', 'SUPPLIED')
		->whereDoesntHave('latestDeliveryStatus', function ($q) {
			$q->where('delivery_status', 'Delivered');
		});
		

		// -------------------------
		// BASE QUERY FOR UPDATED ENTRIES
		// -------------------------
		$updatedentries = PreAppointment::with([        'latestDeliveryStatus:preappointment_delivery_status_histories.appointment_id,delivery_status,remarks',
        'histories:id,appointment_id,delivery_status,delivery_otp,remarks,created_at'
		])	
		->leftJoin('site_plants', 'preappointments.consignee_code', '=', 'site_plants.plant_site_code')
		->select([
			'preappointments.id','inv_number','inv_doc_date','lr_no','lr_date','po_no','po_date','appointment_date','remarks as apptremarks','consignor_name','consignor_location','consignee_code','consignee_name',
			'consignee_location','vendor_name','no_of_cases_sale',
			'truck_type','vehicle_no','driver_name','driver_no','arrival_date',
			'shipment_inv_value','appointment_status',
			'reason_not_accepting','reschedule_date','site_plants.site_incharge_contact_no'
		])
		->where('appointment_status', 'SUPPLIED')
		->whereHas('latestDeliveryStatus', function ($q) {
			$q->where('delivery_status', 'Delivered');
		});
		
		// -----------------------------------------
		// APPLY FILTER BASED ON USER ROLE / MOBILE
		// -----------------------------------------

		if ($role == 12) {  
			// DRIVER → Show appointments assigned to him
			$entries->where('preappointments.driver_no', $mobile);
			$updatedentries->where('preappointments.driver_no', $mobile);

		} elseif ($role == 8  || $role==11) {  
			// CONSIGNEE → Match with site incharge number
			$entries->where('site_plants.site_incharge_contact_no', $mobile);
			$updatedentries->where('site_plants.site_incharge_contact_no', $mobile);
		}
		// If Admin → no filter required
		$entries = $entries->get();
		$updatedentries = $updatedentries->get();
		
		return view(
			'admin.preappointment.appointment_delivey_status_update',
			compact(['pagetitle','title','entries','updatedentries'])
		);
	}
	
	
	public function updateDeliveryStatus(Request $request, $id)
	{
		$appointment = PreAppointment::findOrFail($id);

		$status = $request->status;
		$remarks = $request->remarks;
		$created_by = Auth::user()->role_id;
		$createddate = date('Y-m-d H:i:s');

		//  Check if return is allowed
		if (in_array($status, ['Return by Driver', 'Return by Buyer'])) {
			// Get return setting for consignee
			$returnSetting = ConsigneeReturnDuration::where('consignee_code', $appointment->consignee_code)
				->where(function($q) {
					$q->whereNull('end_date')
					  ->orWhere('end_date', '>=', Carbon::now());
				})
				->first();

			// Get last Reported delivery status
			$reportedStatus = PreappointmentDeliveryStatusHistory::where('appointment_id', $appointment->id)
				->where('delivery_status', 'Reported')
				->latest('created_at')
				->first();

			if ($reportedStatus) {
				// Get return duration — default to 45 if null
				$returnMinutes = $returnSetting && $returnSetting->return_time_minutes !== null	? $returnSetting->return_time_minutes : 45;

				$minTime = Carbon::parse($reportedStatus->created_at)->addMinutes($returnMinutes);

				if (Carbon::now()->lt($minTime)) {
					//return back()->with('error', "Return not allowed before return duration ({$returnMinutes} minutes).");
					return response()->json([
							'success' => false,
							'message' => "Return not allowed before return duration ({$returnMinutes} minutes)."
						]);
				}
			} else {
				return response()->json([
					'success' => false,
					'message' => 'Cannot return. Reported status not found.'
				]);
				//return back()->with('error', 'Cannot return. Reported status not found.');
			}
		}

		// Save new delivery status
		$history = PreappointmentDeliveryStatusHistory::create([
			'appointment_id'   => $appointment->id,
			'inv_no'           => $appointment->inv_number,
			'delivery_status'  => $status,
			'remarks'          => $remarks,
			'created_at'       => $createddate,
			'created_by'       => $created_by
		]);
		
		return response()->json([
			'success' => true,
			'status'  => $history->delivery_status,
			'remarks' => $history->remarks,
			'time'    => Carbon::parse($history->created_at)->format('d-m-Y H:i'),
			'message' => 'Status updated successfully.'
		]);
		
	}
	//Delivery Status history 
	public function ajaxHistory($id)
    {
        $appointment = PreAppointment::with('histories')->findOrFail($id);
		
		$html = '<ul class="list-group">';
		foreach ($appointment->histories as $h) {
			$html .= '<li class="list-group-item">'
				   . '<strong>'.$h->status.'</strong>'
				   . ($h->remarks ? ' - '.$h->remarks : '')
				   . '<br><small>'.$h->created_at->format("d-m-Y H:i").'</small>'
				   . '</li>';
		}
		$html .= '</ul>';

		return response()->json(['html' => $html]);
       // return view('appointments.deliveryhistory', compact('appointment'));
    }
	//upload otp by driver
	public function updateDeliveryOtp(Request $request)
	{
		$request->validate([
			'id' => 'required',
			'otp' => 'required'
		]);

		$record = PreappointmentDeliveryStatusHistory::where('appointment_id', $request->id)
			->where('delivery_status', 'Delivered')
			->latest('id')
			->first();

		if (!$record) {
			return response()->json([
				'success' => false,
				'message' => 'Delivered record not found.'
			]);
		}

		$record->delivery_otp = $request->otp;
		$record->save();

		return response()->json([
			'success' => true,
			'message' => 'OTP updated successfully.'
		]);
	}	
	///Upload POD File (front and Back)
	
	public function Appointment_pod_files()
	{
		$title = 'Appointment POD file';
		$pagetitle = $title . ' Listing';

		$user = Auth::user();
		$mobile = $user->mobile;
		$role = $user->role_id;

		// -----------------------------
		// BASE QUERY: PENDING POD UPLOAD
		// -----------------------------
		$entries = PreAppointment::with(['podFront', 'podBack'])
			->leftJoin('site_plants', 'preappointments.consignee_code', '=', 'site_plants.plant_site_code')
			->where('assigned_to_consignee', '<>', '')
			->where('appointment_status', '<>', '')
			->whereHas('histories', function ($q) {
				$q->where('delivery_status', 'Delivered');
			})
			->where(function ($query) {
				$query->whereDoesntHave('podFront')
					  ->orWhereDoesntHave('podBack');
			});

		// -----------------------------
		// BASE QUERY: COMPLETED POD UPLOAD
		// -----------------------------
		$updatedentries = PreAppointment::with(['podFront', 'podBack'])
			->leftJoin('site_plants', 'preappointments.consignee_code', '=', 'site_plants.plant_site_code')
			->where('appointment_status', '<>', '')
			->whereHas('histories', function ($q) {
				$q->where('delivery_status', 'Delivered');
			})
			->whereHas('latestDeliveryStatus', function ($q) {
			$q->where('delivery_status', 'Delivered');
		});


		// DRIVER — Match appointments.driver_no with login mobile
		if ($role == 12) {
			$entries->where('preappointments.driver_no', $mobile);
			$updatedentries->where('preappointments.driver_no', $mobile);
		}

		// CONSIGNEE — Match site incharge number + consignee_code is auto-joined
		if ($role == 8 || $role==11) {
			$entries->where('site_plants.site_incharge_contact_no', $mobile);
			$updatedentries->where('site_plants.site_incharge_contact_no', $mobile);
		}

		// FETCH AFTER APPLYING ROLE FILTER
		$entries = $entries->get();
		$updatedentries = $updatedentries->get();

		return view('admin.preappointment.podfileupload',
			compact('pagetitle', 'title', 'entries', 'updatedentries')
		);
	}
	
	public function uploadPODFile(Request $request)
	{
		
		try{
		
		$request->validate([
			'appointment_id' => 'required',
			'file_type' => 'required|in:podfront,podback',
			'file' => 'required|file|mimes:jpg,jpeg,png,webp,heic',
		]);

		$file = $request->file('file');
		
		if (!$file || !$file->isValid()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid file uploaded.'
            ], 422);
        }
		
		
		$originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
		$extension = $file->getClientOriginalExtension();
		$fileName = time().'_'.str_replace(" ","_",$originalName). '.' . $extension;

		$destinationPath = public_path('uploads/preappointmentpod');
		$file->move($destinationPath, $fileName);

		$relativePath = 'uploads/preappointmentpod/' . $fileName;

		$podFile = PreappointmentPodFile::create([
			'appointment_id' => $request->appointment_id,
			'file_type' => $request->file_type,
			'file_path' => $relativePath,
		]);

		return response()->json([
			'success' => true,
			'message' => 'File uploaded successfully.',
			'file_url' => asset($relativePath),
			'file_type' => $request->file_type,
		]);
		} catch (\Illuminate\Validation\ValidationException $e) {

        // RETURN VALIDATION ERRORS
        return response()->json([
            'success' => false,
            'message' => $e->errors()
        ], 422);

    } catch (\Exception $e) {

        // UNEXPECTED ERRORS
        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 500);
		}
		
	}
}
	
