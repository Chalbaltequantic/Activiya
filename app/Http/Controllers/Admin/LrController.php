<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Gate;
use App\Models\{Lr,Vendor,VendorAddress,Siteplant};
use Illuminate\Database\QueryException;

use Illuminate\Support\Facades\DB;
use PDF;
use Auth;


class LrController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');     
    }	
	
	public function index()
	{
		
		
		if(!empty(Auth::user()->vendor_code))
		{
			$vendorCode = Auth::user()->vendor_code;
			$vendor = Vendor::where('vendor_code',$vendorCode)->first(); 
		
			$vendorId = $vendor->id;
			$invoices = Lr::where('vendor_id', $vendorId)
					->with(['consignor','consignee','registeredAddress'])
					->orderBy('id','desc')
					->paginate(20);
		}
		else{
			$invoices = Lr::with('consignor','consignee', 'registeredAddress')
				->orderBy('id','desc')
				->paginate(20);
		}
		

		return view('admin.lr.index', compact('invoices'));
	}
	
	public function edit($id)
	{
		$lr = Lr::findOrFail($id);

		if(Auth::user()->vendor_code)
		{
			$vendorCode = Auth::user()->vendor_code;
			$vendor = Vendor::where('vendor_code',$vendorCode)->first();
		}
		else
		{
			$vendor = Vendor::first();
		}
        $registered = $vendor->addresses()
            ->where('address_type','Registered')->first();

        $billing = $vendor->addresses()
            ->where('address_type','Billing')->get();

        $branch = $vendor->addresses()
            ->where('address_type','Branch')->get();

        $plants = Siteplant::all();

		return view('admin.lr.edit', compact('lr','vendor','registered','billing','branch','plants'));
	}
	
	public function create()
    {
		if(Auth::user()->vendor_code)
		{
			$vendorCode = Auth::user()->vendor_code;
			$vendor = Vendor::where('vendor_code',$vendorCode)->first();
		}
		else
		{
			$vendor = Vendor::first();
		}
        $registered = $vendor->addresses()
            ->where('address_type','Registered')->first();

        $billing = $vendor->addresses()
            ->where('address_type','Billing')->get();

        $branch = $vendor->addresses()
            ->where('address_type','Branch')->get();

        $plants = Siteplant::all();

        $invoiceNo = Lr::generateLrinvoiceNumber();

        return view('admin.lr.create',
            compact('vendor','registered','billing','branch','plants','invoiceNo'));
    }

    public function store(Request $request)
	{
		$request->validate([
        'lr_no' => 'required|unique:lrs,lr_no',
        'invoice_no' => 'required'
		],[
			'lr_no.unique' => 'This LR Number already exists. Please enter a different LR No.'
		]);

		
		 
		$vendorCode = Auth::user()->vendor_code;
		$vendor = Vendor::where('vendor_code',$vendorCode)->first(); // simplify login logic
		$registered = $vendor->addresses()
			->where('address_type','Registered')->first();
		

        $billing = $vendor->addresses()
            ->where('address_type','Billing')->first();

        $branch = $vendor->addresses()
            ->where('address_type','Branch')->first();	

		$consignor = Siteplant::find($request->consignor);
		$consignee = Siteplant::find($request->consignee);
		 
		$action = $request->input('action');
		 
		 Lr::create([
        'vendor_id' => $vendor->id,
		'eway_bill_no' =>$request->eway_bill_no ?? null,
        'invoice_no' => $request->invoice_no ?? null,
        'lr_no' => $request->lr_no ?? null,
        'registered_address_id' => $registered->id ?? null,
        'billing_address_id' => $billing->id ?? null,
        'branch_address_id' => $branch->id ?? null,
        'bill_date' => $request->bill_date ?? null,
        'vehicle_no' => $request->vehicle_no ?? null,
        'insurance' => $request->insurance ?? null,
        'fssai_no' => $request->fssai_no ?? null,        
        'gstin' => $request->gstin ?? null, 
        'msme' => $request->msme ?? null,        
        'indent_no' => $request->indent_no ?? null,        
        'caution' => $request->caution ?? null,        
        'notice' => $request->notice ?? null,        
        'consignor_id' => $request->consignor ?? null,
        'consignee_id' => $request->consignee ?? null,
        'origin' => $consignor->city ?? null,
        'destination' => $consignee->city ?? null,
		'consignor' =>$consignor->plant_site_name ?? null,
		'consignee' =>$consignee->plant_site_name ?? null,
        'packages' => $request->packages ?? null,
        'description' => $request->description ?? null,
        'actual_weight' => $request->actual_weight ?? null,
        'charged' => $request->charged ?? null,
        'rate' => $request->rate ?? null,
        'amount' => $request->amount ?? null,
        'invoice_value' => $request->invoice_value ?? null,
        'surcharge' => $request->surcharge ?? null,
        'hamali' => $request->hamali ?? null,
        'risk_charge' => $request->risk_charge ?? null,
        'b_charge' => $request->b_charge ?? null,
        'other_charge' => $request->other_charge ?? null,
        'total_amount' => $request->total_amount ?? null,
        'invoice_date' => $request->invoice_date ?? null,
        'arrival_date' => $request->arrival_date ?? null,
        'dispatch_date' => $request->dispatch_date ?? null,
        'truck_type' => $request->truck_type ?? null,
		'status' => $action == 'final' ? 'final' : 'draft'
    ]);

		//return redirect()->route('admin.lr.list');
		return redirect()->route('admin.lr.list')
        ->with('success', $action == 'final' ? 'LR Final Saved' : 'LR Saved as Draft');
	}
	
	public function update(Request $request, $id)
	{
		$lr = Lr::findOrFail($id);

		
		if ($lr->status === 'final') {
			return redirect()->back()->with('error', 'Final LR cannot be edited.');
		}

	
		$request->validate([
			'invoice_no' => 'required',
		]);

		
		$status = $request->action === 'final' ? 'final' : 'draft';
		$consignor = Siteplant::find($request->consignor);
		$consignee = Siteplant::find($request->consignee);
		
		$lr->update([
		
		'eway_bill_no' =>$request->eway_bill_no ?? null,
        'billing_address_id' => $billing->id ?? null,
        'branch_address_id' => $branch->id ?? null,
        'bill_date' => $request->bill_date ?? null,
        'vehicle_no' => $request->vehicle_no ?? null,
        'insurance' => $request->insurance ?? null,        
        'indent_no' => $request->indent_no ?? null, 
        'consignor_id' => $request->consignor ?? null,
        'consignee_id' => $request->consignee ?? null,
        'origin' => $consignor->city ?? null,
        'destination' => $consignee->city ?? null,
		'consignor' =>$consignor->plant_site_name ?? null,
		'consignee' =>$consignee->plant_site_name ?? null,
        'packages' => $request->packages ?? null,
        'description' => $request->description ?? null,
        'actual_weight' => $request->actual_weight ?? null,
        'charged' => $request->charged ?? null,
        'rate' => $request->rate ?? null,
        'amount' => $request->amount ?? null,
        'invoice_value' => $request->invoice_value ?? null,
        'surcharge' => $request->surcharge ?? null,
        'hamali' => $request->hamali ?? null,
        'risk_charge' => $request->risk_charge ?? null,
        'b_charge' => $request->b_charge ?? null,
        'other_charge' => $request->other_charge ?? null,
        'total_amount' => $request->total_amount ?? null,
        'invoice_date' => $request->invoice_date ?? null,
        'arrival_date' => $request->arrival_date ?? null,
        'dispatch_date' => $request->dispatch_date ?? null,
        'truck_type' => $request->truck_type ?? null,
		'status' => $status
		]);

		return redirect()->route('admin.lr.list')
			->with('success', 'LR updated successfully as ' . strtoupper($status));
	}
   
	public function pdf($id)
	{
		$user = Auth::user();
		
		$invoice = LR::with([
			'vendor',
			'consignor','consignee',
			'registeredAddress'
		])->findOrFail($id);

		// Authorization logic
		if ($user->role->name !== 'SuperAdmin') {

			if (!$user->vendor_code || 
				$invoice->vendor->vendor_code !== $user->vendor_code) {

				abort(403, 'Unauthorized');
			}
		}

		$vendor = $invoice->vendor;
		
		
		 $consignor = DB::table('site_plants')
        ->where('id', $invoice->consignor_id)
        ->first();

		$consignee = DB::table('site_plants')
        ->where('id', $invoice->consignee_id)
        ->first();

		return PDF::loadView('admin.lr.pdf', compact('invoice','vendor','consignor', 'consignee'))
			->setPaper('A4', 'portrait')
			->stream('lr-'.$invoice->invoice_no.'.pdf');
	}
	
	
	
}
