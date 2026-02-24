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
use App\Models\Appointment;
use App\Models\AppointmentStatusLog;
use App\Models\DeliveryStatusHistory;
use App\Models\ConsigneeReturnDuration;
use App\Models\PodFile;

use Auth;

class AppointmentController extends Controller
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
        return view('admin.appointment.index',compact(['pagetitle','title']));
    }
	
	public function appointmentdatalist(Request $request)
    {
        $title = 'Appintment Data Upload';
        $pagetitle = $title.' Listing';
        // $appointments = Appointment::with('latestStatus')->get();     will be altered as delivery status wor completed
	    $appointmentdatalist = Appointment::orderBy('created_at', 'desc')->get();       
        return view('admin.appointment.appointmentdatalist',compact(['pagetitle','title','appointmentdatalist']));
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
				
				$lr_cn_date = $row['D'];
				$lr_date = Carbon::parse($lr_cn_date)->format('Y-m-d');
								
				$shipment_inv_value = preg_replace("/,+/", "", $row['Q']);
				$delivery_gross_weight = $row['R'];

				$s5_consignor_short_name_location = Siteplant::where("plant_site_code", $row['E'])->first(["s5_d5_short_name"]);

				$d5_consignee_short_name_location = Siteplant::where("plant_site_code", $row['H'])->first(["s5_d5_short_name"]);
				
				//echo $row['R']; echo"<br>";

                $data = [
					'inv_number'  => $row['A'] ?? null,
					'inv_doc_date' => $inv_doc_date ?? null,
					'lr_no' => $row['C'] ?? null,
					'lr_date' => $lr_date ?? null,
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
					't_code' => $row['N'] ?? null,
					'truck_type' => $row['O'] ?? null,
					'vehicle_no' => $row['P'] ?? null,
					'shipment_inv_value' => $shipment_inv_value ?? null,
					'delivery_gross_weight' => $delivery_gross_weight ?? null,
					'company_code' => $row['S'] ?? null,
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

					// Check truck code
					/*$truckExists = TruckMaster::where('code', $data['t_code'])->exists();
					if (!$truckExists) {
						$errorRows[] = ['row' => $rowNumber, 'reason' => 'Truck code not found'];
						continue;
					}*/

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
            $exists = Appointment::where('inv_number', $data['inv_number'])
                     ->where('lr_no', $data['lr_no'])
					 ->exists();

            if ($exists) {
                $errorRows[] = ['row' => $rowNumber, 'reason' => 'Duplicate invoice / lrno entry'];
                continue;
            }
					
					Appointment::create($data); 
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
		$appointmentdata = Appointment::find($id);
		$userid = auth()->user()->id; //get loggedin user id		
		return view('admin.appointment.editappointmentdata', compact('appointmentdata'));
	}

	public function save_appointmentdata(Request $request)
	{
		$validatedData = $request->validate(
			[
				'inv_number' => 'required',
				'inv_doc_date' => 'required',
				'lr_no' => 'required',
				'lr_date' => 'required',
				'consignor_code' => 'required',
				'consignor_name' => 'required',
				'consignor_location' => 'required',
				'consignor_short_location' => 'required',
				'consignee_code' => 'required',
			],
			[
				'inv_number.required' => 'Please enter inv number',
				'inv_doc_date.required' => 'Please enter inv doc date',
				'lr_no.required' => 'Please enter lr no',
				'lr_date.required' => 'Please enter lr date',
				'consignor_code.required' => 'Please enter consignor code',
				'consignor_name.required' => 'Please enter consignor name',
				'consignor_location.required' => 'Please enter consignor location',
				'consignor_location.required' => 'Please enter consignor location',
				'consignor_short_location.required' => 'Please enter consignor short location',
				'consignee_code.required' => 'Please enter consignee code',
			]
		);
			$id = $request->id;
			Appointment::find($id)->update([
					'inv_number'  => $request->inv_number,
					'inv_doc_date' => $request->inv_doc_date,
					'lr_no' => $request->lr_no,
					'lr_date' => $request->lr_date,
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
					't_code' => $request->t_code,
					'truck_type' => $request->truck_type,
					'vehicle_no' => $request->vehicle_no,
					'shipment_inv_value' => $request->shipment_inv_value,
					'delivery_gross_weight' => $request->delivery_gross_weight,
					'company_code' => $request->company_code,
					'updated_at' => Carbon::now(),
					'status' => $request->status,
					'updated_by	' => Auth::user()->id	
					]);
			return Redirect('/admin/appointmentdata/appointment-history')->with('success', 'Appointment data updated successfully!');
		
	}
	
	
	public function DeleteAppointmentData($id)
	{
		Appointment::find($id)->delete();
		return Redirect('/admin/appointment')->with('success', 'Appointment date deleted successfully!');
	}
	
	
	//manual Upload
	public function manualupload()
	{
		$userid = auth()->user()->id; //get loggedin user id		
		return view('admin.appointment.manualupload');
	}

	public function save_manual_appointmentdata(Request $request)
{
    $created_by = Auth::id();
    $createddate = date('Y-m-d H:i:s');

    $inv_number = $request->input('inv_number', []);
    $inv_doc_date = $request->input('inv_doc_date', []);
    $lr_no = $request->input('lr_no', []);
    $lr_date = $request->input('lr_date', []);
    $consignor_code = $request->input('consignor_code', []);
    $consignor_name = $request->input('consignor_name', []);
    $consignor_location = $request->input('consignor_location', []);
    $consignee_code = $request->input('consignee_code', []);
    $consignee_name = $request->input('consignee_name', []);
    $consignee_location = $request->input('consignee_location', []);
    $v_code = $request->input('v_code', []);
    $vendor_name = $request->input('vendor_name', []);
    $no_of_cases_sale = $request->input('no_of_cases_sale', []);
    $t_code = $request->input('t_code', []);
    $truck_type = $request->input('truck_type', []);
    $vehicle_no = $request->input('vehicle_no', []);
    $shipment_inv_value = $request->input('shipment_inv_value', []);
    $delivery_gross_weight = $request->input('delivery_gross_weight', []);
    $company_code = $request->input('company_code', []);

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

            $lr_cndate_formatted = null;
            if (!empty($lr_date[$i])) {
                try {
                    $lr_cndate_formatted = Carbon::parse($lr_date[$i])->format('Y-m-d');
                } catch (\Exception $e) {
                    $errorRows[] = ['row' => $rowNumber, 'reason' => 'Invalid LR Date'];
                    continue;
                }
            }

            // Clean numeric fields
            $shipment_value = isset($shipment_inv_value[$i]) ? preg_replace("/,+/", "", $shipment_inv_value[$i]) : null;
            $gross_weight = isset($delivery_gross_weight[$i]) ? preg_replace("/,+/", "", $delivery_gross_weight[$i]) : null;

            // Skip if mandatory fields are missing
            if (empty($inv_number[$i]) || empty($inv_doc_date[$i]) || empty($lr_no[$i]) || empty($lr_date[$i])) {
                $errorRows[] = ['row' => $rowNumber, 'reason' => 'Mandatory fields missing'];
                continue;
            }

            // Get short names
            $s5 = Siteplant::where("plant_site_code", $consignor_code[$i])->first(["s5_d5_short_name"]);
            $d5 = Siteplant::where("plant_site_code", $consignee_code[$i])->first(["s5_d5_short_name"]);

            // Prepare data
            $data = [
                'inv_number' => $inv_number[$i] ?? null,
                'inv_doc_date' => $invdocdate_formatted,
                'lr_no' => $lr_no[$i] ?? null,
                'lr_date' => $lr_cndate_formatted,
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
                't_code' => $t_code[$i] ?? null,
                'truck_type' => $truck_type[$i] ?? null,
                'vehicle_no' => $vehicle_no[$i] ?? null,
                'shipment_inv_value' => $shipment_value,
                'delivery_gross_weight' => $gross_weight,
                'company_code' => $company_code[$i] ?? null,
                'created_at' => $createddate,
                'created_by' => $created_by,
                'status' => '1'
            ];
//print_r($data); exit;
            // Validation checks
            if (!Vendor::where('vendor_code', $data['v_code'])->exists()) {
                $errorRows[] = ['row' => $rowNumber, 'reason' => 'Vendor code not found'];
                continue;
            }

            if (!TruckMaster::where('code', $data['t_code'])->exists()) {
                $errorRows[] = ['row' => $rowNumber, 'reason' => 'Truck code not found'];
                continue;
            }

            // Duplicate check
            $exists = Appointment::where('inv_number', $data['inv_number'])
                     ->where('lr_no', $data['lr_no'])
					 ->exists();

            if ($exists) {
                $errorRows[] = ['row' => $rowNumber, 'reason' => 'Duplicate invoice / LRNO entry'];
                continue;
            }
			//dd($data);
            Appointment::create($data);
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

        return redirect('/admin/appointmentdata/appointment-history')
            ->with('success', "$insertedCount appointments saved successfully.")
            ->with('errorRows', $errorRows);

    } catch (\Exception $e) {
        DB::rollback();
        return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
    }
}
	
/// Appointment update
	public function appointment_update_by_vendor_branch()
    { 
        $title = 'Appointment Update';
        $pagetitle = $title.' Listing';
		$created_by = Auth::user()->role_id;
	
        // Filter records for account1
		
			$usr_id = Auth::user()->id;
			$vendorCode = Auth::user()->vendor_code;
			$entries = Appointment::select([
				'id', 'inv_number', 'inv_doc_date',	'lr_no', 'lr_date',
				'consignor_name', 'consignor_location', 'consignee_code','consignee_name', 
				'consignee_location', 'vendor_name', 'no_of_cases_sale',
				'truck_type', 'vehicle_no','driver_name','driver_no','arrival_date'

			])
			->where(function($query) {
					$query->whereNull('truck_type')
					->orWhere('truck_type', '');
					})
			->where(function($query) {
					$query->whereNull('vehicle_no')
						  ->orWhere('vehicle_no', '');
				})
			//->where('v_code', $vendorCode)
			->get();
			
			$updatedentries = Appointment::select([
				'id', 'inv_number', 'inv_doc_date',	'lr_no', 'lr_date',
				'consignor_name', 'consignor_location', 'consignee_code','consignee_name', 
				'consignee_location', 'vendor_name','no_of_cases_sale',
				'truck_type', 'vehicle_no', 'driver_name','driver_no','arrival_date'
			])
			->where('truck_type','<>','')
			->where('vehicle_no', '<>','')
			//->where('v_code', $vendorCode)
			->get();
	
		
        return view('admin.appointment.appointmentupdate',compact(['pagetitle','title','entries','updatedentries']));
    }
	
	public function updateMultipleAppointment(Request $request)
	{
        
        $saveErrors = [];

		
		foreach ($request->data as $row) 
		{
			$entry = Appointment::find($row['id']);
			if (!$entry) {
                continue; // skip if entry not found
            }
						
			$inv_number = $row['inv_number'];
			$lr_no = $row['lr_no'];
		
			
			$createddate = date('Y-m-d H:i:s');
			 try{
					$entry->truck_type = $row['truck_type'];
					$entry->vehicle_no = $row['vehicle_no'];
					$entry->driver_name = $row['driver_name'];
					$entry->driver_no = $row['driver_no'];
					$entry->arrival_date = $row['arrival_date'];
					$entry->delivery_remarks = $row['delivery_remarks'];
					$entry->truck_detail_updated_by = Auth::user()->id;				
					$entry->truck_detail_updated_at = $createddate;	

					$otp = rand(100000, 999999);
					$entry->otp_for_driver = $otp;					
					$entry->save();
					
				//Send email to CONSIGNEE
					 $sitePlant = DB::table('site_plants')
						->where('plant_site_code', $entry->consignee_code)
						->first();

						if (!$sitePlant || empty($sitePlant->site_incharge_mail_id)) {
							throw new \Exception("Consignee email not found");
						}
						$toEmail = $sitePlant->site_incharge_mail_id;
					
						$subject  = "OTP for invoice no. ".$inv_number ."& LR no. ". $lr_no;
						$data = [
									'otp'     => $otp,
									'invoice' => $inv_number,
									'lr_no'   => $lr_no,
								];

						$files = [];
						Mail::send('mail.delivery_otp_mail', $data, function($message) use ($to_email, $subject, $files) {
							$message->to($to_email)->subject($subject);
							$message->from(env("MAIL_USERNAME"), 'Activiya.com');
							
						});
					}
					catch (\Exception $e) 
					{

						Log::error("Save failed for INV No: {$inv_number} — Error: " . $e->getMessage());
						
						$saveErrors[] = "Unexpected error while saving data for INV No: {$inv_number}";
					}
		} //for loop 
		return back()->with([
                 'saveErrors' => $saveErrors,
			]);
	}
	
	//SEND TO HO or CONSIGNEE after truck detail update
	
	public function appointment_send_ho_consignee()
    { 
        $title = 'Appointment Assign to HO / Consignee';
        $pagetitle = $title.' Listing';
		$created_by = Auth::user()->role_id;
	
        // Filter records for account1
		
			$usr_id = Auth::user()->id;
			$vendorCode = Auth::user()->vendor_code;
			$entries = Appointment::select([
				'id', 'inv_number', 'inv_doc_date',	'lr_no', 'lr_date',
				'consignor_name', 'consignor_location', 'consignee_code','consignee_name', 
				'consignee_location', 'vendor_name', 'no_of_cases_sale',
				'truck_type', 'vehicle_no','arrival_date'

			])
			->where(function($query) {
					$query->whereNull('assigned_to_ho')
					->orWhere('assigned_to_ho', '');
					})
			->where(function($query) {
					$query->whereNull('assigned_to_consignee')
					->orWhere('assigned_to_consignee', '');
					})
						
			//->where('v_code', $vendorCode)
			->orderBy('created_at', 'desc')
			->get();
			/*comment from above query
			//->where('truck_type','<>','')
			//->where('arrival_date','<>','')
			*/
			$updatedentries = Appointment::select([
				'id', 'inv_number', 'inv_doc_date',	'lr_no', 'lr_date',
				'consignor_name', 'consignor_location', 'consignee_code','consignee_name', 
				'consignee_location', 'vendor_name','no_of_cases_sale',
				'truck_type', 'vehicle_no', 'driver_name','driver_no','arrival_date', 'assigned_to_consignee', 'assigned_to_ho'
			])
			->where('assigned_to_consignee','<>','')
			->orWhere('assigned_to_ho','<>','')
			//->where('v_code', $vendorCode)
			->orderBy('created_at', 'desc')
			->get();
	
		
        return view('admin.appointment.appointmentassignto_ho_consignee',compact(['pagetitle','title','entries','updatedentries']));
    }
	
	 public function checkSelection(Request $request)
    {
        $id = $request->id;
        $type = $request->type;

        // No DB update yet, just validation logic
        if ($type === 'ho') {
            return response()->json(['status' => 'ok', 'message' => 'HO selected. Consignee disabled for this row.']);
        } elseif ($type === 'consignee') {
            return response()->json(['status' => 'ok', 'message' => 'Consignee selected. HO disabled for this row.']);
        }

        return response()->json(['status' => 'error', 'message' => 'Invalid selection.']);
    }

    public function updateSelection(Request $request)
    {
        $data = $request->data; // Array of rows: id, ho, consignee
		
		$createddate = date('Y-m-d H:i:s');
        foreach ($data as $row) {
            if(!empty($row['id']))
			{
				$appoint = Appointment::find($row['id']);
				if ($appoint) {
					$appoint->assigned_to_ho = $row['ho'] ? 1 : 0;
					$appoint->assigned_to_consignee = $row['consignee'] ? 1 : 0;
					$appoint->assigned_by = Auth::user()->id;
					$appoint->assigned_at = $createddate;
					
					 //  Check mapping for subvendor_code
					$mapping = DB::table('vendor_subvendor_mapping')
                    ->where('company_code', $appoint->company_code)
                    ->where('consignee_code', $appoint->consignee_code)
                    ->where('vendor_code', $appoint->v_code)
                    ->first();

					if ($mapping) {
						$appoint->subvendor_code = $mapping->subvendor_code;
					} else {
						// if no match found → leave as NULL
						$appoint->subvendor_code = null;
					}
				
					$appoint->save();
				
				}
			}
        }

        return response()->json(['status' => 'success', 'message' => 'Selection updated successfully.']);
    }
	
	
	//Send Request From Ho To CONSIGNEE
	
	public function HoTOConsignee()
    {
        // Fetch appointments (dummy example, adjust your query)
         $title = 'Appointment Assign to HO / Consignee';
        $pagetitle = $title.' Listing';
		$created_by = Auth::user()->role_id;
	
        // Filter records for account1
		
			$usr_id = Auth::user()->id;
			$vendorCode = Auth::user()->vendor_code;
			$entries = Appointment::select([
				'id', 'inv_number', 'inv_doc_date',	'lr_no', 'lr_date',
				'consignor_name', 'consignor_location', 'consignee_code','consignee_name', 
				'consignee_location', 'vendor_name', 'no_of_cases_sale',
				'truck_type', 'vehicle_no','arrival_date'

			])
			->where(function($query) {
					$query->whereNull('assigned_to_ho')
					->orWhere('assigned_to_ho', '');
					})
			->where(function($query) {
					$query->whereNull('assigned_to_consignee')
					->orWhere('assigned_to_consignee', '');
					})
			->where('truck_type','<>','')
			->where('arrival_date','<>','')			
			//->where('v_code', $vendorCode)
			->get();
			
			$updatedentries = Appointment::select([
				'id', 'inv_number', 'inv_doc_date',	'lr_no', 'lr_date',
				'consignor_name', 'consignor_location', 'consignee_code','consignee_name', 
				'consignee_location', 'vendor_name','no_of_cases_sale',
				'truck_type', 'vehicle_no', 'driver_name','driver_no','arrival_date', 'assigned_to_consignee'
			])
			->where('assigned_to_consignee','<>','')
			->orWhere('assigned_to_ho','<>','')
			//->where('v_code', $vendorCode)
			->get();
	
		
        return view('admin.appointment.appointment_send_ho_to_consignee',compact(['pagetitle','title','entries','updatedentries']));

    }

    public function assignHoToConsignee(Request $request)
    {
        $createddate = date('Y-m-d H:i:s');

		DB::beginTransaction();
        try {
            $ids = $request->input('consignee_ids', []);

            if (empty($ids)) {
                return response()->json(['status' => 'error', 'message' => 'No appointments selected.']);
            }

            Appointment::whereIn('id', $ids)->update([
                'assigned_to_consignee' => 1,
                'assigned_to_ho' => 0,
				'assigned_by' => Auth::user()->id,
				'assigned_at' => $createddate
            ]);

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Appointments successfully sent to Consignee.']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => 'Something went wrong: '.$e->getMessage()]);
        }
    }
	
	///Accept Reject Reschedule Assgnment by CONSIGNEE
	public function Appointment_accept_reject_reschedule()
    {
        // Fetch appointments (dummy example, adjust your query)
         $title = 'Appointment Accept / Reject / Reschedule';
        $pagetitle = $title.' Listing';
		$created_by = Auth::user()->role_id;
	
        // Filter records for account1
		
			$usr_id = Auth::user()->id;
			$vendorCode = Auth::user()->vendor_code;
			$entries = Appointment::select([
				'id', 'inv_number', 'inv_doc_date',	'lr_no', 'lr_date',
				'consignor_name', 'consignor_location', 'consignee_code','consignee_name', 
				'consignee_location', 'vendor_name', 'no_of_cases_sale',
				'truck_type', 'vehicle_no','arrival_date','shipment_inv_value'

			])
			->where('assigned_to_consignee', '1')
			->where('truck_type','<>','')
			->where('arrival_date','<>','')	
			->where(function($query) {
					$query->whereNull('appointment_status')
					->orWhere('appointment_status', '');
					})	
			//->where('v_code', $vendorCode)
			->get();
			
			$updatedentries = Appointment::select([
				'id', 'inv_number', 'inv_doc_date',	'lr_no', 'lr_date',
				'consignor_name', 'consignor_location', 'consignee_code','consignee_name', 
				'consignee_location', 'vendor_name','no_of_cases_sale',
				'truck_type', 'vehicle_no', 'driver_name','driver_no','arrival_date', 'assigned_to_consignee', 'shipment_inv_value', 'appointment_status', 'reason_not_accepting', 'reschedule_date'
			])
			->where('assigned_to_consignee','<>','')
			->where('appointment_status','<>','')
			//->where('v_code', $vendorCode)
			->get();
	
		
        return view('admin.appointment.appointment_accept_reject_reschedule',compact(['pagetitle','title','entries','updatedentries']));

    }
	
	public function updateStatus(Request $request)
    {
        $request->validate([
            'appointments' => 'required|array|min:1',
            'appointments.*.appointment_id' => 'required|integer|exists:appointments,id',
            'appointments.*.status' => 'required|in:accepted,rejected,rescheduled',
            'appointments.*.reason' => 'nullable|string',
            'appointments.*.reschedule_date' => 'nullable|date'
        ]);

        DB::beginTransaction();
        try {
            foreach ($request->appointments as $data) {
				
				
                // Validation for conditional fields
               /* if ($data['status'] == 'rejected' && empty($data['reason'])) {
                    throw new \Exception("Reason is required for rejection (Appointment ID: {$data['appointment_id']}).");
                }
                if ($data['status'] == 'rescheduled' && (empty($data['reason']) || empty($data['reschedule_date']))) {
                    throw new \Exception("Reason and reschedule date are required for rescheduling (Appointment ID: {$data['appointment_id']}).");
                }*/
				$createddate = date('Y-m-d H:i:s');
				$appointment = Appointment::findOrFail($data['appointment_id']);
				$appointment->appointment_status = $data['status'];
				$appointment->reason_not_accepting = $data['status'] != 'accepted'? $data['reason']:null;
				$appointment->reschedule_date = $data['status'] == 'rescheduled' ? $data['reschedule_date'] : null;
				$appointment->appointment_status_updated_by = Auth::user()->id;
				$appointment->appointment_status_updated_at = $createddate;
				$appointment->save();
				
                AppointmentStatusLog::create([
                    'appointment_id' => $data['appointment_id'],
                    'consignee_id' => auth()->id(),
                    'status' => $data['status'],
                    'reason' => $data['status'] != 'accepted'? $data['reason']:null,
                    'reschedule_date' => $data['status'] == 'rescheduled' ? $data['reschedule_date'] : null
                ]);
            }
            DB::commit();

            return response()->json(['success' => true, 'message' => 'Appointments updated successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }
	
	
	////appointment to siteoperator / driver_name for updating Deliver Status
	/*public function Appointment_delivery_status()
    {
        $title = 'Appointment Delivery Status';
        $pagetitle = $title.' Listing';
		$created_by = Auth::user()->role_id;
	
      $entries = Appointment::with(['latestDeliveryStatus:delivery_status_histories.appointment_id,delivery_status,remarks'])
	    ->leftJoin('site_plants', 'appointments.consignee_code', '=', 'site_plants.plant_site_code')

        ->select([
				'appointments.id', 'inv_number', 'inv_doc_date',	'lr_no', 'lr_date',
				'consignor_name', 'consignor_location', 'consignee_code','consignee_name', 
				'consignee_location', 'vendor_name','no_of_cases_sale',
				'truck_type', 'vehicle_no', 'driver_name','driver_no','arrival_date', 'assigned_to_consignee', 'shipment_inv_value', 'appointment_status', 'reason_not_accepting', 'reschedule_date', 'site_plants.site_incharge_contact_no'
			])
			->where('assigned_to_consignee','<>','')
			->where('appointment_status','<>','')
			->where('appointment_status','accepted')
			->whereDoesntHave('latestDeliveryStatus', function ($q) {
				$q->where('delivery_status', 'Delivered');
			})
			->get();
	
	$updatedentries = Appointment::with(['latestDeliveryStatus:delivery_status_histories.appointment_id,delivery_status,remarks', 'podFront', 'podBack'])
	    ->leftJoin('site_plants', 'appointments.consignee_code', '=', 'site_plants.plant_site_code')

        ->select([
				'appointments.id', 'inv_number', 'inv_doc_date',	'lr_no', 'lr_date',
				'consignor_name', 'consignor_location', 'consignee_code','consignee_name', 
				'consignee_location', 'vendor_name','no_of_cases_sale',
				'truck_type', 'vehicle_no', 'driver_name','driver_no','arrival_date', 'assigned_to_consignee', 'shipment_inv_value', 'appointment_status', 'reason_not_accepting', 'reschedule_date', 'site_plants.site_incharge_contact_no'
			])
			->whereHas('latestDeliveryStatus', function ($q) {
			$q->where('delivery_status', 'Delivered');
			})
			->get();
	
		
        return view('admin.appointment.appointment_delivey_status_update',compact(['pagetitle','title','entries','updatedentries']));

    }
	*/
	
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
		$entries = Appointment::with([        'latestDeliveryStatus:delivery_status_histories.appointment_id,delivery_status,remarks',
        'histories:id,appointment_id,delivery_status,delivery_otp,remarks,created_at'
		])
		->leftJoin('site_plants', 'appointments.consignee_code', '=', 'site_plants.plant_site_code')
		->select([
			'appointments.id','inv_number','inv_doc_date','lr_no','lr_date',
			'consignor_name','consignor_location','consignee_code','consignee_name',
			'consignee_location','vendor_name','no_of_cases_sale',
			'truck_type','vehicle_no','driver_name','driver_no','arrival_date',
			'assigned_to_consignee','shipment_inv_value','appointment_status',
			'reason_not_accepting','reschedule_date','site_plants.site_incharge_contact_no'
		])
		->where('assigned_to_consignee', '<>', '')
		->where('appointment_status', 'accepted')
		->whereDoesntHave('latestDeliveryStatus', function ($q) {
			$q->where('delivery_status', 'Delivered');
		});

		// -------------------------
		// BASE QUERY FOR UPDATED ENTRIES
		// -------------------------
		$updatedentries = Appointment::with([
			'latestDeliveryStatus:delivery_status_histories.appointment_id,delivery_status,remarks',
			'podFront','podBack'
		])
		->leftJoin('site_plants', 'appointments.consignee_code', '=', 'site_plants.plant_site_code')
		->select([
			'appointments.id','inv_number','inv_doc_date','lr_no','lr_date',
			'consignor_name','consignor_location','consignee_code','consignee_name',
			'consignee_location','vendor_name','no_of_cases_sale',
			'truck_type','vehicle_no','driver_name','driver_no','arrival_date',
			'assigned_to_consignee','shipment_inv_value','appointment_status',
			'reason_not_accepting','reschedule_date','site_plants.site_incharge_contact_no'
		])
		->whereHas('latestDeliveryStatus', function ($q) {
			$q->where('delivery_status', 'Delivered');
		});

		// -----------------------------------------
		// APPLY FILTER BASED ON USER ROLE / MOBILE
		// -----------------------------------------

		if ($role == 12) {  
			// DRIVER → Show appointments assigned to him
			$entries->where('appointments.driver_no', $mobile);
			$updatedentries->where('appointments.driver_no', $mobile);

		} elseif ($role == 8  || $role==11) {  
			// CONSIGNEE → Match with site incharge number
			$entries->where('site_plants.site_incharge_contact_no', $mobile);
			$updatedentries->where('site_plants.site_incharge_contact_no', $mobile);
		}
		// If Admin → no filter required
		$entries = $entries->get();
		$updatedentries = $updatedentries->get();
		
		return view(
			'admin.appointment.appointment_delivey_status_update',
			compact(['pagetitle','title','entries','updatedentries'])
		);
	}
	
	
	public function updateDeliveryStatus(Request $request, $id)
	{
		$appointment = Appointment::findOrFail($id);

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
			$reportedStatus = DeliveryStatusHistory::where('appointment_id', $appointment->id)
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
		$history = DeliveryStatusHistory::create([
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
        $appointment = Appointment::with('histories')->findOrFail($id);
		
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

		$record = DeliveryStatusHistory::where('appointment_id', $request->id)
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
		$entries = Appointment::with(['podFront', 'podBack'])
			->leftJoin('site_plants', 'appointments.consignee_code', '=', 'site_plants.plant_site_code')
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
		$updatedentries = Appointment::with(['podFront', 'podBack'])
			->leftJoin('site_plants', 'appointments.consignee_code', '=', 'site_plants.plant_site_code')
			->where('assigned_to_consignee', '<>', '')
			->where('appointment_status', '<>', '')
			->whereHas('histories', function ($q) {
				$q->where('delivery_status', 'Delivered');
			});

		// DRIVER — Match appointments.driver_no with login mobile
		if ($role == 12) {
			$entries->where('appointments.driver_no', $mobile);
			$updatedentries->where('appointments.driver_no', $mobile);
		}

		// CONSIGNEE — Match site incharge number + consignee_code is auto-joined
		if ($role == 8 || $role==11) {
			$entries->where('site_plants.site_incharge_contact_no', $mobile);
			$updatedentries->where('site_plants.site_incharge_contact_no', $mobile);
		}

		// FETCH AFTER APPLYING ROLE FILTER
		$entries = $entries->get();
		$updatedentries = $updatedentries->get();

		return view('admin.appointment.podfileupload',
			compact('pagetitle', 'title', 'entries', 'updatedentries')
		);
	}
	
	public function uploadPODFile(Request $request)
	{
		
		try{
		
		$request->validate([
			'appointment_id' => 'required|exists:appointments,id',
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

		$destinationPath = public_path('uploads/pod');
		$file->move($destinationPath, $fileName);

		$relativePath = 'uploads/pod/' . $fileName;

		$podFile = PodFile::create([
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
	
