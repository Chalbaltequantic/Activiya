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
use App\Models\VendorAddress;

use Auth;

class VendorAddressController extends Controller
{
	 //
	public function __construct()
    {
        $this->middleware('auth:admin');     
    }
	
    
	public function index($vendorId)
	{
		$vendor = Vendor::findOrFail($vendorId);
		$addresses = $vendor->addresses;
		return view('admin.vendor_addresses.index', compact('vendor', 'addresses'));
	}
	public function create($vendorId)
    {
        $vendor = Vendor::findOrFail($vendorId);
		
        return view('admin.vendor_addresses.create', compact('vendor'));
    }

    public function store(Request $request, $vendorId)
    {
        $request->validate([
            'address_line1' => 'required',
            'city' => 'required',
            'state' => 'required',
            'zip_code' => 'required',
            'country' => 'required',
        ]);
		//echo $request->address_line1; exit;
		$created_by = Auth::user()->id; 
		
        $vendor = Vendor::findOrFail($vendorId);
		
		$address = new VendorAddress($request->all());
		$address->vendor_id = $vendor->id;
		$address->save();
		
		return redirect()->route('admin.vendor-addresses.index', $vendorId)->with('success', 'Address added successfully.');
       
    }

    public function edit($vendorId, $id)
    {
        $vendor = Vendor::findOrFail($vendorId);
        $address = VendorAddress::findOrFail($id);
        return view('admin.vendor_addresses.edit', compact('vendor', 'address'));
    }

    public function update(Request $request, $vendorId, $id)
    {
        $request->validate([
            'address_line1' => 'required',
            'city' => 'required',
            'state' => 'required',
            'zip_code' => 'required',
            'country' => 'required',
        ]);

        $address = VendorAddress::findOrFail($id);
        $address->update($request->all());

        return redirect()->route('admin.vendor-addresses.index', $vendorId)->with('success', 'Address updated successfully.');
    }

    public function destroy($vendorId, $id)
    {
        VendorAddress::destroy($id);
        return redirect()->route('admin.vendor-addresses.index', $vendorId)->with('success', 'Address deleted successfully.');
    }
}

