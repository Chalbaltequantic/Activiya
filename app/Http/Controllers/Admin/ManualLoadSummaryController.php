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

use App\Models\Material;
use App\Models\Admin;
use App\Models\Loadoptimizer;
use App\Models\ManualLoadSummary;
use App\Models\LoadOptimizerItemHistory;
use App\Models\Ratedata;
use App\Models\AllocationHistory;
use App\Models\AllocationEditHistory;
use App\Models\LoadSendHistory;
use App\Models\LoadPlacementStatusLog;
use App\Models\ManualAllocationHistory;

use Auth;


class ManualLoadSummaryController extends Controller
{
   //
	
    public function __construct()
    {
		$this->middleware('auth:admin'); 
    }

	
	public function ManualsummaryList(Request $request)
    {
        $title = 'Manual Load Summary';
        $pagetitle = $title.' Listing';
		$user_role = Auth::user()->role_id;
		$data = $request->all();        
	    $loads = ManualLoadSummary::orderBy('created_at', 'desc')->get();       
        return view('admin.manual_load_summary.manual_loadsummary',compact(['pagetitle','title','loads','user_role']));
    }
	
	//AJAX: Fetch dependent data : origin city name , desination city, truck_code, truck name
	
/*    public function fetchRowData(Request $request)
    {
        $origin = $request->origin_code;
        $destination = $request->destination_code;
        $t_mode = $request->t_mode;
        
        $originCity = DB::table('site_plants')
            ->where('plant_site_code', $origin)
            ->value('city');

        $destinationCity = DB::table('site_plants')
            ->where('plant_site_code', $destination)
            ->value('city');

        $material = DB::table('materials')
            ->where('material_code', $sku)
            ->first();

		 if (!$originCity || !$destinationCity || !$material) {
            return response()->json(['error'=>'Invalid master data']);
        }

        return response()->json([
            'origin_city' => $originCity,
            'destination_city' => $destinationCity,
            'sku_description' => $material->material_description,
            'total_weight' => round($material->gross_weight_kg * $qty, 2),
            'total_volume' => round($material->volume_cft * $qty, 2),
        ]);
    }
*/
public function fetchRowData(Request $request)
{
    $request->validate([
        'origin_code'      => 'required',
        'destination_code' => 'required',
        't_mode'           => 'required'
    ]);

    $originCode      = $request->origin_code;
    $destinationCode = $request->destination_code;
    $tMode           = $request->t_mode;

	 // Fetch rate master details
    $rate = DB::table('rate_master')
        ->where('consignor_code', $originCode)
        ->where('consignee_code', $destinationCode)
        ->where('mode', $tMode)
        ->where('rank', 1)
        ->first();
    
   

    if (!$rate) {
        return response()->json([
            'status'  => false,
            'message' => 'No rate master found for selected route and mode'
        ], 404);
    }

    // Return combined response
    return response()->json([
        'status'              => true,
        'origin_city'  => $rate->consignor_location,
        'destination_city'  => $rate->consignee_location,
        'truck_code'          => $rate->t_code ?? null,
        'truck_type'          => $rate->truck_type ?? null,
        'rate_id'             => $rate->id ?? null
    ]);
}

	//manual Upload
	
	public function manualupload()
	{
		$userid = auth()->user()->id; //get loggedin user id		
		return view('admin.manual_load_summary.manualupload');
	}
	private function generateNextReference()
	{
		$lastRef = ManualLoadSummary::whereNotNull('reference_no')
			->orderBy('id', 'desc')
			->value('reference_no');

		if (!$lastRef) {
			//return 'Z000001';
		}

		$num = (int) substr($lastRef, 1);

		return 'Z' . str_pad($num + 1, 6, '0', STR_PAD_LEFT);
	}

	
	public function save_manual_data(Request $request)
	{
    $created_by  = Auth::user()->id;
    $createddate = now();

    $t_mode = $request->t_mode;

    $origin_name_code      = $request->origin_name_code ?? [];
    $origin_name           = $request->origin_name ?? [];
    $destination_name_code = $request->destination_name_code ?? [];
    $destination_city      = $request->destination_name ?? [];

    $truck_code  = $request->truck_code ?? [];
    $truck_name  = $request->truck_name ?? [];
    $qty         = $request->qty ?? [];

    // For non-FTL
    $weight      = $request->weight ?? [];
    $volumecft   = $request->cft ?? [];
    $no_of_cases = $request->no_of_cases ?? [];

    $required_pickup_date = $request->required_pickup_date ?? [];
    $indent_instructions  = $request->indent_instructions ?? [];
    $remarks              = $request->remarks ?? [];

    $insertedCount = 0;

    DB::beginTransaction();

    try {

        foreach ($origin_name_code as $i => $originCode) {

            // Skip empty rows
            if (empty($originCode) || empty($destination_name_code[$i])) {
                continue;
            }

            // Generate reference PER ROW
            $referenceNo = $this->generateNextReference();

            $required_pickupdate = !empty($required_pickup_date[$i])
                ? date('Y-m-d', strtotime($required_pickup_date[$i]))
                : null;

            ManualLoadSummary::create([
                'reference_no'           => $referenceNo,
                'origin_name_code'       => $originCode,
                'origin_name'            => $origin_name[$i] ?? null,
                'destination_name_code'  => $destination_name_code[$i],
                'destination_name'       => $destination_city[$i] ?? null,
                't_mode'                 => $t_mode,

                'truck_code'             => $truck_code[$i] ?? null,
                'truck_name'             => $truck_name[$i] ?? null,
                'qty'                    => $qty[$i] ?? null,

                'total_weight'           => $weight[$i] ?? null,
                'total_volume'           => $volumecft[$i] ?? null,
                'no_of_cases'            => $no_of_cases[$i] ?? null,

                'required_pickup_date'   => $required_pickupdate,
                'indent_instructions'    => $indent_instructions[$i] ?? null,
                'remarks'                => $remarks[$i] ?? null,

                'status'                 => 1,
                'created_by'             => $created_by,
                'created_at'             => $createddate,
            ]);

            $insertedCount++;
        }

        DB::commit();

        if ($insertedCount === 0) {
            return back()
                ->withInput()
                ->with('error', 'No valid rows found to insert.');
        }

       // return back()->with('success', "{$insertedCount} rows inserted successfully.");
	   return Redirect('/admin/manual-load-summary/list')->with('success', "{$insertedCount} rows inserted successfully.");

    } catch (\Exception $e) {

        DB::rollBack();

        Log::error('Manual Loads Insert Error', [
            'error' => $e->getMessage(),
        ]);

        return back()->with('error', $e->getMessage());
    }
}

	
		public function sendApproval(Request $r)
		{
			$created_by   = Auth::user()->id;
			$createddate  = now();
			LoadSummary::where('id', $r->id)->update([
				'approval_remark' => $r->remark,
				'approval_status' => 'SENT_FOR_APPROVAL',
				'approval_sent_by' => $created_by,
				'approval_sent_at' => $createddate
			]);

			return response()->json(['success' => true]);
		}
	

   
   
	///Summary Approval / Reject
	public function loadSummaryApproval()
	{		
		// Fetch summaries (unqualified first) to approve / reject
		$loads = LoadSummary::where('is_qualified', 0)
				->where('approval_status','SENT_FOR_APPROVAL')
				->orderBy('id', 'desc')
				->get();
		return view('admin.loadoptimizer.approve_reject_load_summary', compact('loads'));
	}
	
	
	public function updateStatus(Request $request, $id)
	{
		$request->validate([
			'status' => 'required',
			'reason' => 'nullable|string|max:255',
		]);

		if (empty($request->reason)) {
			return response()->json([
				'status'  => 'error',
				'message' => 'Remark is required'
			], 422);
		}

		try {
			\DB::beginTransaction();

			$isQualified = ($request->status === 'APPROVED') ? 1 : 0;

			$updated = \DB::table('load_summary')
				->where('id', $id)
				->update([
					'approval_status' => $request->status,
					'is_qualified' => $isQualified,
					'approved_at'   => now(),
					'approved_by' => Auth::user()->id,
					'approved_by_remark' =>$request->reason
				]);

			// If no row updated → invalid ID or already updated
			if ($updated === 0) {
				\DB::rollBack();

				return response()->json([
					'status'  => 'error',
					'message' => 'Summary not found or no changes applied'
				], 404);
			}

			\DB::commit();

			return response()->json([
				'status'  => 'success',
				'message' => 'Summary status updated successfully'
			]);

		} catch (\Throwable $e) {

			\DB::rollBack();

			\Log::error('Load Summary status update failed', [
				'summary_id' => $id,
				'status'     => $request->status,
				'error'      => $e->getMessage()
			]);

			return response()->json([
				'status'  => 'error',
				'message' => 'Failed to update summary status. Please try again.'
			], 500);
		}
	}
	
	/**
     *lop Allocation Automatic
	 */
	
	/*public function loadsummary_auto_allocation()
	{
		
		
		$qualifiedloads = LoadSummary::where('is_qualified', 1)									
									->orderBy('is_qualified')
									->orderBy('reference_no')
									->get();
		return view('admin.loadoptimizer.loadsummary_auto_allocation', compact('qualifiedloads'));
	}*/
	
	/**
     * AJAX batch allocation
     */
  public function vendorAutoAllocationProcess(Request $request)
	{
		
		    // Ensure tracking row exists
		$run = DB::table('allocation_runs')
			->where('run_type', 'MANUAL_AUTO_ALLOCATION')
			->first();

		if (!$run) {
			DB::table('allocation_runs')->insert([
				'run_type'    => 'MANUAL_AUTO_ALLOCATION',
				'last_run_at' => '2026-01-01 00:00:00',
				'created_at'  => now(),
				'updated_at'  => now(),
			]);

			$run = DB::table('allocation_runs')
				->where('run_type', 'MANUAL_AUTO_ALLOCATION')
				->first();
		}

		//Latest manual load time
		$latestManualLoad = ManualLoadSummary::max('created_at');

		// No data yet
		if (!$latestManualLoad) {
			return response()->json([
				'completed' => false,
				'message'   => 'No manual loads exist'
			]);
		}

		// Stop if nothing new
		if ($latestManualLoad <= $run->last_run_at) {
			return response()->json([
				'completed' => false,
				'message'   => 'No new manual loads found'
			]);
		}
		
		$limit  = 10;
		$offset = $request->offset ?? 0;
		$created_by  = Auth::user()->id;
		$createddate = now();

		$errors = [];
		$processed = 0;

		// Fetch MANUAL load summaries without vendor
		$loads = ManualLoadSummary::where(function ($q) {
				$q->whereNull('vendor_code')
				  ->orWhere('vendor_code', '')
				  ->orWhere('vendor_code', 'NA');
			})
			->orderBy('reference_no')
			->offset($offset)
			->limit($limit)
			->get();

		if ($loads->isEmpty()) {
			return response()->json([
				'completed' => true,
				'processed' => 0,
				'errors'    => [],
			]);
		}

		foreach ($loads as $load) {

			try {

				DB::transaction(function () use (
					$load,
					$created_by,
					$createddate,
					&$processed
				) {

					/* STEP 1: Fetch ONLY Rank 1 vendor from Rate Master */
					$vendor = Ratedata::where('consignor_code', trim($load->origin_name_code))
						->where('consignee_code', trim($load->destination_name_code))
						->where('t_code', trim($load->truck_code))
						->where('rank', 1)
						->first();

					if (!$vendor) {
						throw new \Exception('No Rank 1 vendor found in rate master');
					}

					/* STEP 2: Update MANUAL load summary */
					$load->vendor_name = $vendor->vendor_name;
					$load->vendor_code = $vendor->vendor_code;
					$load->vendor_rank = $vendor->rank;
					$load->vendor_code_source = 'Auto Allocation';
					$load->vendor_code_updated_at = $createddate;
					$load->save();

					/* STEP 3: Allocation history (for tracking only) */
					ManualAllocationHistory::create([
						'load_summary_id' => $load->id,
						'vendor_code'     => $vendor->vendor_code,
						'vendor_name'     => $vendor->rankvendor_name,
						'vendor_rank'     => $vendor->rank,
						'origin_code'     => $load->origin_name_code,
						'destination_code'=> $load->destination_name_code,
						'truck_type'      => $load->truck_code,
						'cycle_total'     => null, // not applicable for manual
						'allocated_by'    => $created_by,
						'allocated_at'    => $createddate,
					]);

					$processed++;
				});
				
				

			} catch (\Exception $e) {

				$errors[] = [
					'load_id'      => $load->id,
					'reference_no' => $load->reference_no,
					'origin'       => $load->origin_name_code,
					'destination'  => $load->destination_name_code,
					'truck'        => $load->truck_code,
					'reason'       => $e->getMessage(),
				];
			}
		}
		//  UPDATE LAST RUN TIME
    DB::table('allocation_runs')
        ->where('run_type', 'MANUAL_AUTO_ALLOCATION')
        ->update([
            'last_run_at' => now(),
            'updated_at'  => now()
        ]);
		return response()->json([
			'completed' => true,
			'processed' => $processed,
			'failed'    => count($errors),
			'errors'    => $errors,
		]);
	}

	
	//Edit Loadsummary allocated vendor
	public function editVendor($id)
	{
		$load = LoadSummary::findOrFail($id);

		$vendors = Ratedata::where([
			'consignor_code' => $load->origin_name_code,
			'consignee_code' => $load->destination_name_code,
			't_code'         => $load->truck_code,
		])
		->orderBy('rank')
		->get();

		return response()->json([
			'load'    => $load,
			'vendors' => $vendors
		]);
	}
	
	public function updateVendor(Request $request)
	{
		$request->validate([
			'load_summary_id' => 'required',
			'vendor_code'     => 'required',
			'remarks'         => 'required|min:5'
		]);

		$load = LoadSummary::findOrFail($request->load_summary_id);

		DB::transaction(function () use ($request, $load) {

			// Save history
			AllocationEditHistory::create([
				'load_summary_id' => $load->id,
				'old_vendor_code' => $load->vendor_code,
				'old_vendor_name' => $load->vendor_name,
				'old_vendor_code_source' => $load->vendor_code_source,
				'new_vendor_code' => $request->vendor_code,
				'new_vendor_name' => $request->vendor_name,
				'remarks'         => $request->remarks,
				'edited_by'       => Auth::id(),
				'edited_at'       => now(),
			]);

			// Update load
			$load->vendor_code = $request->vendor_code;
			$load->vendor_name = $request->vendor_name;
			$load->vendor_code_source = 'Manual Edit';
			$load->vendor_code_updated_at = now();
			$load->save();
		});

		return response()->json(['status' => 'success']);
	}
	
	
	
	public function sendToVendor(Request $request)
	{
		$request->validate([
			'load_id' => 'required|exists:load_summary,id',
			'remarks' => 'required|string|max:500'
		]);

		DB::transaction(function () use ($request) {

			$load = LoadSummary::lockForUpdate()->find($request->load_id);

			if ($load->sent_status === 'sent_to_vendor') {
				throw new \Exception('Load already sent to vendor');
			}

			$vendorRank = (int) $load->vendor_rank;
			$source     = $load->allocation_source; // Auto Allocation | Edit manual

			/**
			 * ==================================================
			 * AUTO ALLOCATION → ALWAYS SEND TO VENDOR
			 * ==================================================
			 */
			if ($source === 'Auto Allocation') {

				$this->markSentToVendor($load, $request->remarks);

				LoadSendHistory::create([
					'load_summary_id'  => $load->id,
					'vendor_code'      => $load->vendor_code,
					'vendor_rank'      => $vendorRank,
					'allocation_source'=> 'Auto Allocation',
					'remarks'          => $request->remarks,
					'sent_by'          => Auth::user()->id,
					'sent_at'          => now(),
					'status'           => 'pending'
				]);

				return;
			}

			/**
			 * ==================================================
			 * MANUAL + RANK >= 2  SEND TO APPROVER
			 * ==================================================
			 */
			if ($source === 'Manual Edit' && $vendorRank >= 2) {

				$load->sent_status = 'approval_required';
				$load->save();

				LoadSendHistory::create([
					'load_summary_id'  => $load->id,
					'vendor_code'      => $load->vendor_code,
					'vendor_rank'      => $vendorRank,
					'allocation_source'=> 'Manual Edit',
					'remarks'          => $request->remarks,
					'sent_by'          => Auth::user()->id,
					'sent_at'          => now(),
					'status'           => 'approval_required'
				]);

				return;
			}

			/**
			 * ==================================================
			 * MANUAL + RANK 1 → SEND TO VENDOR
			 * ==================================================
			 */
			$this->markSentToVendor($load, $request->remarks);

			LoadSendHistory::create([
				'load_summary_id'  => $load->id,
				'vendor_code'      => $load->vendor_code,
				'vendor_rank'      => $vendorRank,
				'allocation_source'=> 'Manual Edit',
				'remarks'          => $request->remarks,
				'sent_by'          => auth()->id(),
				'sent_at'          => now(),
				'status'           => 'pending'
			]);
		});

		return response()->json([
			'status'  => true,
			'message' => 'Load processed successfully'
		]);
	}

	/**
	 * Helper: Mark load as sent to vendor
	 */
	private function markSentToVendor($load, $remarks)
	{
		$load->sent_to_vendor = 1;
		$load->sent_status    = 'sent_to_vendor';
		$load->sent_remarks   = $remarks;
		$load->sent_by        = Auth::user()->id;
		$load->sent_at        = now();
		$load->save();
	}
	
	
	public function vendorLoads()
	{ 
		 
		$vendorCode = Auth::user()->vendor_code;
		
		if(!empty($vendorCode))
		{
			/*$loads = LoadSummary::where('vendor_code', $vendorCode)
					->where('sent_to_vendor', 1)
					->orderBy('sent_at', 'desc')
					->get();*/
		$loads = LoadSummary::whereHas('sendHistory', function ($q) use ($vendorCode) {
            $q->where('vendor_code', $vendorCode);
        })
        ->with(['sendHistory' => function ($q) use ($vendorCode) {
            $q->where('vendor_code', $vendorCode)
              ->latest('id')
              ->limit(1);
        }])
        ->orderByDesc('sent_at')
        ->get();
		
		}
		else
		{
			$loads = LoadSummary::where('sent_to_vendor', 1)->with('sendHistory')
					->orderBy('sent_at', 'desc')
					->get();
		}			
		//return view('vendor.loads', compact('loads'));
		
		return view('admin.loadoptimizer.allocation_sent_to_vendor', compact('loads'));
	}
	
	public function sendPreview($id)
	{
		$load = LoadSummary::with('truck')->findOrFail($id);

		return response()->json([
			'reference' => $load->reference_no,
			'route'     => $load->origin_name_code . ' → ' . $load->destination_name_code,
			'truck'     => $load->truck->truck_code ?? $load->truck_code ?? 'NA'
		]);
	}
	
	
	/*public function placementStatus()
	{ 
		 
		$vendorCode = Auth::user()->vendor_code;
		
		if(!empty($vendorCode))
		{
			
		$loads = LoadSummary::where('sent_status', 'accepted')
		->whereHas('sendHistory', function ($q) use ($vendorCode) {
            $q->where('vendor_code', $vendorCode);
        })
        ->with(['sendHistory' => function ($q) use ($vendorCode) {
            $q->where('vendor_code', $vendorCode)
              ->latest('id')
              ->limit(1);
        }])
        ->orderByDesc('sent_at')
        ->get();
		
		}
		else
		{
			$loads = LoadSummary::where('sent_status', 'accepted')
			->with('sendHistory')
					->orderBy('sent_at', 'desc')
					->get();
		}			
			
		return view('admin.loadoptimizer.change_placement_status', compact('loads'));
	}*/
	
	public function placementStatus()
	{
		$vendorCode = Auth::user()->vendor_code;

		if(!empty($vendor_code))
		{
		$loads = LoadSummary::where('sent_status', 'accepted')
			->whereHas('sendHistory', function ($q) use ($vendorCode) {
				$q->where('vendor_code', $vendorCode);
			})
			->with([
				'latestPlacement',
				'placementLogs',
				'sendHistory' => function ($q) use ($vendorCode) {
					$q->where('vendor_code', $vendorCode)
					  ->latest('id')
					  ->limit(1);
				}
			])
			->orderByDesc('sent_at')
			->get();
		}
		else{
			$loads = LoadSummary::where('sent_status', 'accepted')
			->with('sendHistory')
					->orderBy('sent_at', 'desc')
					->get();
		}
		return view(
			'admin.loadoptimizer.change_placement_status',
			compact('loads')
		);
	}
	
	///track placement status
	public function trackplacementStatus()
	{
		$vendorCode = Auth::user()->vendor_code;

		if(!empty($vendor_code))
		{
		$loads = LoadSummary::where('sent_status', 'accepted')
			->whereHas('sendHistory', function ($q) use ($vendorCode) {
				$q->where('vendor_code', $vendorCode);
			})
			->with([
				'latestPlacement',
				'placementLogs','latestSendHistory', 
				'sendHistory' => function ($q) use ($vendorCode) {
					$q->where('vendor_code', $vendorCode)
					  ->latest('id')
					  ->limit(1);
				}
			])
			->orderByDesc('sent_at')
			->get();
		}
		else{
			$loads = LoadSummary::where('sent_status', 'accepted')
			->with('sendHistory')
					->orderBy('sent_at', 'desc')
					->get();
		}
		return view(
			'admin.loadoptimizer.track_placement_status',
			compact('loads')
		);
	}
	
	public function placementHistory(Request $request, $loadId)
	{
		
		$history = LoadPlacementStatusLog::with('admin')
        ->where('load_summary_id', $loadId)
        ->orderBy('id','desc')
        ->get();

    return response()->json($history);
	}
	
	
}
