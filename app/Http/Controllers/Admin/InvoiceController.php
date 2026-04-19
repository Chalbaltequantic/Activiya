<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use PhpOffice\PhpSpreadsheet\IOFactory;
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
		$action = $request->input('action', 'draft');

		$request->validate([
			'site_plant_id'      => 'required',
			'bill_date'          => 'required|date',
			'items'              => 'required|array|min:1',
			'items.0.lr_no'      => 'required',
			'items.0.lr_date'    => 'required',
			'items.0.gst'        => 'required|numeric',
		]);

		try {
			DB::transaction(function () use ($request, $action) {

				$vendorCode = Auth::user()->vendor_code;
				$vendor = Vendor::where('vendor_code', $vendorCode)->first();

				if (!$vendor) {
					throw new \Exception('Vendor not found for logged in user.');
				}

				$registered = $vendor->addresses()
					->where('address_type', 'Registered')
					->first();

				if (!$registered) {
					throw new \Exception('Registered address not found for vendor.');
				}

				$plant = Siteplant::find($request->site_plant_id);

				if (!$plant) {
					throw new \Exception('Selected client / site plant not found.');
				}

				$gstType = ($registered->state == $plant->state) ? 'CGST_SGST' : 'IGST';

				$invoice = Invoice::create([
					'vendor_id'             => $vendor->id,
					'site_plant_id'         => $plant->id,
					'invoice_no'            => Invoice::generateInvoiceNumber(),
					'bill_date'             => $request->bill_date,
					'gst_type'              => $gstType,
					'registered_address_id' => $registered->id,
					'billing_address_id'    => $request->billing_address_id,
					'branch_address_id'     => $request->branch_address_id,
					'indent_id'             => $request->indent_id,
					'status'                => $action === 'final' ? 'final' : 'draft',
				]);

				$totalTaxable = 0;
				$totalTax = 0;
				$grandTotal = 0;

				/* ================= MAIN ITEMS ================= */
				foreach ($request->items as $item) {

					$baseFreight = isset($item['base_freight']) ? (float) $item['base_freight'] : 0;
					$gstPercent  = isset($item['gst']) ? (float) $item['gst'] : 0;

					$gstApplicableAnnexure = 0;
					$nonGstAnnexure = 0;

					if ($request->has('annexures') && is_array($request->annexures)) {
						foreach ($request->annexures as $a) {

							$hasData = collect($a)->filter(function ($value) {
								return !is_null($value) && trim((string) $value) !== '';
							});

							if ($hasData->isEmpty()) {
								continue;
							}

							// GST applicable components
							$gstApplicableAnnexure += (float) ($a['freight'] ?? 0);
							$gstApplicableAnnexure += (float) ($a['loading_detention_charge'] ?? 0);
							$gstApplicableAnnexure += (float) ($a['loading_charge'] ?? 0);
							$gstApplicableAnnexure += (float) ($a['two_point_loading_charge'] ?? 0);
							$gstApplicableAnnexure += (float) ($a['unloading_detention_charge'] ?? 0);
							$gstApplicableAnnexure += (float) ($a['unloading_charge'] ?? 0);
							$gstApplicableAnnexure += (float) ($a['two_point_delivery_charge'] ?? 0);
							$gstApplicableAnnexure += (float) ($a['gr_charges'] ?? 0);
							$gstApplicableAnnexure += (float) ($a['fix_rental'] ?? 0);

							// GST not applicable components
							$nonGstAnnexure += (float) ($a['toll_tax'] ?? 0);
							$nonGstAnnexure += (float) ($a['green_tax'] ?? 0);
						}
					}

					// GST taxable = base freight + gst-applicable annexure amounts
					$taxable = $baseFreight + $gstApplicableAnnexure;

					// GST calculated only on taxable
					$gstAmount = $taxable * ($gstPercent / 100);

					// Final payable = taxable + GST + non-GST tax items
					$itemTotal = $taxable + $gstAmount + $nonGstAnnexure;

					if ($gstType == 'CGST_SGST') {
						$cgst = $gstAmount / 2;
						$sgst = $gstAmount / 2;
						$igst = 0;
					} else {
						$cgst = 0;
						$sgst = 0;
						$igst = $gstAmount;
					}

					InvoiceItem::create([
						'invoice_id'            => $invoice->id,
						'lr_no'                 => $item['lr_no'] ?? null,
						'lr_date'               => $item['lr_date'] ?? null,
						'vehicle_dispatch_date' => $item['vehicle_dispatch_date'] ?? null,
						'from_location'         => $item['from'] ?? null,
						'to_location'           => $item['to'] ?? null,
						'po_no'                 => $item['po_no'] ?? null,
						'description'           => $item['description'] ?? null,
						'taxable'               => $taxable,
						'base_freight'          => $baseFreight,
						'gst_percent'           => $gstPercent,
						'cgst'                  => $cgst,
						'sgst'                  => $sgst,
						'igst'                  => $igst,
						'total'                 => $itemTotal,
						'truck_type'            => $item['truck_type'] ?? null,
						'vehicle_no'            => $item['vehicle_no'] ?? null,
						'actual_weight'         => $item['actual_weight'] ?? null,
						'charged_weight'        => $item['charge_weight'] ?? null,
					]);

					$totalTaxable += $taxable;
					$totalTax += $gstAmount;
					$grandTotal += $itemTotal;
				}

				/* ================= ANNEXURE ================= */
				if ($request->has('annexures') && is_array($request->annexures)) {
					foreach ($request->annexures as $a) {

						$hasData = collect($a)->filter(function ($value) {
							return !is_null($value) && trim((string) $value) !== '';
						});

						if ($hasData->isEmpty()) {
							continue;
						}

						InvoiceAnnexure::create([
							'invoice_id'                 => $invoice->id,
							'customer_ref_no'            => $a['customer_ref_no'] ?? null,
							'obd_no'                     => $a['obd_po_no'] ?? null,
							'arrival_date'               => $a['arrival_date'] ?? null,
							'dispatch_date'              => $a['dispatch_date'] ?? null,
							'loading_detention_days'     => $a['loading_detention_days'] ?? null,
							'loading_detention_charge'   => $a['loading_detention_charge'] ?? null,
							'loading_charge'             => $a['loading_charge'] ?? null,
							'loading_pt_det_charge'      => $a['two_point_loading_charge'] ?? null,
							'reporting_date'             => $a['reporting_date'] ?? null,
							'transit_days'               => $a['transit_days'] ?? null,
							'unloading_date'             => $a['unloading_date'] ?? null,
							'unloading_detention_days'   => $a['unloading_detention_days'] ?? null,
							'unloading_detention_charge' => $a['unloading_detention_charge'] ?? null,
							'unloading_charge'           => $a['unloading_charge'] ?? null,
							'unloading_pt_det_charge'    => $a['two_point_delivery_charge'] ?? null,
							'freight'                    => $a['freight'] ?? null,
							'gr_charges'                 => $a['gr_charges'] ?? null,
							'fix_rental'                 => $a['fix_rental'] ?? null,
							'toll_tax'                   => $a['toll_tax'] ?? null,
							'green_tax'                  => $a['green_tax'] ?? null,
						]);
					}
				}

				$invoice->update([
					'total_taxable' => $totalTaxable,
					'total_tax'     => $totalTax,
					'grand_total'   => $grandTotal,
				]);
			});

			return redirect()->route('admin.invoice.list')
				->with('success', $action === 'final' ? 'Invoice saved as Final.' : 'Invoice saved as Draft.');

		} catch (\Exception $e) {
			return redirect()->back()
				->withInput()
				->with('error', 'Error: ' . $e->getMessage());
		}
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
	
	 public function uploadAnnexureForm($id)
    {
        $invoice = Invoice::with(['vendor', 'annexures'])->findOrFail($id);
       // $this->authorizeInvoice($invoice);

        return view('admin.invoice.upload_annexure', compact('invoice'));
    }
	
	public function uploadAnnexureStore(Request $request, $id)
	{
		$invoice = Invoice::with(['vendor', 'items', 'annexures'])->findOrFail($id);

		$request->validate([
			'annexure_file' => 'required|file|mimes:xls,xlsx',
		]);

		DB::transaction(function () use ($request, $invoice) {
			$spreadsheet = IOFactory::load($request->file('annexure_file')->getPathname());
			$sheet = $spreadsheet->getActiveSheet();
			$rows = $sheet->toArray(null, true, true, false);

			if (count($rows) < 2) {
				throw new \Exception('The uploaded file has no data rows.');
			}

			foreach ($rows as $index => $row) {
				if ($index === 0) {
					continue;
				}

				if (collect($row)->filter(fn ($v) => $v !== null && trim((string) $v) !== '')->isEmpty()) {
				continue;
				}

				if (!isset($row[0]) || trim((string) $row[0]) === '') {
					continue;
				}
				
					
				$arrival_date = !empty($row[3]) ? date('Y-m-d', strtotime($row[3])) : null;
				$dispatch_date = !empty($row[4]) ? date('Y-m-d', strtotime($row[4])) : null;

				$loading_detention_days = 0;
				if ($arrival_date && $dispatch_date) {
					$arr_date = strtotime($arrival_date);
					$disp_date = strtotime($dispatch_date);
					$loading_detention_days = max(0, ceil(($disp_date - $arr_date) / 86400));
				}

				$reporting_date = !empty($row[8]) ? date('Y-m-d', strtotime($row[8])) : null;
				$unloading_date = !empty($row[9]) ? date('Y-m-d', strtotime($row[9])) : null;

				$unloading_detention_days = 0;
				if ($reporting_date && $unloading_date) {
					$report_date = strtotime($reporting_date);
					$unload_date = strtotime($unloading_date);
					$unloading_detention_days = max(0, ceil(($unload_date - $report_date) / 86400));
				}

				InvoiceAnnexure::create([
					'invoice_id'                 => $invoice->id,
					'customer_ref_no'            => $row[0] ?? null,
					'obd_no'                     => $row[1] ?? null,
					'freight'                    => $row[2] ?? null,
					'arrival_date'               => $arrival_date,
					'dispatch_date'              => $dispatch_date,
					'loading_detention_days'     => $loading_detention_days,
					'loading_detention_charge'   => $row[5] ?? null,
					'loading_charge'             => $row[6] ?? null,
					'loading_pt_det_charge'      => $row[7] ?? null,
					'reporting_date'             => $reporting_date,
					'unloading_date'             => $unloading_date,
					'unloading_detention_days'   => $unloading_detention_days,
					'transit_days'               => $row[10] ?? null,
					'unloading_detention_charge' => $row[11] ?? null,
					'unloading_charge'           => $row[12] ?? null,
					'unloading_pt_det_charge'    => $row[13] ?? null,
					'gr_charges'                 => $row[14] ?? null,
					'fix_rental'                 => $row[15] ?? null,
					'toll_tax'                   => $row[16] ?? null,
					'green_tax'                  => $row[17] ?? null
				]);
			}

			// Refresh annexures after insert
			$invoice->load('annexures', 'items');

			$baseFreight = 0;
			$gstPercent = 0;

			$item = $invoice->items()->first();
			if ($item) {
				$baseFreight = (float) ($item->base_freight ?? 0);
				$gstPercent = (float) ($item->gst_percent ?? 0);
			}

			$gstApplicableAnnexure = 0;
			$nonGstAnnexure = 0;

			foreach ($invoice->annexures as $a) {
				// GST applicable
				$gstApplicableAnnexure += (float) ($a->freight ?? 0);
				$gstApplicableAnnexure += (float) ($a->loading_detention_charge ?? 0);
				$gstApplicableAnnexure += (float) ($a->loading_charge ?? 0);
				$gstApplicableAnnexure += (float) ($a->loading_pt_det_charge ?? 0);
				$gstApplicableAnnexure += (float) ($a->unloading_detention_charge ?? 0);
				$gstApplicableAnnexure += (float) ($a->unloading_charge ?? 0);
				$gstApplicableAnnexure += (float) ($a->unloading_pt_det_charge ?? 0);
				$gstApplicableAnnexure += (float) ($a->gr_charges ?? 0);
				$gstApplicableAnnexure += (float) ($a->fix_rental ?? 0);

				// GST not applicable
				$nonGstAnnexure += (float) ($a->toll_tax ?? 0);
				$nonGstAnnexure += (float) ($a->green_tax ?? 0);
			}

			$taxable = $baseFreight + $gstApplicableAnnexure;
			$gstAmount = $taxable * ($gstPercent / 100);
			$finalTotal = $taxable + $gstAmount + $nonGstAnnexure;

			if ($item) {
				if ($invoice->gst_type === 'CGST_SGST') {
					$cgst = $gstAmount / 2;
					$sgst = $gstAmount / 2;
					$igst = 0;
				} else {
					$cgst = 0;
					$sgst = 0;
					$igst = $gstAmount;
				}

				$item->update([
					'taxable' => $taxable,
					'cgst'    => $cgst,
					'sgst'    => $sgst,
					'igst'    => $igst,
					'total'   => $finalTotal,
				]);
			}

			$invoice->update([
				'total_taxable' => $taxable,
				'total_tax'     => $gstAmount,
				'grand_total'   => $finalTotal,
			]);
		});

		return redirect()->route('admin.invoice.list')->with('success', 'Annexures uploaded successfully.');
	}




	/*  public function uploadAnnexureStore(Request $request, $id)
    {
        $invoice = Invoice::with('vendor')->findOrFail($id);
      //  $this->authorizeInvoice($invoice);

        $request->validate([
            'annexure_file' => 'required|file|mimes:xls,xlsx',
        ]);

        DB::transaction(function () use ($request, $invoice) {
            $spreadsheet = IOFactory::load($request->file('annexure_file')->getPathname());
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray(null, true, true, false);

            if (count($rows) < 2) {
                throw new \Exception('The uploaded file has no data rows.');
            }


            foreach ($rows as $index => $row) {
                if ($index === 0) {
                    continue;
                }

                if (collect($row)->filter(fn ($v) => $v !== null && trim((string) $v) !== '')->isEmpty()) {
                    continue;
                }
				
				$arrival_date = !empty($row[3]) ? date('Y-m-d', strtotime($row[3])) : null;
				$dispatch_date = !empty($row[4]) ? date('Y-m-d', strtotime($row[4])) : null;
				
				$arr_date = strtotime($arrival_date);
				$disp_date = strtotime($dispatch_date);

				$loading_detention_days = ceil(abs($disp_date - $arr_date) / 86400);
				
				$repporting_date = !empty($row[8]) ? date('Y-m-d', strtotime($row[8])) : null;
				$unloading_date = !empty($row[9]) ? date('Y-m-d', strtotime($row[9])) : null;
				
				$report_date = strtotime($repporting_date);
				$unload_date = strtotime($unloading_date);

				$unloading_detention_days = ceil(abs($unload_date - $report_date) / 86400);

                InvoiceAnnexure::create([
                    'invoice_id' => $invoice->id,
                    'customer_ref_no' => $row[0] ?? null,
                    'obd_no' => $row[1] ?? null,
                    'freight' => $row[2] ?? null,
                    'arrival_date' => $arrival_date,
                    'dispatch_date' => $dispatch_date,
                    'loading_detention_days' =>$loading_detention_days ?? null,
                    'loading_detention_charge' => $row[5] ?? null,
					'loading_charge' => $row[6] ?? null,
					'loading_pt_det_charge' => $row[7] ?? null,
                    'reporting_date' => $repporting_date,
                    'unloading_date' => $unloading_date,
                    'unloading_detention_days' => $unloading_detention_days ?? null,
                    'transit_days' => $row[10] ?? null,
                    'unloading_detention_charge' => $row[1] ?? null,                    
                    'unloading_charge' => $row[12] ?? null,                    
                    'unloading_pt_det_charge' => $row[13] ?? null,
                    'gr_charges' => $row[14] ?? null,
                    'fix_rental' => $row[15] ?? null,
                    'toll_tax' => $row[16] ?? null,
                    'green_tax' => $row[17] ?? null
                    
                ]);
            }
        });

        return redirect()->route('admin.invoice.list')->with('success', 'Annexures uploaded successfully.');
    }
  */
	

  	
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
