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
use Illuminate\Support\Facades\Mail;

use App\Models\Spotby;
use App\Models\Vendor;
use App\Models\Ratedata;
use App\Models\TruckMaster;
use App\Models\Siteplant;
use App\Models\Admin;
use App\Models\SpotbyVendorQuote;
use App\Services\SpotbuyNotificationService;

use Auth;


class SpotbyController extends Controller
{
    //
	public function __construct()
    {
        $this->middleware('auth:admin');     
    }
	
	public function index(Request $request)
    {
        $title = 'Spotby Upload';
        $pagetitle = $title.' Listing';
             
        return view('admin.spotby.index',compact(['pagetitle','title']));
    }
	
	public function spotbylist(Request $request)
    {
        $title = 'spot by list';
        $pagetitle = $title.' Listing';
       $user_role = Auth::user()->role_id;
		$data = $request->all();        
	    $spotbylist = Spotby::orderBy('created_at', 'desc')->get();       
        return view('admin.spotby.spotbylist',compact(['pagetitle','title','spotbylist','user_role']));
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
		
		$createddate = date('Y-m-d H:i:s');

		$errorRows = [];
		$insertedCount = 0;
		$validData = [];

        DB::beginTransaction();
        try {
            foreach ($rows as $index => $row) {
                if ($index == 1) continue; // skip header row
				
				 $rowNumber = $index + 1;
				if (count(array_filter($row, fn($value) => trim((string)$value) !== '')) === 0) {
						continue;
					}
				$valid_from_data = $row['D'];
				$valid_from = Carbon::parse($valid_from_data)->format('Y-m-d');
				
				$valid_to_data = $row['E'];
				$valid_to = Carbon::parse($valid_to_data)->format('Y-m-d');
				
				$rfq_start_date_time_data = $row['L'] ?? null;
				$rfq_start_date_time = Carbon::parse($rfq_start_date_time_data)->format('Y-m-d H:i:s');
				
				$rfq_end_date_time_data = $row['M'] ?? null;
				$rfq_end_date_time = Carbon::parse($rfq_end_date_time_data)->format('Y-m-d H:i:s');
				
				$origin = $row['A'];
				$destination = $row['B'];
				
				 $originCity = DB::table('site_plants')
					->where('plant_site_code', $origin)
					->value('city');
					
				if (empty($originCity)) {
					$errorRows[] = [
									'row' => $rowNumber,
									'from' => $origin,
									'to' => $destination,
									'message' => 'Invalid origin master data. Source city not found for From code: ' . $origin
								];
					continue;
				}

				$destinationCity = DB::table('site_plants')
					->where('plant_site_code', $destination)
					->value('city');
					
				 if (empty($destinationCity)) {
						$errorRows[] = [
							'row' => $rowNumber,
							'from' => $origin,
							'to' => $destination,
							'message' => 'Invalid destination master data. Destination city not found for To code: ' . $destination
						];
						continue;
				}
				
				
				$first2_char_from  = strtoupper(substr($originCity,0,2));
				$first2_char_to  = strtoupper(substr($destinationCity,0,2));
			
                $data = [
                    'from' => $row['A'] ?? null,
                    'to' => $row['B'] ?? null,
                    'source_city' => $originCity ?? null,
                    'destination_city' => $destinationCity ?? null,
                    'vehicle_type' => $row['C'] ?? null,
                    'valid_from' => $valid_from ?? null,
                    'valid_upto' => $valid_to ?? null,
                    'no_of_vehicles' => $row['F'] ?? null,
                    'goods_qty' => $row['G'] ?? null,
                    'uom' => $row['H'] ?? null,
                    'loading_charges' => $row['I'] ?? null,
                    'unloading_charges' => $row['J'] ?? null,
                    'special_instruction' => $row['K'] ?? null,
                    'rfq_start_date_time' =>  $rfq_start_date_time,
                    'rfq_end_date_time' => $rfq_end_date_time,
                    'created_at' => $createddate,
                    'created_by' => Auth::user()->id,
                    'status' => '1'
                ];
				
           

            // ---- INSERT ----
            $spotby = Spotby::create($data);
			$reference_no = $first2_char_from . $first2_char_to .'00'. $spotby->id;
			//update unique reference number
			$spotby->reference_no = $reference_no;
			$spotby->save();
			
            $insertedCount++;
            }

            DB::commit();
			
			 if ($insertedCount === 0) {
            // No data inserted
            return back()
                ->with([
                    'errorRows' => $errorRows,
                    'error' => 'Please correct the highlighted errors.',
                ]);
        }

        // Success, maybe partial insert
        return redirect()->back()->with([
            'success' => "$insertedCount rows inserted successfully.",
            'failedRows' => $errorRows
        ]);
			
			
           // return redirect()->back()->with('success', 'Excel imported successfully!');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
	
	public function getspotbyDetails($id)
	{
		$spotby = Spotby::find($id);
		$userid = auth()->user()->id; //get loggedin user id		
		return view('admin.spotby.editspotby', compact('spotby'));
	}

	public function save_spotby(Request $request)
	{
		$validatedData = $request->validate(
			[
				'from' => 'required',
				'to' => 'required',
				'vehicle_type' => 'required',
			],
			[
				'from.required' => 'Please enter from location',
				'to.required' => 'Please to location',
				'vehicle_type.required' => 'Please vehicle type',
			]
		);
			$id = $request->id;
			
			$valid_from_data = $request->valid_from;
			$valid_from = Carbon::parse($valid_from_data)->format('Y-m-d');
			
			$valid_to_data = $request->valid_to;
			$valid_to = Carbon::parse($valid_to_data)->format('Y-m-d');
			
			$rfq_start_date_time_data = $request->rfq_start_date_time;
			$rfq_start_date_time = Carbon::parse($rfq_start_date_time_data)->format('Y-m-d H:i:s');
			
			$rfq_end_date_time_data = $request->rfq_end_date_time;
			$rfq_end_date_time = Carbon::parse($rfq_end_date_time_data)->format('Y-m-d H:i:s');
			
			$originCity = DB::table('site_plants')
					->where('plant_site_code', $request->from)
					->value('city');
					
					if (empty($originCity)) {
					return response()->json(['error'=>'Invalid origin master data']);
				}

				$destinationCity = DB::table('site_plants')
					->where('plant_site_code', $request->to)
					->value('city');
					
				if (empty($destinationCity)) {
					return response()->json(['error'=>'Invalid destinationCity master data']);
				}
			
			Spotby::find($id)->update([
				
				'from' => $request->from,
				'to' => $request->to,
				'source_city' => $originCity,
				'destination_city' => $destinationCity,
				'vehicle_type' => $request->vehicle_type,
				'valid_from' => $valid_from,
				'valid_upto' => $valid_to,
				'no_of_vehicles' => $request->no_of_vehicles,
				'goods_qty' => $request->goods_qty,
				'uom' => $request->uom,
				'loading_charges' => $request->loading_charges,
				'unloading_charges' => $request->unloading_charges,
				'special_instruction' => $request->special_instruction,
				'rfq_start_date_time' => $rfq_start_date_time ?? null,
				'rfq_end_date_time' => $rfq_end_date_time ?? null,
				'updated_at' => Carbon::now(),
				'status' => $request->status,
			]);
			return Redirect('/admin/spotby-history')->with('success', 'Data updated successfully!');
		
	}
	
	
	public function Deletespotby($id)
	{
		$spotby = Spotby::find($id);		
		Spotby::find($id)->delete();
		return Redirect('/admin/spotby-history')->with('success', 'Data deleted successfully!');
	}
	
	
	//manual Upload
	
	public function manualupload()
	{
		$userid = auth()->user()->id; //get loggedin user id		
		return view('admin.spotby.manualupload');
	}

	public function save_manual_spotby(Request $request)
	{
		
		$created_by = Auth::user()->id; 
		
		$createddate = date('Y-m-d H:i:s');
		
		$from     = $request->input('from', []);
		$to     = $request->input('to', []);
		$vehicle_type = $request->input('vehicle_type', []);
		$valid_from = $request->input('valid_from', []);
		$valid_upto = $request->input('valid_upto', []);
		$no_of_vehicles = $request->input('no_of_vehicles', []);
		$goods_qty = $request->input('goods_qty', []);
		$uom = $request->input('uom', []);
		$loading_charges = $request->input('loading_charges', []);
		$unloading_charges = $request->input('unloading_charges', []);
		$special_instruction = $request->input('special_instruction', []);
		$rfq_start_date_time = $request->input('rfq_start_date_time', []);
		$rfq_end_date_time = $request->input('rfq_end_date_time', []);
		
		$count = count($from);
		
		$errorRows = [];
		$insertedCount = 0;
		$validData = [];
		$rfq_end_date_time_ins = '';
		$rfq_start_date_time_ins ='';
        DB::beginTransaction();
        try {
            for ($i = 0; $i < $count; $i++) {
				
				if(!empty($from[$i]) && !empty($to[$i] && !empty($vehicle_type[$i])))
				{
                $rowNumber = $i + 1;
				
				$valid_from_data = $valid_from[$i];
			 	$valid_from = Carbon::parse($valid_from_data)->format('Y-m-d');
				
				$valid_to_data = $valid_upto[$i];
			 	$valid_to = Carbon::parse($valid_to_data)->format('Y-m-d');
				
				if(!empty($rfq_start_date_time[$i]))
				{
				$rfq_start_date_time_data = $rfq_start_date_time[$i] ;
				$rfq_start_date_time_ins = Carbon::parse($rfq_start_date_time_data)->format('Y-m-d H:i:s');
				}
				else
				{
					$rfq_start_date_time_ins = null;
				}
				if(!empty($rfq_end_date_time[$i]))
				{
					$rfq_end_date_time_data = $rfq_end_date_time[$i];
					$rfq_end_date_time_ins = Carbon::parse($rfq_end_date_time_data)->format('Y-m-d H:i:s');
				}
				else{
					$rfq_end_date_time_ins = null;
				}
				
				$origin = $from[$i];
				$destination = $to[$i];
				
				 $originCity = DB::table('site_plants')
					->where('plant_site_code', $origin)
					->value('city');
					
				if (empty($originCity)) {
					$errorRows[] = [
									'row' => $rowNumber,
									'from' => $origin,
									'to' => $destination,
									'message' => 'Invalid origin master data. Source city not found for From code: ' . $origin
								];
					continue;
				}

				$destinationCity = DB::table('site_plants')
					->where('plant_site_code', $destination)
					->value('city');
					
				 if (empty($destinationCity)) {
						$errorRows[] = [
							'row' => $rowNumber,
							'from' => $origin,
							'to' => $destination,
							'message' => 'Invalid destination master data. Destination city not found for To code: ' . $destination
						];
						continue;
				}
				
				
				$first2_char_from  = strtoupper(substr($originCity,0,2));
				$first2_char_to  = strtoupper(substr($destinationCity,0,2));
			
				
					
					$data = [
					'from' => $from[$i] ?? null,
					'to' => $to[$i] ?? null,
					'source_city' => $originCity ?? null,
                    'destination_city' => $destinationCity ?? null,
					'vehicle_type' => $vehicle_type[$i] ?? null,
					'valid_from' => $valid_from ?? null,
					'valid_upto' => $valid_to ?? null,
					'no_of_vehicles' => $no_of_vehicles[$i] ?? null,
					'goods_qty' => $goods_qty[$i] ?? null,
					'uom' => $uom[$i] ?? null,
					'loading_charges' => $loading_charges[$i] ?? null,
					'unloading_charges' => $unloading_charges[$i] ?? null,
					'special_instruction' => $special_instruction[$i] ?? null,
					'rfq_start_date_time' => $rfq_start_date_time_ins ?? null,
					'rfq_end_date_time' => $rfq_end_date_time_ins ?? null,
					'created_at' => $createddate,
					'created_by' => Auth::user()->id,
					'status' => '1'
					];
				
					// ---- INSERT ---					
					$spotby = Spotby::create($data);
					$reference_no = $first2_char_from . $first2_char_to .'00'. $spotby->id;
					//update unique reference number
					$spotby->reference_no = $reference_no;
					$spotby->save();
					$insertedCount++;
				}
            }
			
			//print_r($errorRows); exit;
            DB::commit();
			
			if ($insertedCount === 0) {
            // No data inserted
            return back()
                ->withInput()
                ->with([
                    'errorRows' => $errorRows,
                    'error' => 'No data inserted. Please correct the highlighted errors.',
                ]);
        }

        // Success, maybe partial insert
        return redirect()->back()->with([
            'success' => "$insertedCount rows inserted successfully.",
            'failedRows' => $errorRows
        ]);
		
			
        } catch (\Exception $e) {
            DB::rollback();
           return redirect()->back()->withInput()->with('error', 'Error: ' . $e->getMessage());
			//return redirect()->back()->withInput()->with('error', 'Error: Something went wrong.');
        }		
	}
	
	//spot by select Vendor	
	public function spotbyselectvendor(Request $request)
    {
        $title = 'spot by list';
        $pagetitle = $title.' Listing';
		$user_role = Auth::user()->role_id;
		$data = $request->all();   
		$vendors = Vendor::whereNULL('parent_id')->orderBy('created_at', 'desc')->get();  		
	    $spotbylist = Spotby::with('vendors')->whereDoesntHave('vendors')->get();  

		$historyspotbylist =	Spotby::with('vendors')->whereHas('vendors')->get(); 
		
         return view('admin.spotby.spotby-select-vendor',compact(['pagetitle','title','spotbylist','user_role', 'vendors', 'historyspotbylist']));
    }
	
	
	// Vendor - show list of Spotby assigned

	public function vendorQuote()
	{
		
		$roleName = Auth::user()->role->name;


		if ($roleName==='SuperAdmin') {
			
			// ADMIN VIEW — see all assignments

			// Tab 1: Assigned but not quoted by any vendor
			$spotbylist = Spotby::whereHas('vendors')
				->whereDoesntHave('quotes')
				->with('vendors')
				->get();

			// Tab 2: All quoted spotbys
			$historyQuotes = Spotby::whereHas('quotes')
				->with(['quotes', 'vendors'])
				->get();

		} else {
			// Logged-in vendor
			$user = Auth::user();
			$vendor_code = $user->vendor_code;

			$vendorId = Vendor::where('vendor_code', $vendor_code)->value('id');

			// Tab 1: Assigned but not yet quoted
			$spotbylist = Spotby::whereHas('vendors', function($q) use ($vendorId) {
					$q->where('vendor_id', $vendorId);
				})
				->whereDoesntHave('quotes', function($q) use ($vendorId) {
					$q->where('vendor_id', $vendorId);
				})
				->get();

			// Tab 2: Already quoted 
			$historyQuotes = Spotby::whereHas('quotes', function($q) use ($vendorId) {
					$q->where('vendor_id', $vendorId)->where('round', 1);;
				})
				->with(['quotes' => function($q) use ($vendorId) {
					$q->where('vendor_id', $vendorId)->where('round', 1);;
				}])
				->get();
			
		}

		return view('admin.spotby.spotby-vendor-quote', 
			compact('spotbylist', 'historyQuotes')
		);
	}
	
	// Store multiple vendor quotes at once
    public function storeAll(Request $request)
    {
        $vendor_code = Auth::user()->vendor_code; // assuming vendor is logged in
		
		$vendorId = Vendor::where('vendor_code', $vendor_code)->value('id') ?? 1;	//Get vendor master Id using vendor code	

        $data = $request->input('quotes', []);
		
		//print_r($data); exit;

        if (empty($data)) {
            return response()->json(['status' => 'error', 'message' => 'No quotes received']);
        }

        foreach ($data as $item) {
            if (!isset($item['spotby_id'], $item['price'], $item['transit_time'])) {
                continue;
            }

            // check if vendor already quoted -> set round accordingly
            $round = SpotbyVendorQuote::where('spotby_id', $item['spotby_id'])
                ->where('vendor_id', $vendorId)
                ->count() >= 1 ? 2 : 1;

            SpotbyVendorQuote::updateOrCreate(
                [
                    'spotby_id' => $item['spotby_id'],
                    'vendor_id' => $vendorId,
                    'round'     => $round,
                ],
                [
                    'price'        => $item['price'],
                    'transit_time' => $item['transit_time'],
                ]
            );
        }

        return response()->json(['status' => 'success', 'message' => 'All quotes saved successfully!']);
    }

//USER S1 Round 2 Revised price
	// Vendor - show list of Spotby assigned
    public function vendorQuoteRound2()
    {
        $roleName = Auth::user()->role->name;
		
		if ($roleName==='SuperAdmin') {
		 $spotbylist = Spotby::whereHas('quotes', function ($q) {
                $q->where('round', 1)
                  ->whereNotNull('client_revised_price');
            })
            ->whereDoesntHave('quotes', function ($q) {
                $q->where('round', 2);
            })
            ->with(['quotes' => function ($q) {
                $q->where('round', 1)
                  ->whereNotNull('client_revised_price');
            }, 'vendors'])
            ->get();


        // Tab 2: All Round 2 submitted
        $historyQuotes = Spotby::whereHas('quotes', function ($q) {
                $q->where('round', 2);
            })
            ->with(['quotes', 'vendors'])
            ->get();
		}
		else{
		
			   $vendor_code = Auth::user()->vendor_code; // assuming vendor is logged in
				
			   $vendorId = Vendor::where('vendor_code', $vendor_code)->value('id');
				
				$spotbylist = Spotby::whereDoesntHave('quotes', function($q) use ($vendorId) {
				$q->where('vendor_id', $vendorId)
				  ->where('round', 2);
				})
				->whereHas('quotes', function($q) use ($vendorId) {
				$q->where('vendor_id', $vendorId)->WhereNotNull('client_revised_price')
				  ->where('round', 1);
				})
				->with(['quotes' => function($q) use ($vendorId) {
				$q->where('vendor_id', $vendorId)
				  ->where('round', 1);
				}])
				->get();

				//Tab 2: Spotbys already quoted by vendor (history)
				$historyQuotes = Spotby::whereHas('quotes', function($q) use ($vendorId) {
					$q->where('vendor_id', $vendorId)
					  ->where('round', 2);
				})
				->with(['quotes' => function($q) use ($vendorId) {
					$q->where('vendor_id', $vendorId)
					  ->where('round', 2);
				}])
				->get();
				
		/* Check Qualified status ONLY for History Tab */
				
				foreach ($historyQuotes as $historyQuote) {

					$historyQuote->is_qualified = false;

					if (!empty($historyQuote->freeze_vendor_name) && !empty($vendorShortName) &&
						trim($historyQuote->freeze_vendor_name) === $vendorShortName) 
					{
						$historyQuote->is_qualified = true;
					}
				}
				
				
		}
		
		
        return view('admin.spotby.spotby-vendor-quote-round2', compact('spotbylist', 'historyQuotes'));
    }
	
	// Store multiple vendor quotes at once
	public function storeAllRound2(Request $request)
	{
		try {
			$vendor_code = Auth::user()->vendor_code;
			$vendorId = Vendor::where('vendor_code', $vendor_code)->value('id') ?? 1;

			$data = $request->input('quotes', []);
				//print_r($data); exit;
			if (empty($data)) {
				return response()->json([
					'status' => 'error',
					'message' => 'No quotes received'
				]);
			}

			foreach ($data as $quoteData) {
				SpotbyVendorQuote::create([
					'spotby_id'    => $quoteData['spotby_id'],
					'vendor_id'    => $vendorId,
					'round'        => 2,
					'price'        => $quoteData['price'] ?? null,
					'transit_time' => $quoteData['transit_time'] ?? null,
				]);
			}

			return response()->json([
				'status' => 'success',
				'message' => 'All revised quotes saved successfully.'
			]);
		} catch (\Exception $e) {
			return response()->json([
				'status' => 'error',
				'message' => 'Failed to save revised quotes.',
				'error'   => $e->getMessage()
			]);
		}
	}


///User B1 _Round 2 (Buyer) Add revised price and time by Buyer client
	/*public function buyerB1R2Quote()
    {
        $vendor_code = Auth::user()->vendor_code; // assuming vendor is logged in
		
		$vendorId = Vendor::where('vendor_code', $vendor_code)->value('id');	
		//Get vendor master Id using vendor code	
		 
		 $spotbylist = Spotby::with(['quotes' => function ($q) {
            $q->whereNull('price')
			  ->whereNull('client_revised_price')
              ->where('round', 1)
              ->orderBy('price', 'asc')
              ->orderBy('transit_time', 'asc')
              ->with('vendor'); // vendor relation
			}])->get();

		//Tab 2: Spotbys already quoted by vendor (history)
				
		$historyQuotes = Spotby::whereHas('quotes', function ($q) {
        $q->whereNotNull('price')
          ->whereNotNull('client_revised_price')
          ->where('round', 1);
			})
			->with(['quotes' => function ($q) {
				$q->whereNotNull('price')
				  ->whereNotNull('client_revised_price')
				  ->where('round', 1)
				  ->orderBy('price', 'asc')
				  ->orderBy('transit_time', 'asc')
				  ->with('vendor'); // vendor relation
			}])
			->get();
        return view('admin.spotby.spotby-client-quote-b1r2-revised', compact('spotbylist', 'historyQuotes'));
    }*/
	
	// User B1 Round 2 (Buyer)
// Buyer adds revised price and transit time

// User B1 Round 2 (Buyer)
// Buyer adds revised price and transit time

public function buyerB1R2Quote()
{
    /* TAB 1
    Supplier Round 1 quoted, but Round 2 does NOT exist yet
   
    */

    $spotbylist = Spotby::whereHas('quotes', function ($q) {

            /*
              Supplier must have submitted Round 1 quotation
             */

            $q->where('round', 1)
              ->whereNotNull('price');

        })

        /*
          If Round 2 quotation already exists,
          do not show this Spot Buy in Tab 1.
         */

        ->whereDoesntHave('quotes', function ($q) {

            $q->where('round', 2);

        })

        /* Load Round 1 supplier quotations */

        ->with([
            'quotes' => function ($q) {

                $q->where('round', 1)
                  ->whereNotNull('price')
                  ->orderBy('price', 'asc')
                  ->orderBy('transit_time', 'asc')
                  ->with('vendor');

            }
        ])

        ->get();


    $historyQuotes = Spotby::whereHas('quotes', function ($q) {

            $q->where('round', 2)
              ->whereNotNull('price');

        })

        ->with([
            'quotes' => function ($q) {

                /* Load Round 2 records*/

                $q->where('round', 2)
                  ->whereNotNull('price')
                  ->orderBy('price', 'asc')
                  ->orderBy('transit_time', 'asc')
                  ->with('vendor');

            }
        ])

        ->get();


    return view(
        'admin.spotby.spotby-client-quote-b1r2-revised',
        compact(
            'spotbylist',
            'historyQuotes'
        )
    );
}
	
	///storeClientOffers
/*	
	public function storeClientOffers(Request $request)
	{
		$spotbyIds   = $request->input('spotby_id');
		$prices      = $request->input('client_price');
		$times       = $request->input('client_time');
		$round		= $request->input('round');
		try {
				foreach ($spotbyIds as $i => $spotby_id) {
					// Update all vendor quotes for this spotby
					SpotbyVendorQuote::where('spotby_id', $spotby_id)
						->where('round',$round) 
						->update([
							'client_revised_price'        => $prices[$i],
							'client_revised_transit_time' => $times[$i],
						]);
								
				}
				
				return response()->json(['success' => true, 'message' => 'Client offers saved for all vendors!']);
			}
			catch(\Exception $e)
			{
					// \Log::error('Error saving client offers: '.$e->getMessage(), [				'trace' => $e->getTraceAsString()]);
					

					return response()->json([
					'success' => false,
					'message' => 'Client offers can not be saved for all vendors!']);		
			}

	}
	*/
	
	public function storeClientOffers(Request $request)
	{
		$spotbyIds = $request->input('spotby_id');
		$prices    = $request->input('client_price');
		$times     = $request->input('client_time');
		$round     = $request->input('round');

		try {

			foreach ($spotbyIds as $i => $spotby_id) {

				SpotbyVendorQuote::where('spotby_id', $spotby_id)
					->where('round', $round)
					->update([
						'client_revised_price'        => $prices[$i],
						'client_revised_transit_time' => $times[$i],
					]);

				try {

					$spotby = Spotby::find($spotby_id);
					if ($spotby) {

						/* Get all suppliers/vendors already selected
						 * for this Spot Buy.
						 */

						$supplierIds = DB::table('spotby_vendors')
										->where('spotby_id', $spotby_id)
										->pluck('vendor_id')
										->toArray();

						if (!empty($supplierIds)) {

							SpotbuyNotificationService::createForSuppliers(
								$supplierIds,(int) $spotby_id,	2,	'Buyer Round 2 Updated',
								'Buyer has updated the quotation in Round 2. Please review and submit your revised quotation.',
								route('admin.vendor.quotes.round2')
							);
						}
					}

				} catch (\Throwable $notificationException) {

					\Log::error(
						'Buyer Round 2 supplier notification failed',
						[
							'spotby_id' => $spotby_id,
							'error'     => $notificationException->getMessage(),
						]
					);
				}

			}
			return response()->json([
				'success' => true,
				'message' => 'Client offers saved for all vendors!'
			]);

		}
		catch (\Exception $e)
		{
			/*
			\Log::error('Error saving client offers: '.$e->getMessage(), [
				'trace' => $e->getTraceAsString()
			]);
			*/
			return response()->json([
				'success' => false,
				'message' => 'Client offers can not be saved for all vendors!'
			]);
		}
	}
	
	
	//User B1 _Round 3 (Buyer) Add revised price and time by Buyer client after round 2 price submitted by vendor
	
	
	public function buyerRevisedQuoteB1R3()
	{
		$vendor_code = Auth::user()->vendor_code;

		$vendorId = Vendor::where('vendor_code', $vendor_code)->value('id');

		$spotbylist = Spotby::with([
			'quotes' => function ($q) {
				$q->select(
						'spotby_vendor_quotes.*',
						DB::raw('r1.client_revised_price as round1_client_revised_price'),
						DB::raw('r1.transit_time as round1_client_transit_time')
					)
					->leftJoin('spotby_vendor_quotes as r1', function ($join) {
						$join->on('spotby_vendor_quotes.spotby_id', '=', 'r1.spotby_id')
							 ->on('spotby_vendor_quotes.vendor_id', '=', 'r1.vendor_id')
							 ->where('r1.round', 1);
					})
					->whereNotNull('spotby_vendor_quotes.price')
					->whereNull('spotby_vendor_quotes.client_revised_price')
					->where('spotby_vendor_quotes.round', 2)
					->orderBy('spotby_vendor_quotes.price', 'asc')
					->orderBy('spotby_vendor_quotes.transit_time', 'asc')
					->with('vendor');
			},
			'vendors'
		])->get();

		// History with round1 data
		$historyQuotes = Spotby::with([
			'quotes' => function ($q) {
				$q->select(
						'spotby_vendor_quotes.*',
						DB::raw('r1.client_revised_price as round1_client_revised_price'),
						DB::raw('r1.transit_time as round1_client_transit_time')
					)
					->leftJoin('spotby_vendor_quotes as r1', function ($join) {
						$join->on('spotby_vendor_quotes.spotby_id', '=', 'r1.spotby_id')
							 ->on('spotby_vendor_quotes.vendor_id', '=', 'r1.vendor_id')
							 ->where('r1.round', 1);
					})
					->whereNotNull('spotby_vendor_quotes.price')
					->whereNotNull('spotby_vendor_quotes.client_revised_price')
					->where('spotby_vendor_quotes.round', 2)
					->orderBy('spotby_vendor_quotes.price', 'asc')
					->orderBy('spotby_vendor_quotes.transit_time', 'asc')
					->with('vendor');
			}
		])->get();

		return view('admin.spotby.client-quote-b1-r3-revised', compact('spotbylist', 'historyQuotes'));
	}
	/*
	public function storeClientOffersB1R3(Request $request)
	{
		$spotbyIds   = $request->input('spotby_id');
		//$prices      = $request->input('client_price');
		$times       = $request->input('client_time');
		$round		= $request->input('round');
		$freezeVendors		= $request->input('freeze_vendor_name');
		$finalRates		= $request->input('final_price');
		$created_by = Auth::user()->id;		
		$createddate = date('Y-m-d H:i:s');
		$body = '';
		
		//print_r($freezeVendors); exit;
		try {
				foreach ($spotbyIds as $i => $spotby_id) {
					// Update all vendor quotes for this spotby
					SpotbyVendorQuote::where('spotby_id', $spotby_id)
						->where('round',$round) 
						->update([
							'client_revised_price'        => $finalRates[$i],
							'client_revised_transit_time' => $times[$i],
						]);	

					//  Update spotby master table				
				
					$spotbuy_upd = Spotby::where('id', $spotby_id)
						->update([
							'freeze_vendor_name' => $freezeVendors[$i],
							'final_rate'         => $finalRates[$i],
							'approve_status'       => 'Pending', 
							'freeze_date'       => $createddate, 
							'freeze_by'       => $created_by , 
						]);	
					if($spotbuy_upd)
					{
						//$spotbuy_data = Spotby::where('id', $spotby_id);
						$spotbuy_data = Spotby::where('id', $spotby_id)->first();
						$from = $spotbuy_data->from;
						$to = $spotbuy_data->to;
						$valid_from = $spotbuy_data->valid_from;
						$valid_upto = $spotbuy_data->valid_upto;
						$freeze_date = $spotbuy_data->freeze_date;
						$freeze_vendor_name = $spotbuy_data->freeze_vendor_name;
						$subject = "Award of Tender – RFQ No. {$spotby_id} . {$from} to {$to}"	;
						$admins = Admin::whereIn('role_id', [20, 22, 4])->where('status','1')->get();
					 $files = [];
					foreach ($admins as $admin) {
						$to_email = $admin->email;
						$to_name = $admin->name; // assuming 'name' column exists
						$data = [
							'name' => $to_name,
							'spotby_id' => $spotby_id,
							'from' => $from,
							'to' => $to,
							'valid_from' => $valid_from,
							'valid_upto' => $valid_upto,
							'vendor_name' => $freeze_vendor_name, 
							'freeze_date' => $freeze_date, 
							'body' => $body, // assuming $body is already defined
						];

						
						/*Mail::send('mail.soptbuy_approval_mail', $data, function($message) use ($to_email, $subject, $files) {
							$message->to($to_email)->subject($subject);
							$message->from(env("MAIL_USERNAME"), 'Activiya.com');

							foreach ($files as $file) {
								$message->attach($file);
							}
						});
					}
				
					}					
				}				
				return response()->json(['success' => true, 'message' => 'Client offers saved for all vendors!']);
			}
			catch(\Exception $e){
					 \Log::error('Error saving client offers: '.$e->getMessage(), [
					'trace' => $e->getTraceAsString()
					]);
					
               //  echo 'Error saving client offers: '.$e->getMessage(); exit;
					return response()->json([
					'success' => false,
					'message' => 'Client offers cant be saved for vendors!']);		
				}
	}*/
	
	public function storeClientOffersB1R3(Request $request)
	{
		$spotbyIds   = $request->input('spotby_id');
		//$prices      = $request->input('client_price');
		$times       = $request->input('client_time');
		$round		= $request->input('round');
		$freezeVendors		= $request->input('freeze_vendor_name');
		$finalRates		= $request->input('final_price');
		$created_by = Auth::user()->id;		
		$createddate = date('Y-m-d H:i:s');
		$body = '';
		
		//print_r($freezeVendors); exit;
		try {
				foreach ($spotbyIds as $i => $spotby_id) {
					
					// Update all vendor quotes for this spotby
					SpotbyVendorQuote::where('spotby_id', $spotby_id)
						->where('round',$round) 
						->update([
							'client_revised_price'        => $finalRates[$i],
							'client_revised_transit_time' => $times[$i],
						]);	

					//  Update spotby master table				
					
					$spotbuy_upd = Spotby::where('id', $spotby_id)
						->update([
							'freeze_vendor_name' => $freezeVendors[$i],
							'final_rate'         => $finalRates[$i],
							'approve_status'     => 'Pending', 
							'freeze_date'        => $createddate, 
							'freeze_by'          => $created_by, 
						]);	
					
					if($spotbuy_upd)
					{
						//$spotbuy_data = Spotby::where('id', $spotby_id);
						$spotbuy_data = Spotby::where('id', $spotby_id)->first();

						$from = $spotbuy_data->from;
						$to = $spotbuy_data->to;
						$valid_from = $spotbuy_data->valid_from;
						$valid_upto = $spotbuy_data->valid_upto;
						$freeze_date = $spotbuy_data->freeze_date;
						$freeze_vendor_name = $spotbuy_data->freeze_vendor_name;

						$subject = "Award of Tender – RFQ No. {$spotby_id} . {$from} to {$to}";

						$admins = Admin::whereIn('role_id', [20, 22, 4])
							->where('status','1')
							->get();

						$files = [];

						foreach ($admins as $admin) {

							$to_email = $admin->email;
							$to_name = $admin->name; // assuming 'name' column exists

							$data = [
								'name' => $to_name,
								'spotby_id' => $spotby_id,
								'from' => $from,
								'to' => $to,
								'valid_from' => $valid_from,
								'valid_upto' => $valid_upto,
								'vendor_name' => $freeze_vendor_name, 
								'freeze_date' => $freeze_date, 
								'body' => $body, // assuming $body is already defined
							];

							
							/*Mail::send('mail.soptbuy_approval_mail', $data, function($message) use ($to_email, $subject, $files) {
								$message->to($to_email)->subject($subject);
								$message->from(env("MAIL_USERNAME"), 'Activiya.com');

								foreach ($files as $file) {
									$message->attach($file);
								}
							});*/
						}
					}


					/* Buyer Round 3 Supplier Notification */

					try {

						$spotby = Spotby::find($spotby_id);

						if ($spotby) {

							/* Get all suppliers/vendors already linked with this Spot Buy.
							 */
							$supplierIds = DB::table('spotby_vendors')
							->where('spotby_id', $spotby_id)
							->pluck('vendor_id')
							->toArray();


							/*
							 * Create Round 3 notification
							 */

							if (!empty($supplierIds)) {

								SpotbuyNotificationService::createForSuppliers(

									$supplierIds,(int) $spotby_id,3, 'Buyer Round 3 Updated', 'Buyer has updated the final quotation in Round 3. Please review the latest quotation status.',
									route('admin.spotbuy.notifications.index')
								);
							}
						}

					} catch (\Throwable $notificationException) {

						\Log::error(
							'Buyer Round 3 supplier notification failed',
							[
								'spotby_id' => $spotby_id,
								'error'     => $notificationException->getMessage(),
							]
						);
					}

				}				

				return response()->json([
					'success' => true,
					'message' => 'Client offers saved for all vendors!'
				]);

			}
			catch(\Exception $e){

				\Log::error(
					'Error saving client offers: '.$e->getMessage(),
					[
						'trace' => $e->getTraceAsString()
					]
				);
					
				// echo 'Error saving client offers: '.$e->getMessage(); exit;

				return response()->json([
					'success' => false,
					'message' => 'Client offers cant be saved for vendors!'
				]);		
			}
	}
	
	
	
	
	
	//USER 3 ROUND 3 Approval
	
		//User B1 _Round 3 (Buyer) Add revised price and time by Buyer client after round 2 price submitted by vendor
	
	
	public function buyerQuoteRound3Approver()
	{
		$user = Auth::user();

		// base query for pending spotbys with required conditions
		$spotbyQuery = Spotby::with(['quotes.vendor'])
			->where('approve_status', 'Pending')
			->whereNotNull('freeze_vendor_name')
			->whereNotNull('final_rate');

		// apply approver logic based on user role
	   /* if ($user->role === 'approver') {
			// First-level approver (up to 1 lac)
			$spotbyQuery->where('final_price', '<=', 100000);
		} elseif ($user->role === 'verifier') {
			// Second-level approver (above 1 lac)
			$spotbyQuery->where('final_price', '>', 100000);
		}*/

		// fetch and map values
		$spotbylist = $spotbyQuery->get()->map(function ($spotby) {
			$spotby->market_avg_price = $spotby->quotes
				->where('round', 1)
				->avg('price');

			$spotby->target_freight_rate = optional(
				$spotby->quotes
					->where('round', 2)
					->whereNotNull('client_revised_price')
					->first()
			)->client_revised_price;

			$spotby->target_transit_time = optional(
				$spotby->quotes
					->where('round', 2)
					->whereNotNull('client_revised_transit_time')
					->first()
			)->client_revised_transit_time;

			return $spotby;
		});

		// Tab 2: Spotbys already quoted by vendor (history)
		$spotbyQuery = Spotby::with(['quotes.vendor'])
			->where('approve_status', 'Pending')
			->whereNotNull('freeze_vendor_name')
			->whereNotNull('final_rate');

		// apply approver logic based on user role
	   /* if ($user->role === 'approver') {
			// First-level approver (up to 1 lac)
			$spotbyQuery->where('final_price', '<=', 100000);
		} elseif ($user->role === 'verifier') {
			// Second-level approver (above 1 lac)
			$spotbyQuery->where('final_price', '>', 100000);
		}*/

		// fetch and map values
		$historyQuotes = Spotby::with([
        'quotes.vendor',
        'freezeByUser:id,name',   // join for freeze_by
        'approvedByUser:id,name'  // join for approve_by
			])
			->whereIn('approve_status', ['approved', 'rejected'])
			->get()
			->map(function ($spotby) {
				$spotby->market_avg_price = $spotby->quotes
					->where('round', 1)
					->avg('price');

				$spotby->target_freight_rate = optional(
					$spotby->quotes
						->where('round', 2)
						->whereNotNull('client_revised_price')
						->first()
				)->client_revised_price;

				$spotby->target_transit_time = optional(
					$spotby->quotes
						->where('round', 2)
						->whereNotNull('client_revised_transit_time')
						->first()
				)->client_revised_transit_time;

				// Add freeze/approve names for direct use in Blade
				$spotby->freeze_by_name = optional($spotby->freezeByUser)->name;
				$spotby->approved_by_name = optional($spotby->approvedByUser)->name;

				return $spotby;
			});
		
		return view(
			'admin.spotby.client-quote-round3-approval',
			compact('spotbylist', 'historyQuotes')
		);
	}
	
	
	////Approve / Reject / Final Price B1 R3 Buyer
	public function bulkApproval(Request $request)
	{
		try {
			$updates = $request->input('approvals', []); // array: spotby_id => status

			foreach ($updates as $spotbyId => $status) {
				Spotby::where('id', $spotbyId)->update([
					'approve_status' => $status,
					'approve_by'     => Auth::user()->id,
					'approve_date'   => Carbon::now(),
				]);
			}

			return response()->json(['success' => true, 'message' => 'Approval status updated successfully.']);
		} catch (\Exception $e) {
			return response()->json([
				'success' => false,
				'message' => 'Error: ' . $e->getMessage()
			], 500);
		}
	}
	
}
