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

use App\Models\Siteplant;
use App\Models\ConsigneeReturnDuration;
use Auth;

class ConsigneeReturnDurationController  extends Controller
{
    //
	public function __construct()
    {
        $this->middleware('auth:admin');     
    }
	
	public function index(Request $request)
    {
        $title = 'Data Upload';
        $pagetitle = $title.' Listing';
              
        return view('admin.consignee_return_duration.index',compact(['pagetitle','title']));
    }
	//returndatalist
	public function Returndatalist(Request $request)
    {
        $title = 'Return duration';
        $pagetitle = $title.' Listing';
       $user_role = Auth::user()->role_id;
		$data = $request->all();        
	    $durations = ConsigneeReturnDuration::orderBy('created_at', 'desc')->get();       
        return view('admin.consignee_return_duration.returndatalist',compact(['pagetitle','title','durations','user_role']));
    }
	public function getreturndataDetails($id)
	{
		$returndata = ConsigneeReturnDuration::find($id);
		$userid = auth()->user()->id; //get loggedin user id		
		return view('admin.consignee_return_duration.editreturndata', compact('returndata'));
	}

	public function save_mappingdata(Request $request)
	{
		$validatedData = $request->validate(
			[
				'consignee_code' => 'required',
				'consignee_name' => 'required',
				'return_time_minutes' => 'required',
				'end_date' => 'required',
				'status' => 'required',
			],
			[
				'consignee_code.required' => 'Please enter consignee code',
				'consignee_name.required' => 'Please enter consignee name',
				'return_time_minutes' => 'Please enter retuen time in number',
				'end_date' => 'Please select date',
				'status.required' => 'Please Select status',
			]
		);
			$id = $request->id;
			ConsigneeReturnDuration::find($id)->update([
				'consignee_code' => $request->consignee_code,
				'consignee_name' => $request->consignee_name,
				'return_time_minutes' => $request->return_time_minutes,
				'end_date' => $request->end_date,
				'subvendor_code' => $request->subvendor_code,
				'updated_at' => Carbon::now(),
				'status' => $request->status,
			]);
			return Redirect('/admin/consignee-return-duration/data-list')->with('success', 'Return duration data updated successfully!');
		
	}
	
	
	public function DeleteReturnData($id)
	{
		
		$duration = ConsigneeReturnDuration::findOrFail($id);
		if($duration)
		{
			$duration->delete();
			return Redirect('/admin/consignee-return-duration/data-list')->with('success', 'Return duration data deleted successfully!');
		}
		else
		{
			return Redirect('/admin/consignee-return-duration/data-list')->with('error', 'Return duration data not found!');
		}
		
	}
	
	
	//manual Upload
	
	public function manualupload()
	{
		$userid = auth()->user()->id; //get loggedin user id		
		return view('admin.consignee_return_duration.manualupload');
	}

	public function save_manual_returndata(Request $request)
	{
		
		$created_by = Auth::user()->id; 		
		$createddate = date('Y-m-d');
	
		$consignee_code = $request->input('consignee_code', []);
		$consignee_name    = $request->input('consignee_name', []);
		$return_time_minutes     = $request->input('return_time_minutes', []);
		
		$end_date = $request->input('end_date', []);
		$count = count($consignee_code);

        DB::beginTransaction();
        try {
            for ($i = 0; $i < $count; $i++) {

				if(!empty($consignee_code[$i]))
				{
				
					$data = [
						'consignee_code' => $consignee_code ?? null,
						'consignee_name' => $consignee_name[$i] ?? null,
						'return_time_minutes' => $return_time_minutes[$i] ?? null,
						'end_date' => $end_date[$i] ?? null,
						'created_at' => $createddate,
						'created_by' => Auth::user()->id,
						'status' => '1'
					];
				
				
				ConsigneeReturnDuration::create($data);
					
				}
            }

            DB::commit();
           return Redirect('/admin/consignee-return-duration/data-list')->with('success', 'Data imported successfully!');
			//return view('import')->with(['success' => 'Import completed.','duplicates' => $duplicateRows]);
			
        } catch (\Exception $e) {
            DB::rollback();
            //return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
			return redirect()->back()->with('error', 'Error: Something went wrong.');
        }
		
	}
	
}
	
