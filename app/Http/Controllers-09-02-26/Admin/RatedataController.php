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

use App\Models\Ratedata;

use Auth;


class RatedataController extends Controller
{
    //
	public function __construct()
    {
        $this->middleware('auth:admin');     
    }
	
	public function index(Request $request)
    {
        $title = 'Rate Data Upload';
        $pagetitle = $title.' Listing';
       
		$data = $request->all();        
	    $ratedatalist = Ratedata::orderBy('created_at', 'desc')->get();       
        return view('admin.ratedata.index',compact(['pagetitle','title','ratedatalist']));
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
				if (count(array_filter($row, fn($value) => trim((string)$value) !== '')) === 0) {
						continue;
					}
				$validity_start = $row['P'];
				$validity_end = $row['Q'];

				$validity_start_date = Carbon::parse($validity_start)->format('Y-m-d');
				$validity_end_date = Carbon::parse($validity_end)->format('Y-m-d');
				
				$a_amount = preg_replace("/,+/", "", $row['O']);

                $data = [
                    'consignor_name' => $row['A'] ?? null,
                    'consignor_code' => $row['B'] ?? null,
                    'consignor_location' => $row['C'] ?? null,
                    's5_consignor_short_name_and_location' => $row['D'] ?? null,
                    'consignee_name' => $row['E'] ?? null,
                    'consignee_code' => $row['F'] ?? null,
                    'consignee_location' => $row['G'] ?? null,
                    'd5_consignor_short_name_and_location' => $row['H'] ?? null,
                    'mode' => $row['I'] ?? null,
                    'logic' => $row['J'] ?? null,
                    'vendor_code' => $row['K'] ?? null,
                    'vendor_name' => $row['L'] ?? null,
                    't_code' => $row['M'] ?? null,
                    'truck_type' => $row['N'] ?? null,
					'a_amount' => $a_amount ?? null,
                    'validity_start' => isset($validity_start_date) ? $validity_start_date : null,
                    'validity_end' => isset($validity_end_date) ? $validity_end_date : null,
                    'tat' => $row['R'] ?? null,
                    'rank' => $row['S'] ?? null,
                    'distance' => $row['T'] ?? null,
                    'custom1' => $row['U'] ?? null,
                    'custom2' => $row['V'] ?? null,
                    'custom3' => $row['W'] ?? null,
                    'custom4' => $row['X'] ?? null,
                    'custom5' => $row['Y'] ?? null,
                    'created_at' => $createddate,
                    'created_by' => Auth::user()->id,
                    'status' => '1'
                ];

                // Check for duplicate using ref1, ref3, lr_no
              /*  $exists = Billdata::where('ref1', $data['ref1'])
                    ->where('ref3', $data['ref3'])
                    ->where('lr_no', $data['lr_no'])
                    ->exists();
					
				
				$exists = Ratedata::where('lr_no', $data['lr_no'])
                    ->exists();	*/

               // if (!$exists) {
                    Ratedata::create($data);
               // }
            }

				DB::commit();
				return redirect()->back()->with('success', 'Excel imported successfully!');
			} catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
	
	public function getRatedataDetails($id)
	{
		$ratedata = Ratedata::find($id);
		$userid = auth()->user()->id; //get loggedin user id		
		return view('admin.ratedata.editratedata', compact('ratedata'));
	}

	public function save_ratedata(Request $request)
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
			Ratedata::find($id)->update([
				'consignor_name' => $request->consignor_name,
				'consignor_code' => $request->consignor_code,
				'consignor_location' => $request->consignor_location,
				's5_consignor_short_name_and_location' => $request->s5_consignor_short_name_and_location,
				'consignee_name' => $request->consignee_name,
				'consignee_code' => $request->consignee_code,
				'consignee_location' => $request->consignee_location,
				'd5_consignor_short_name_and_location' => $request->d5_consignor_short_name_and_location,
				't_code' => $request->t_code,
				'truck_type' => $request->truck_type,
				'a_amount' => $request->a_amount,
				'validity_start' => $request->validity_start,
				'validity_end' => $request->validity_start,				
				'tat' => $request->tat,
				'rank' => $request->rank,
				'mode' => $request->mode,
				'logic' => $request->logic,
				'distance' => $request->distance,
				'custom1' => $request->custom1,
				'custom2' => $request->custom2,
				'custom3' => $request->custom3,
				'custom4' => $request->custom4,
				'custom5' => $request->custom5,
				'updated_at' => Carbon::now(),
				'status' => $request->status,
			]);
			return Redirect('/admin/ratedata')->with('success', 'Rate data updated successfully!');
		
	}
	
	
	public function DeleteRateData($id)
	{
		$ratedata = Ratedata::find($id);		
		Ratedata::find($id)->delete();
		return Redirect('/admin/ratedata')->with('success', 'Rate date deleted successfully!');
	}
	
	
	//manual Upload	
	public function manualupload()
	{
		$userid = auth()->user()->id; //get loggedin user id		
		return view('admin.ratedata.manualuploadratedata');
	}

	public function save_manual_ratedata(Request $request)
	{
		
		$created_by = Auth::user()->id; 
		
		$createddate = date('Y-m-d');
		$consignor_name     = $request->input('consignor_name', []);		
		$consignor_code     = $request->input('consignor_code', []);
		$consignor_location = $request->input('consignor_location', []);
		$s5_consignor_short_name_and_location = $request->input('s5_consignor_short_name_and_location', []);
		$consignee_name = $request->input('consignee_name', []);
		$consignee_code = $request->input('consignee_code', []);
		$consignee_location = $request->input('consignee_location', []);
		$d5_consignor_short_name_and_location = $request->input('d5_consignor_short_name_and_location', []);
		$t_code = $request->input('t_code', []);
		$truck_type = $request->input('truck_type', []);		
		$vendor_code = $request->input('vendor_code', []);
		$vendor_name = $request->input('vendor_name', []);
		$amount = $request->input('a_amount', []);
		$mode = $request->input('mode', []);
		$logic = $request->input('logic', []);
		$validate_start = $request->input('validity_start', []);
		$validate_end = $request->input('validity_start', []);
		$tat = $request->input('tat', []);
		$rank = $request->input('rank', []);
		$distance = $request->input('distance', []);
		$custom1 = $request->input('custom1', []);
		$custom2 = $request->input('custom2', []);
		$custom3 = $request->input('custom3', []);
		$custom4 = $request->input('custom4', []);
		$custom5 = $request->input('custom5', []);
		
		//print_r($consignor_name); exit;
		
		$count = count($consignor_name);

        DB::beginTransaction();
        try {
            for ($i = 0; $i < $count; $i++) {

               
				$startdate = $validate_start[$i];
				
				$validate_start_date = Carbon::parse($startdate)->format('Y-m-d');
				
				$enddate = $validate_end[$i];
				
				$validate_end_date = Carbon::parse($enddate)->format('Y-m-d');
				
				$a_amount = preg_replace("/,+/", "", $amount[$i]);
				if(!empty($consignor_name[$i]))
				{
				
					$data = [
						'consignor_name' => $consignor_name[$i] ?? null,
						'consignor_code' => $consignor_code[$i] ?? null,
						'consignor_location' => $consignor_location[$i] ?? null,
						's5_consignor_short_name_and_location' => $s5_consignor_short_name_and_location[$i] ?? null,
						'consignee_name' => $consignee_name[$i] ?? null,
						'consignee_code' => $consignee_code[$i] ?? null,
						'consignee_location' => $consignee_location[$i] ?? null,
						'd5_consignor_short_name_and_location' => $d5_consignor_short_name_and_location[$i] ?? null,
						't_code' => $t_code[$i] ?? null,
						'truck_type' => $truck_type[$i] ?? null,
						'a_amount' => $a_amount ?? null,
						'vendor_code' => $vendor_code[$i] ?? null,
						'vendor_name' => $vendor_name[$i] ?? null,						
						'validity_start' => $validate_start_date ?? null,
						'validity_end' =>  $validate_end_date ?? null,
						'mode' => $mode[$i] ?? null,
						'logic' => $logic[$i] ?? null,
						'tat' => $tat[$i] ?? null,
						'rank' => $rank[$i] ?? null,
						'distance' => $distance[$i] ?? null,
						'custom1' => $custom1[$i] ?? null,
						'custom2' => $custom2[$i] ?? null,
						'custom3' => $custom3[$i] ?? null,
						'custom4' => $custom4[$i] ?? null,
						'custom5' => $custom5[$i] ?? null,
						'created_at' => $createddate,
						'created_by' => Auth::user()->id,
						'status' => '1'
					];
				

                // Check for duplicate using ref1, ref3, lr_no
              /*  $exists = Billdata::where('ref1', $data['ref1'])
                    ->where('ref3', $data['ref3'])
                    ->where('lr_no', $data['lr_no'])
                    ->exists();
					*/
				
					//$exists = Billdata::where('lr_no', $data['lr_no'])
					//	->exists();	
					
					  /*  if ($exists) {
							$duplicateRows[] = [
								'Row' => $data['ref1'],
								'ref1' => $ref1,
								'ref3' => $data['ref3'],
								'lr_no' => $data['lr_no'],
							];
							continue;
						}	*/

					//if (!$exists) {
						Ratedata::create($data);
					//}
				}
            }

            DB::commit();
            return redirect()->back()->with('success', 'Data imported successfully!');
			//return view('import')->with(['success' => 'Import completed.','duplicates' => $duplicateRows]);
			
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
			//return redirect()->back()->with('error', 'Error: Something went wrong.');
        }
		
	}
	
}
