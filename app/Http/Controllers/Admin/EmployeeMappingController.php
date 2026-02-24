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

use App\Models\EmployeeMapping;

use Auth;


class EmployeeMappingController extends Controller
{
    //
	public function __construct()
    {
        $this->middleware('auth:admin');     
    }
	
	public function index(Request $request)
    {
        $title = 'Employee Mapping Data Upload';
        $pagetitle = $title.' Listing';
       
		$data = $request->all();        
	    $mappingdatalist = EmployeeMapping::orderBy('created_at', 'desc')->get();       
        return view('admin.employeemapping.index',compact(['pagetitle','title','mappingdatalist']));
    }
	//mappingdatalist
	public function employeemappingdatalist(Request $request)
    {
        $title = 'Employee Mapping Data ';
        $pagetitle = $title.' Listing';
        $user_role = Auth::user()->role_id;
		$data = $request->all();        
	    $mappingdatalist = EmployeeMapping::orderBy('created_at', 'desc')->get();       
        return view('admin.employeemapping.mappingdatalist',compact(['pagetitle','title','mappingdatalist','user_role']));
    }
	public function getEmployeeMappingdataDetails ($id)
	{
		$mappingdata = EmployeeMapping::find($id);
		$userid = auth()->user()->id; //get loggedin user id		
		return view('admin.employeemapping.editmappingdata', compact('mappingdata'));
	}

	public function save_employeemappingdata(Request $request)
	{
		$validatedData = $request->validate(
			[
				'company_code' => 'required',
				'consignor_code' => 'required',
				'consignee_code' => 'required',
				'vendor_code' => 'required',
				'employee_code' => 'required',
				'status' => 'required',
			],
			[
				'company_code.required' => 'Please enter company code',
				'consignor_code.required' => 'Please enter consignor code',
				'consignee_code.required' => 'Please enter consignee code',
				'vendor_code.required' => 'Please enter vendor code',
				'employee_code.required' => 'Please enter employee code',
				'status.required' => 'Please Select status',
			]
		);
			$id = $request->id;
			EmployeeMapping::find($id)->update([
				'company_code' => $request->consignor_code,
				'consignor_code' => $request->consignor_code,
				'consignee_code' => $request->consignee_code,
				'vendor_code' => $request->vendor_code,
				'subvendor_code' => $request->subvendor_code,
				'employee_code' => $request->employee_code,
				'updated_at' => Carbon::now(),
				'status' => $request->status,
			]);
			return Redirect('/admin/employeemapping/list')->with('success', 'Mapping data updated successfully!');
		
	}
	
	
	public function DeleteEmployeeMappingData($id)
	{
		//$mappingdata = EmployeeMapping::find($id);		
		EmployeeMapping::find($id)->delete();
		return Redirect('/admin/employeemapping/list')->with('success', 'Mapping data deleted successfully!');
	}
	
	
	//manual Upload
	
	public function manualupload()
	{
		$userid = auth()->user()->id; //get loggedin user id		
		return view('admin.employeemapping.manualupload');
	}

	public function save_manual_mappingdata(Request $request)
	{
		
		$created_by = Auth::user()->id; 
		
		$createddate = date('Y-m-d');
		$operation_type =  $request->input('operation_type');
		
		$company_code    = $request->input('company_code', []);
		$consignor_code     = $request->input('consignor_code', []);
		$consignee_code = $request->input('consignee_code', []);
		$vendor_code = $request->input('vendor_code', []);
		$subvendor_code = $request->input('subvendor_code', []);
		$employee_code = $request->input('employee_code', []);
		
		$count = count($consignor_code);

        DB::beginTransaction();
        try {
            for ($i = 0; $i < $count; $i++) {

				if(!empty($consignor_code[$i]))
				{
				
					$data = [
						'operation_type' => $operation_type ?? null,
						'company_code' => $company_code[$i] ?? null,
						'consignor_code' => $consignor_code[$i] ?? null,
						'consignee_code' => $consignee_code[$i] ?? null,
						'vendor_code' => $vendor_code[$i] ?? null,
						'subvendor_code' => $subvendor_code[$i] ?? null,
						'employee_code' => $employee_code[$i] ?? null,
						'created_at' => $createddate,
						'created_by' => Auth::user()->id,
						'status' => '1'
					];
				

                // Check for duplicate using ref1, ref3, lr_no
              /*  $exists = EmployeeMapping::where('company_code', $data['company_code'])
                    ->where('consigner_code', $data['consigner_code'])
                    ->where('consignee_code', $data['consignee_code'])
                    ->where('ve_code', $data['consignee_code'])
                    ->exists();
					*/
				
				EmployeeMapping::create($data);
					
				}
            }

            DB::commit();
           return Redirect('/admin/employeemapping/list')->with('success', 'Data uploaded successfully!');
			//return view('import')->with(['success' => 'Import completed.','duplicates' => $duplicateRows]);
			
        } catch (\Exception $e) {
            DB::rollback();
            //return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
			return redirect()->back()->with('error', 'Error: Something went wrong.');
        }
		
	}
	
}
