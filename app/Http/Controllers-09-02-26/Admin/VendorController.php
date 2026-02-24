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

use App\Models\Vendor;

use Auth;


class VendorController extends Controller
{
    //
	public function __construct()
    {
        $this->middleware('auth:admin');     
    }
	
	public function index()
    {
        Gate::authorize('admin.vendor');
		
		$title = 'Vendors';
        $pagetitle = $title.' Listing';
               
	    $vendorlist = Vendor::orderBy('created_at', 'desc')->get();       
        return view('admin.vendor.index',compact(['pagetitle','title','vendorlist']));
    }
	
	public function getVendorDetails($id)
	{
		$vendordata = Vendor::findOrFail($id);
		$vendorlist = Vendor::orderBy('vendor_name', 'asc')->get();       

		$userid = auth()->user()->id; //get loggedin user id		
		return view('admin.vendor.editvendordata', compact(['vendordata', 'vendorlist']));
	}

	public function save_vendordata(Request $request)
	{
			$id = $request->id;
			$created_by = Auth::user()->id;
			if(!empty($id))
			{
			
				$request->validate([
				'vendor_code' => 'required|unique:vendors,vendor_code,' . $id,
				'vendor_name' => 'required',
				'vendor_type' => 'required',
				
				]);
			
			
			$data = [
					'vendor_code' => $request->vendor_code,
					'vendor_type' => $request->vendor_type,
					'vendor_name' => $request->vendor_name,
					'vendor_short_name' => $request->vendor_short_name,
					'company_code' => $request->company_code,
					'authorized_person_name' => $request->authorized_person_name,
					'authorized_person_phone' => $request->authorized_person_phone,
					'authorized_person_mail' => $request->authorized_person_mail,
					'withholding_tax_type' => $request->withholding_tax_type,
					'tds_section_1' => $request->tds_section_1,
					'receipt_type_1' => $request->receipt_type_1,
					'receipt_name' => $request->receipt_name,
					'withholding_tax_type_2' => $request->withholding_tax_type_2,
					'tds_section_2' => $request->tds_section_2,
					'pan_no' => $request->pan_no,
					'email' => $request->email,
					'gstin_number' => $request->gstin_number,
					'pan_gstin_check' => $request->pan_gstin_check,
					'terms_of_payment_key' => $request->terms_of_payment_key,
					'account_group' => $request->account_group,
					'posting_block_overall' => $request->posting_block_overall,
					'purchase_block_overall' => $request->purchase_block_overall,
					'service_block' => $request->service_block,
					'purchase_shipment_block' => $request->purchase_block_org,
					'updated_at' => Carbon::now(),
					'status' => $request->status,
					'updated_by' => $created_by
				];
				
				if (!is_null($request->parent_id)) {
					$data['parent_id'] = $request->parent_id;
				}
				Vendor::find($id)->update($data);
				
			return Redirect('/admin/vendor')->with('success', 'Vendor data updated successfully!');
			}
			else
			{
				$validatedData = $request->validate(
													[
														'vendor_code' => 'required|unique:vendors,vendor_code',
														'vendor_type' => 'required',
														'vendor_name' => 'required',
														'company_code' => 'required',
													],
													[
														'vendor_code.required' => 'Please enter vendor code',
														'vendor_type.required' => 'Please enter vendor type',
														'vendor_name.required' => 'Please enter vendor name',
														'company_code.required' => 'Please enter company code',
													]
													);
				
				
				$data = [
						'vendor_code' => $request->vendor_code,
						'vendor_type' => $request->vendor_type,
						'vendor_name' => $request->vendor_name,
						'vendor_short_name' => $request->vendor_short_name,
						'company_code' => $request->company_code,
						'authorized_person_name' => $request->authorized_person_name,
						'authorized_person_phone' => $request->authorized_person_phone,
						'authorized_person_mail' => $request->authorized_person_mail,
						'withholding_tax_type' => $request->withholding_tax_type,
						'tds_section_1' => $request->tds_section_1,
						'receipt_type_1' => $request->receipt_type_1,
						'receipt_name' => $request->receipt_name,
						'withholding_tax_type_2' => $request->withholding_tax_type_2,
						'tds_section_2' => $request->tds_section_2,
						'pan_no' => $request->pan_no,
						'email' => $request->email,
						'gstin_number' => $request->gstin_number,
						'pan_gstin_check' => $request->pan_gstin_check,
						'terms_of_payment_key' => $request->terms_of_payment_key,
						'account_group' => $request->account_group,
						'posting_block_overall' => $request->posting_block_overall,
						'purchase_block_overall' => $request->purchase_block_overall,
						'service_block' => $request->service_block,
						'purchase_shipment_block' => $request->purchase_block_org,
						'created_at' => Carbon::now(),
						'status' => $request->status,
						'created_by' => $created_by
					
					];
					
					if (!is_null($request->parent_id)) {
					$data['parent_id'] = $request->parent_id;
					}
					
					Vendor::create($data);
					
					return Redirect('/admin/vendor')->with('success', 'Vendor data updated successfully!');
			}
		
	}
	
	public function AddVendor()
	{	//Gate::authorize('admin.vendor.index');
	
		$vendorlist = Vendor::orderBy('vendor_name', 'asc')->get();       

		return view('admin.vendor.addvendor', compact('vendorlist'));
		
	}

	
	
	public function DeleteVendorData($id)
	{
			$vendor = Vendor::find($id);
			$vendor->addresses()->delete();
			$vendor->bankAccounts()->delete();
			$vendor->delete();
		
		return Redirect('/admin/vendor')->with('success', 'Vendor data deleted successfully!');
	}	
	
	
}
