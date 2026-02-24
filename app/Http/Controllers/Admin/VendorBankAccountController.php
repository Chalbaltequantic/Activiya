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
use App\Models\VendorBankAccount;

use Auth;

class VendorBankAccountController extends Controller
{
     //
	public function __construct()
    {
        $this->middleware('auth:admin');     
    }
	
	public function index($vendorId)
	{
		$vendor = Vendor::findOrFail($vendorId);
		$bankAccounts = $vendor->bankAccounts;
		return view('admin.vendor_bank_accounts.index', compact('vendor', 'bankAccounts'));
	}
	
	
	public function create($vendorId)
    {
        $vendor = Vendor::findOrFail($vendorId);
        return view('admin.vendor_bank_accounts.create', compact('vendor'));
    }

    public function store(Request $request, $vendorId)
    {
        $request->validate([
            'bank_name' => 'required|string',
            'account_number' => 'required|string',
            'ifsc_code' => 'required|string',
            'branch' => 'nullable|string',
        ]);

        $vendor = Vendor::findOrFail($vendorId);
       // $vendor->bankAccounts()->create($request->all());
	   
		$bankAccounts = new VendorBankAccount($request->all());
		$bankAccounts->vendor_id = $vendor->id;
		$bankAccounts->save();

        return redirect()->route('admin.vendor-bank-accounts.index', $vendorId)->with('success', 'Bank account added successfully.');
    }

    public function edit($vendorId, $id)
    {
        $vendor = Vendor::findOrFail($vendorId);
        $bankaccount = VendorBankAccount::findOrFail($id);
        return view('admin.vendor_bank_accounts.edit', compact('vendor', 'bankaccount'));
    }

    public function update(Request $request, $vendorId, $id)
    {
        $request->validate([
            'bank_name' => 'required',
            'account_number' => 'required',
            'ifsc_code' => 'required',
            'branch_name' => 'required',
            'account_holder_name' => 'required',
            'account_type' => 'required',
            'status' => 'required',
        ]);

        $bankAccount = VendorBankAccount::findOrFail($id);
        $bankAccount->update($request->all());

        return redirect()->route('admin.vendor-bank-accounts.index', $vendorId)->with('success', 'Bank account updated successfully.');
    }

    public function destroy($vendorId, $id)
    {
        VendorBankAccount::destroy($id);
        return redirect()->route('admin.vendor-bank-accounts.index', $vendorId)->with('success', 'Bank account deleted successfully.');
    }
}

