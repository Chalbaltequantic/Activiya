<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Gate;
use App\Models\{Invoice,InvoiceItem,Vendor,VendorAddress,Siteplant,InvoiceAnnexure};

use Illuminate\Support\Facades\DB;
use PDF;
use Auth;


class InvoiceController extends Controller
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
			$invoices = Invoice::where('vendor_id', $vendorId)
				->with('items','sitePlant', 'registeredAddress', 'billingAddress', 'branchAddress')
				->orderBy('id','desc')
				->paginate(20);
		}
		else{
			$invoices = Invoice::with('items','sitePlant', 'registeredAddress', 'billingAddress', 'branchAddress')
				->orderBy('id','desc')
				->paginate(20);
		}
		

		return view('admin.invoice.index', compact('invoices'));
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

        $invoiceNo = Invoice::generateInvoiceNumber();

        return view('admin.invoice.create',
            compact('vendor','registered','billing','branch','plants','invoiceNo'));
    }

    public function store(Request $request)
	{
		$request->validate([
			'billing_address_id'=>'required',
			'branch_address_id'=>'required',
			'items'=>'required|array|min:1'
			
		]);

		DB::transaction(function() use ($request){

			$vendorCode = Auth::user()->vendor_code;
			$vendor = Vendor::where('vendor_code',$vendorCode)->first(); // simplify login logic
			$registered = $vendor->addresses()
				->where('address_type','Registered')->first();

			$plant = Siteplant::find($request->site_plant_id);

			$gstType = ($registered->state == $plant->state)
				? 'CGST_SGST' : 'IGST';
			
			$action = $request->input('action', 'draft');
			
			$invoice = Invoice::create([
				'vendor_id'=>$vendor->id,
				'site_plant_id'=>$plant->id,
				'invoice_no'=>Invoice::generateInvoiceNumber(),
				'bill_date'=>$request->bill_date,
				'gst_type'=>$gstType,
				'registered_address_id'=>$registered->id,
				'billing_address_id'=>$request->billing_address_id,
				'branch_address_id'=>$request->branch_address_id,
				'indent_id'=>$request->indent_id,
				'status' => $action === 'final' ? 'final' : 'draft',
			]);

		
			foreach($request->items as $item){

				$gstAmount = $item['taxable'] * ($item['gst']/100);

				if($gstType=='CGST_SGST'){
					$cgst=$gstAmount/2; $sgst=$gstAmount/2; $igst=0;
				}else{
					$cgst=0; $sgst=0; $igst=$gstAmount;
				}

				InvoiceItem::create([
					'invoice_id'=>$invoice->id,
					'lr_no'=>$item['lr_no'],
					'lr_date'=>$item['lr_date'],
					'vehicle_dispatch_date'=>$item['vehicle_dispatch_date'],
					'from_location'=>$item['from'],
					'to_location'=>$item['to'],
					'po_no'=>$item['po_no'],
					'description'=>$item['description']?? '',
					'taxable'=>$item['taxable'],
					'base_freight'=>$item['base_freight'],
					'gst_percent'=>$item['gst'],
					'cgst'=>$cgst,
					'sgst'=>$sgst,
					'igst'=>$igst,
					'total'=>$item['taxable']+$gstAmount
				]);
			}

			
			if ($request->has('annexures') && is_array($request->annexures)) 
			{
				foreach($request->annexures as $a){

					/*Skip the rows which is empty*/
					if (collect($a)->filter()->isEmpty()) {
					continue;
					}
					InvoiceAnnexure::create([
						'invoice_id'=>$invoice->id,
						'customer_ref_no'=>$a['customer_ref_no'] ?? null,
						'obd_no'=>$a['obd_no'] ?? null,
						'arrival_date'=>$a['arrival_date'] ?? null,
						'delivery_date'=>$a['delivery_date'] ?? null,
						'transit_days'=>$a['transit_days'] ?? null,
						'vehicle_no'=>$a['vehicle_no'] ?? null,
						'vehicle_size'=>$a['vehicle_size'] ?? null,
						'actual_weight'=>$a['actual_weight'] ?? null,
						'no_of_packages'=>$a['no_of_packages'] ?? null,
						'freight'=>$a['freight'] ?? null,
						'charge_weight'=>$a['charge_weight'] ?? null,
						'loading_charge'=>$a['loading_charge'] ?? null,
						'unloading_charge'=>$a['unloading_charge'] ?? null,
						'loading_pt_det_charge'=>$a['loading_pt_det_charge'] ?? null,
						'unloading_pt_det_charge'=>$a['unloading_pt_det_charge'] ?? null,
						'incentive_charge'=>$a['incentive_charge'] ?? null,
						'other_charge'=>$a['other_charge'] ?? null,
						'two_point_delivery'=>$a['two_point_delivery'] ?? null,
						'toll_tax'=>$a['toll_tax'] ?? null,
						'green_tax'=>$a['green_tax'] ?? null
					]);
				}
			}

		});

		//return redirect()->route('admin.invoice.list');
		 return redirect()->route('admin.invoice.list')
        ->with('success', $action === 'final' ? 'Invoice saved as Final.' : 'Invoice saved as Draft.');

	}
	
	public function edit($id)
	{
		$invoice = Invoice::with(['items', 'sitePlant', 'registeredAddress', 'billingAddress', 'branchAddress', 'vendor'])
			->findOrFail($id);

		//$this->authorizeInvoice($invoice);

		if ($invoice->status === 'final') {
			return redirect()->route('admin.invoice.list')
				->with('error', 'Final invoice cannot be edited.');
		}

		$vendor = $invoice->vendor;
		$plants = Siteplant::all();
		$billing = $vendor->addresses()->where('address_type', 'Billing')->get();
		$branch = $vendor->addresses()->where('address_type', 'Branch')->get();

		return view('admin.invoice.edit', compact('invoice', 'vendor', 'plants', 'billing', 'branch'));
	}
	
	public function update(Request $request, $id)
	{
		$invoice = Invoice::with(['items', 'vendor', 'registeredAddress'])->findOrFail($id);
		$this->authorizeInvoice($invoice);

		if ($invoice->status === 'final') {
			return redirect()->route('admin.invoice.list')
				->with('error', 'Final invoice cannot be edited.');
		}

		$action = $request->input('action', 'draft');

		$request->validate([
			'site_plant_id' => 'required',
			'billing_address_id' => 'required',
			'branch_address_id' => 'required',
			'bill_date' => 'required|date',
			'items' => 'required|array|min:1',
			'items.0.lr_no' => 'required',
			'items.0.lr_date' => 'required|date',
			
		]);

		DB::transaction(function () use ($request, $invoice, $action) {
			$plant = Siteplant::findOrFail($request->site_plant_id);

			$registered = $invoice->registeredAddress ?: $invoice->vendor->addresses()->where('address_type', 'Registered')->first();
			$gstType = ($registered && $registered->state == $plant->state) ? 'CGST_SGST' : 'IGST';

			$invoice->update([
				'site_plant_id' => $plant->id,
				'indent_id' => $request->indent_id,
				'bill_date' => $request->bill_date,
				'gst_type' => $gstType,
				'billing_address_id' => $request->billing_address_id,
				'branch_address_id' => $request->branch_address_id,
				'status' => $action === 'final' ? 'final' : 'draft',
			]);

			/*$totalTaxable = 0;
			$totalTax = 0;
			$grandTotal = 0;
			*/

			foreach ($request->items as $index => $item) {
				/*$taxable = (float) ($item['taxable'] ?? 0);
				$gstPercent = (float) ($item['gst'] ?? 0);
				$gstAmount = $taxable * ($gstPercent / 100);

				if ($gstType === 'CGST_SGST') {
					$cgst = $gstAmount / 2;
					$sgst = $gstAmount / 2;
					$igst = 0;
				} else {
					$cgst = 0;
					$sgst = 0;
					$igst = $gstAmount;
				}

				$total = $taxable + $gstAmount;
				*/

				$invoiceItem = null;
				if (!empty($item['id'])) {
					$invoiceItem = InvoiceItem::where('invoice_id', $invoice->id)
						->where('id', $item['id'])
						->first();
				}

				if (!$invoiceItem) {
					$invoiceItem = $invoice->items[$index] ?? new InvoiceItem();
					$invoiceItem->invoice_id = $invoice->id;
				}
				/*
				if needed then can add to update
				'base_freight' => $item['base_freight'] ?? 0,
					'taxable' => $taxable,
					'gst_percent' => $gstPercent,
					'cgst' => $cgst,
					'sgst' => $sgst,
					'igst' => $igst,
					'total' => $total,
				
				*/
				$invoiceItem->fill([
					'description' => $item['description'] ?? '',
						
					'lr_no' => $item['lr_no'] ?? null,
					'lr_date' => $item['lr_date'] ?? null,
					'vehicle_dispatch_date' => $item['vehicle_dispatch_date'] ?? null,
					'from_location' => $item['from'] ?? null,
					'to_location' => $item['to'] ?? null,
					'po_no' => $item['po_no'] ?? null,
					'truck_type' => $item['truck_type'] ?? null,
					'vehicle_no' => $item['vehicle_no'] ?? null,
					'charge_weight' => $item['charge_weight'] ?? null,
					'actual_weight' => $item['actual_weight'] ?? null,
				]);
				$invoiceItem->save();

			//	$totalTaxable += $taxable;
			//	$totalTax += $gstAmount;
				//$grandTotal += $total;
			}

			/*$invoice->update([
				'total_taxable' => $totalTaxable,
				'total_tax' => $totalTax,
				'grand_total' => $grandTotal,
			]);*/
		});

		return redirect()->route('admin.invoice.list')
			->with('success', $action === 'final' ? 'Invoice finalized successfully.' : 'Draft invoice updated successfully.');
	}
  	
	public function pdf($id)
	{
		$user = Auth::user();
		
		$invoice = Invoice::with([
			'vendor',
			'items',
			'annexures',
			'sitePlant',
			'registeredAddress',
			'billingAddress',
			'branchAddress'
		])->findOrFail($id);

		// Authorization logic
		if ($user->role->name !== 'SuperAdmin') {

			if (!$user->vendor_code || 
				$invoice->vendor->vendor_code !== $user->vendor_code) {

				abort(403, 'Unauthorized');
			}
		}

		$vendor = $invoice->vendor;

		return PDF::loadView('admin.invoice.pdf', compact('invoice','vendor'))
			->setPaper('A4', 'portrait')
			->stream('invoice-'.$invoice->invoice_no.'.pdf');
	}
	
	
	
}
