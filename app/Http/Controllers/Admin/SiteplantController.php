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

use App\Models\Siteplant;

use Auth;


class SiteplantController extends Controller
{
    //
	public function __construct()
    {
        $this->middleware('auth:admin');     
    }
	
	public function index(Request $request)
    {
        $title = 'Site Plant Data Upload';
        $pagetitle = $title.' Listing';
       
		$data = $request->all();        
	    $siteplantlist = Siteplant::orderBy('created_at', 'desc')->get();       
        return view('admin.siteplant.index',compact(['pagetitle','title','siteplantlist']));
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


        DB::beginTransaction();
        try {
            foreach ($rows as $index => $row) {
                if ($index == 1) continue; // skip header row

				$s5_d5_short_name = $row['B']."-".$row['A']."-".$row['I'];

                $data = [
                    'plant_site_code' => $row['A'] ?? null,
                    'plant_site_location_name' => $row['B'] ?? null,
                    'site_code' => $row['C'] ?? null,
                    'status' => $row['D'] ?? null,
                    'plant_site_name' => $row['E'] ?? null,
                    'street_house_number' => $row['F'] ?? null,
                    'street1' => $row['G'] ?? null,
                    'street2' => $row['H'] ?? null,
                    'city' => $row['I'] ?? null,
                    'post_code' => $row['J'] ?? null,
                    'state_code' => $row['K'] ?? null,
                    'state_name' => $row['L'] ?? null,
                    'pan_no' => $row['M'] ?? null,
                    'food_license_no' => $row['N'] ?? null,
                    'food_license_expiry' => $row['O'] ?? null,
                    'site_executive_name' => $row['P'] ?? null,
                    'site_executive_contact_no' => $row['Q'] ?? null,
                    'site_executive_mail_id' => $row['R'] ?? null,
                    'site_incharge_name' => $row['S'] ?? null,
                    'site_incharge_contact_no' => $row['T'] ?? null,
                    'site_incharge_mail_id' => $row['U'] ?? null,
                    'site_manager_name' => $row['V'] ?? null,
                    'site_manager_contact_no' => $row['W'] ?? null,
                    'site_manager_mail_id' => $row['X'] ?? null,
                    'region' => $row['Y'] ?? null,
                    'company_code' => $row['Z'] ?? null,
                    'company_type' => $row['AA'] ?? null,
                    's5_d5_short_name' => $s5_d5_short_name ?? null,
                    'created_at' => $createddate,
                    'created_by' => Auth::user()->id
                ];
                // Check for duplicate using ref1, ref3, lr_no
              /*  $exists = Billdata::where('ref1', $data['ref1'])
                    ->where('ref3', $data['ref3'])
                    ->where('lr_no', $data['lr_no'])
                    ->exists();
					*/
				
				$exists = Siteplant::where('plant_site_code', $data['plant_site_code'])
						->exists();	

                if (!$exists) {
                    Siteplant::create($data);
                }
            }

            DB::commit();
            return redirect()->back()->with('success', 'Excel imported successfully!');
        } 
		catch (\Exception $e) 
		{
            DB::rollback();
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
	
	public function getSiteplantdataDetails($id)
	{
		$siteplantdata = Siteplant::find($id);
		$userid = auth()->user()->id; //get loggedin user id		
		return view('admin.siteplant.editsiteplant', compact('siteplantdata'));
	}

	public function save_siteplantdata(Request $request)
	{
				
		$validatedData = $request->validate(
			[
				'plant_site_code' => 'required',
				'plant_site_location_name' => 'required',
				'site_code' => 'required',
			],
			[
				'plant_site_code.required' => 'Please enter plant site code',
				'plant_site_location_name.required' => 'Please enter plant site location name',
				'site_code.required' => 'Please enter site code',
			]
		);
		
			$createddate = date('Y-m-d');
			$id = $request->id;
			
			$s5_d5_short_name = $request->plant_site_location_name."-".$request->plant_site_code."-".$request->city;
			
			Siteplant::find($id)->update([
				 'plant_site_code' => $request->plant_site_code,
                    'plant_site_location_name' => $request->plant_site_location_name,
                    'site_code' => $request->site_code,
                   // 'site_status' => $request->site_status,
                    'plant_site_name' => $request->plant_site_name,
                    'street_house_number' => $request->street_house_number,
                    'street1' => $request->street1,
                    'street2' => $request->street2,
                    'city' => $request->city,
                    'post_code' => $request->post_code,
                    'state_code' => $request->state_code,
                    'state_name' => $request->state_name,
                    'pan_no' => $request->pan_no,
                    'food_license_no' => $request->food_license_no,
                    'food_license_expiry' => $request->food_license_expiry,
                    'site_executive_name' => $request->site_executive_name,
                    'site_executive_contact_no' => $request->site_executive_contact_no,
                    'site_executive_mail_id' => $request->site_executive_mail_id,
                    'site_incharge_name' => $request->site_incharge_name,
                    'site_incharge_contact_no' => $request->site_incharge_contact_no,
                    'site_incharge_mail_id' => $request->site_incharge_mail_id,
                    'site_manager_name' => $request->site_manager_name,
                    'site_manager_contact_no' => $request->site_manager_contact_no,
                    'site_manager_mail_id' => $request->site_manager_mail_id,
                    'region' => $request->region,
                    'company_code' => $request->company_code,
                    'company_type' => $request->company_type,
					's5_d5_short_name' => $s5_d5_short_name,
                    'updated_at' => $createddate,
                    'updated_by' => Auth::user()->id,
                    'status' => $request->status
					]);
			return Redirect('/admin/siteplant')->with('success', 'Site plant data updated successfully!');
		
	}
	
	
	public function DeleteSiteplantData($id)
	{
		$siteplant = Siteplant::find($id);		
		Siteplant::find($id)->delete();
		return Redirect('/admin/siteplant')->with('success', 'Siteplant date deleted successfully!');
	}
	
	
	//manual Upload
	
	public function manualupload()
	{
		$userid = auth()->user()->id; //get loggedin user id		
		return view('admin.siteplant.manualupload_siteplant');
	}

	public function save_manual_siteplantdata(Request $request)
	{
		
		$created_by = Auth::user()->id; 
		
		$createddate = date('Y-m-d');
		
		
		$plant_site_code = $request->input('plant_site_code', []);
		$plant_site_location_name = $request->input('plant_site_location_name', []);
		$site_code = $request->input('site_code', []);
		//$site_status = $request->input('site_status', []);
		$plant_site_name = $request->input('plant_site_name', []);
		$street_house_number= $request->input('street_house_number', []);
		$street1= $request->input('street1', []);
		$street2= $request->input('street2', []);
		$city= $request->input('city', []);
		$post_code= $request->input('post_code', []);
		$state_code= $request->input('state_code', []);
		$state_name= $request->input('state_name', []);
		$pan_no= $request->input('pan_no', []);
		$food_license_no= $request->input('food_license_no', []);
		$food_license_expiry= $request->input('food_license_expiry', []);
		$site_executive_name= $request->input('site_executive_name', []);
		$site_executive_contact_no= $request->input('site_executive_contact_no', []);
		$site_executive_mail_id= $request->input('site_executive_mail_id', []);
		$site_incharge_name= $request->input('site_incharge_name', []);
		$site_incharge_contact_no= $request->input('site_incharge_contact_no', []);
		$site_incharge_mail_id= $request->input('site_incharge_mail_id', []);
		$site_manager_name= $request->input('site_manager_name', []);
		$site_manager_contact_no= $request->input('site_manager_contact_no', []);
		$site_manager_mail_id= $request->input('site_manager_mail_id', []);
		$region= $request->input('region', []);
		$company_code= $request->input('company_code', []);
		$company_type= $request->input('company_type', []);
		$created_at= $createddate;
		$created_by= Auth::user()->id;
		$status= $request->input('site_status', []);
		
		$count = count($plant_site_code);

        DB::beginTransaction();
        try {
            for ($i = 0; $i < $count; $i++) {

				if(!empty($plant_site_code[$i]))
				{
			
					$s5_d5_short_name = $plant_site_location_name[$i]."-".$plant_site_code[$i]."-".$city[$i];
		
					$data = [
								'plant_site_code' => $plant_site_code[$i] ?? null,
								'plant_site_location_name' => $plant_site_location_name[$i] ?? null,
								'site_code' => $site_code[$i] ?? null,
								'site_status' => $site_status[$i] ?? null,
								'plant_site_name' => $plant_site_name[$i] ?? null,
								'street_house_number' => $street_house_number[$i] ?? null,
								'street1' => $street1[$i] ?? null,
								'street2' =>$street2[$i] ?? null,
								'city' => $city[$i] ?? null,
								'post_code' => $post_code[$i] ?? null,
								'state_code' => $state_code[$i] ?? null,
								'state_name' => $state_name[$i] ?? null,
								'pan_no' => $pan_no[$i] ?? null,
								'food_license_no' => $food_license_no[$i] ?? null,
								'food_license_expiry' => $food_license_expiry[$i] ?? null,
								'site_executive_name' => $site_executive_name[$i] ?? null,
								'site_executive_contact_no' => $site_executive_contact_no[$i] ?? null,
								'site_executive_mail_id' => $site_executive_mail_id[$i] ?? null,
								'site_incharge_name' =>$site_incharge_name[$i] ?? null,
								'site_incharge_contact_no' => $site_incharge_contact_no[$i] ?? null,
								'site_incharge_mail_id' => $site_incharge_mail_id[$i] ?? null,
								'site_manager_name' => $site_manager_name[$i] ?? null,
								'site_manager_contact_no' => $site_manager_contact_no[$i] ?? null,
								'site_manager_mail_id' => $site_manager_mail_id[$i] ?? null,
								'region' => $region[$i] ?? null,
								'company_code' => $company_code[$i] ?? null,
								'company_type' => $company_type[$i] ?? null,
								's5_d5_short_name' => $s5_d5_short_name ?? null,
								'created_at' => $createddate,
								'created_by' => Auth::user()->id,
								'status' => $status[$i] ?? null,
							];
				

                // Check for duplicate using ref1, ref3, lr_no
              /*  $exists = Billdata::where('ref1', $data['ref1'])
                    ->where('ref3', $data['ref3'])
                    ->where('lr_no', $data['lr_no'])
                    ->exists();
					*/
				
					//$exists = Siteplant::where('lr_no', $data['lr_no'])
					//	->exists();	

					//if (!$exists) {
						Siteplant::create($data);
					//}
				}
            }

            DB::commit();
            return redirect()->back()->with('success', 'Data imported successfully!');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
			//return redirect()->back()->with('error', 'Error: Something went wrong.');
        }
		
	}
	
}
