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
use App\Models\LoadSummary;
use App\Models\ManualLoadSummary;
use App\Models\LoadOptimizerItemHistory;
use App\Models\Ratedata;
use App\Models\AllocationHistory;
use App\Models\AllocationEditHistory;
use App\Models\LoadSendHistory;
use App\Models\LoadPlacementStatusLog;
use App\Services\LoadOptimizerService;

use Auth;


class LoadoptimizerController extends Controller
{
   //
	protected $service;

    public function __construct(LoadOptimizerService $service)
    {
        $this->service = $service;
		$this->middleware('auth:admin'); 
    }

	
	public function index(Request $request)
    {
        $title = 'Load optimizer Data Upload';
        $pagetitle = $title.' Listing';
        $userid = auth()->user()->id;    
        return view('admin.loadoptimizer.index',compact(['pagetitle','title']));
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

        unset($rows[0]); // remove header row
		
		$created_by = Auth::user()->id; 
		
		$createddate = date('Y-m-d');

		$errorRows = [];
		$insertedCount = 0;
		$validData = [];
		DB::beginTransaction();

        try {

            $groups = [];

            foreach ($rows as $row) {

                // Excel column mapping
                $originCode      = trim($row['A'] ?? '');
                $destinationCode = trim($row['C'] ?? '');
                $skuCode         = trim($row['E'] ?? '');

                if (!$originCode || !$destinationCode || !$skuCode) {
                    continue;
                }

                $groupKey = $originCode . '_' . $destinationCode;
                $groups[$groupKey][] = $row;
            }

            $insertedCount = 0;

            foreach ($groups as $groupRows) {

                // ONE reference per group
                $referenceNo = $this->generateNextReference();

                foreach ($groupRows as $row) {

                    $originCode      = trim($row['A']);
                    $destinationCode = trim($row['C']);
                    $skuCode         = trim($row['E']);

                    $priority  = $row['G'] ?? null;
                    $skuClass  = $row['H'] ?? null;
                    $rdd       = $row['I'] ?? null;
                    $tMode     = $row['J'] ?? null;
                    $qty       = $row['K'] ?? null;

                    $originCity = DB::table('site_plants')
						->where('plant_site_code', $originCode)
						->value('city');

					$destinationCity = DB::table('site_plants')
						->where('plant_site_code', $destinationCode)
						->value('city');

					$material = DB::table('materials')
						->where('material_code', $skuCode)
						->first();	
						
					if (!$material) {
						Log::warning('Invalid SKU skipped', [
							'sku_code' => $row['E'],
							'row' => $row
						]);
						continue;
					}
						
					
					$sku_description = $material->material_description;
					$total_weight = round($material->gross_weight_kg * $qty, 2);
					$total_volume = round($material->volume_cft * $qty, 2);	
								
				$lopq=	Loadoptimizer::create([
					'reference_no'           => $referenceNo,
					'origin_name_code'       => $originCode,
					'origin_name'            => $originCity,
					'destination_name_code'  => $destinationCode,
					'destination_city'       => $destinationCity,
					'sku_code'               => $skuCode,
					'sku_description'        => $sku_description ?? null,
					'priority'               => $priority,
					'sku_class'              => $skuClass,
					'required_delivery_date' => $rdd ? date('Y-m-d', strtotime($rdd)) : null,
					't_mode'                 => $tMode,
					'qty'                    => $qty,
					'z_total_weight'         => $total_weight,
					'z_total_volume'         => $total_volume,
					'status'                 => 1,
					'created_by'             => $created_by,
					'created_at'             => $createddate,
				]);
                    $insertedCount++;
                }

                if ($insertedCount > 0) {
					app(LoadOptimizerService::class)->generate($referenceNo);
				} else {
					Log::warning("Summary skipped – no valid SKU rows", [
						'reference_no' => $referenceNo
					]);
				}
            }

            DB::commit();

            if ($insertedCount === 0) {
                return back()->with('error', 'No valid rows found in Excel.');
            }

            return back()->with('success', "{$insertedCount} rows inserted successfully.");

        } catch (\Exception $e) {

            DB::rollBack();

            Log::error('Load Optimizer XLS Upload Error', [
                'error' => $e->getMessage()
            ]);

            return back()->with('error', 'Something went wrong during upload.'. $e->getMessage());
        }
        
	}
	
	
	public function loadoptimizerdatalist(Request $request)
    {
        $title = 'Loadoptimizer Data Upload';
        $pagetitle = $title.' Listing';
		$user_role = Auth::user()->role_id;
		$data = $request->all();        
	    $lopdatalist = Loadoptimizer::orderBy('created_at', 'desc')->get();       
        return view('admin.materialdata.materialdatalist',compact(['pagetitle','title','lopdatalist','user_role']));
    }
	
	//AJAX: Fetch dependent data : origin city name , desination city, sku description
   
    public function fetchRowData(Request $request)
    {
        $origin = $request->origin_code;
        $destination = $request->destination_code;
        $sku = $request->sku_code;
        $qty = (float) $request->qty;
		 
		 
        $originCity = DB::table('site_plants')
            ->where('plant_site_code', $origin)
            ->value('city');
			
			if (empty($originCity)) {
            return response()->json(['error'=>'Invalid origin master data']);
        }

        $destinationCity = DB::table('site_plants')
            ->where('plant_site_code', $destination)
            ->value('city');
			
		if (empty($destinationCity)) {
            return response()->json(['error'=>'Invalid destinationCity master data']);
        }
			
		$material = DB::table('materials')
            ->where('material_code', $sku)
            ->first();
		if (empty($material)) {
            return response()->json(['error'=>'Invalid material master data']);
        }
		 

        return response()->json([
            'origin_city' => $originCity,
            'destination_city' => $destinationCity,
            'sku_description' => $material->material_description,
            'total_weight' => round($material->gross_weight_kg * $qty, 2),
            'total_volume' => round($material->volume_cft * $qty, 2),
        ]);
    }

	//manual Upload
	
	public function manualupload()
	{
		$userid = auth()->user()->id; //get loggedin user id		
		return view('admin.loadoptimizer.manualupload');
	}
	private function generateNextReference()
	{
		$lastRef = Loadoptimizer::whereNotNull('reference_no')
			->orderBy('id', 'desc')
			->value('reference_no');

		if (!$lastRef) {
			//return 'S000001';
		}

		$num = (int) substr($lastRef, 1);
		return 'S' . str_pad($num + 1, 6, '0', STR_PAD_LEFT);
	}
	
	public function save_manual_data(Request $request)
	{
		$created_by   = Auth::user()->id;
		$createddate  = now();

		$origin_name_code      = $request->origin_name_code ?? [];
		$origin_name           = $request->origin_name ?? [];
		$destination_name_code = $request->destination_name_code ?? [];
		$destination_city      = $request->destination_name ?? [];
		$sku_code              = $request->sku_code ?? [];
		$sku_description       = $request->sku_description ?? [];
		$priority              = $request->priority ?? [];
		$sku_class             = $request->sku_class ?? [];
		$required_delivery_date = $request->required_delivery_date ?? [];
		$t_mode                = $request->t_mode ?? [];		
		$qty                   = $request->qty ?? [];
		$z_total_weight        = $request->z_total_weight ?? [];
		$z_total_volume        = $request->z_total_volume ?? [];

		$count = count($origin_name_code);
		$insertedCount = 0;

		DB::beginTransaction();

		try {

			
				$groups = [];

				foreach ($origin_name_code as $i => $origin) {
					if (empty($origin)) {
						continue;
					}
					$key = $origin . '_' . $destination_name_code[$i];
					$groups[$key][] = $i;
				}

				   foreach ($groups as $groupKey => $indexes) 
				   {

					//Generate ONE reference per group
					$referenceNo = $this->generateNextReference();

					foreach ($indexes as $i) 
					{
						
						$deliveryrequireddate = date('Y-m-d', strtotime($required_delivery_date[$i]));
						
						Loadoptimizer::create([
							'reference_no'           => $referenceNo,
							'origin_name_code'       => $origin_name_code[$i],
							'origin_name'            => $origin_name[$i] ?? null,
							'destination_name_code'  => $destination_name_code[$i],
							'destination_city'       => $destination_city[$i] ?? null,
							'sku_code'               => $sku_code[$i] ?? null,
							'sku_description'        => $sku_description[$i] ?? null,
							'priority'               => $priority[$i] ?? null,
							'sku_class'              => $sku_class[$i] ?? null,
							'required_delivery_date' => $deliveryrequireddate ?? null,
							't_mode'                 => $t_mode[$i] ?? null,						
							'qty'                    => $qty[$i] ?? null,
							'z_total_weight'         => $z_total_weight[$i] ?? null,
							'z_total_volume'         => $z_total_volume[$i] ?? null,
							'status'                 => 1,
							'created_by'             => $created_by,
							'created_at'             => $createddate,
						]);

						$insertedCount++;
				}
				 /**  AUTO LOAD SUMMARY */
				app(LoadOptimizerService::class)->generate($referenceNo);
			}

			DB::commit();

			/*if ($insertedCount === 0) {
				return back()->with('error', 'No valid rows found to insert.');
			}*/
			if ($insertedCount === 0) {
            // No data inserted
            return back()
                ->withInput()
                ->with([
                    'errorRows' => $errorRows,
                    'error' => 'No data inserted. Please correct the highlighted errors.',
                ]);
			}
			return back()->with('success', "{$insertedCount} rows inserted successfully.");

		} catch (\Exception $e) {

			DB::rollBack();
			Log::error('Load Optimizer Insert Error', [
				'error' => $e->getMessage()
			]);
			return back()->with('error', $e->getMessage());
			//return redirect()->back()->with('error', 'Error: Something went wrong.');
		}
	}

	////Load summary
		
		
	public function loadsummary()
	{
		
		// Fetch summaries (unqualified first)
		$loads = LoadSummary::where('is_qualified', 0)
			->orderBy('id', 'desc')
			->get();

		return view('admin.loadoptimizer.load_summary', compact('loads'));
	}
		
		public function qualifiedloadsummary()
		{
			
			// Fetch summaries (qualified first)
			$qualifiedloads = LoadSummary::where('is_qualified', 1)->orderBy('is_qualified')
				->orderBy('reference_no')
				->get();

			return view('admin.loadoptimizer.qualified_load_summary', compact('qualifiedloads'));
		}
		
				
		public function viewLoadedItems($referenceNo)
		{
			$summary = LoadSummary::where('reference_no', $referenceNo)->firstOrFail();

			$items = DB::table('load_summary_items as lsi')
				->join('load_optimizer as lo', 'lo.id', '=', 'lsi.load_optimizer_id')
				->where('lsi.reference_no', $referenceNo)
				->whereNull('lo.deleted_at')
				->get();
			if($summary->is_qualified==1)
			{
				return view('admin.loadoptimizer.qualifieditems', compact('summary', 'items','referenceNo'));
			}
			else
			{
				return view('admin.loadoptimizer.items', compact('summary', 'items','referenceNo'));
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
	

   
    public function getSku($code)
    {
		$material = DB::table('materials')
            ->where('material_code', $code)
            ->first();
		
        return response()->json([
            'description' => $material->material_description,
            'weight'      => $material->gross_weight_kg,
            'volume'      => $material->volume_cft
            
        ]);
    }
	
	public function calculateUtil(Request $request)
	{
		$request->validate([
			'total_weight' => 'required|numeric',
			'total_volume' => 'required|numeric',
			'truck_id'     => 'required|integer',
		]);

		$truck = \DB::table('truck_master')->find($request->truck_id);

		if (!$truck) {
			return response()->json(['error' => 'Truck not found'], 404);
		}

		$zw = round(($request->total_weight / $truck->weight_capacity) * 100, 2);
		$zv = round(($request->total_volume / $truck->max_volume_capacity) * 100, 2);

		return response()->json([
			'zw_util'    => $zw,
			'zv_util'    => $zv,
			'gross_util' => max($zw, $zv),
		]);
	}
	
	///
	 /**
     * Update SKUs & regenerate summary
     */
     public function updateSummaryItems(Request $request, $ref)
    {
        $request->validate([
            'items' => 'required|array'
        ]);

        DB::transaction(function () use ($request, $ref) {
            $this->service->updateLoadOptimizerItems(
                $ref,
                $request->items,
               Auth::user()->id
            );
        });

        /*return redirect()
            ->back()
            ->with('success', 'Summary updated successfully');*/
			return response()->json([
			'status'  => 'success',
			'message' => 'Summary updated successfully'
			]);
    }
	
	////delete items
	
	public function deleteItem(Request $request, int $id)
    {
        DB::transaction(function () use ($id) {

            $item = Loadoptimizer::whereNull('deleted_at')
                ->lockForUpdate()
                ->findOrFail($id);

            /** -----------------------------
             * 1️ STORE FULL HISTORY
             * -----------------------------*/
			 $created_by   = Auth::user()->id;
            LoadOptimizerItemHistory::create([
                'load_optimizer_id' => $item->id,
                'reference_no'      => $item->reference_no,
                'origin_name_code'  => $item->origin_name_code,
                'destination_name_code'=> $item->destination_name_code,
                'sku_code'          => $item->sku_code,
                'sku_description'   => $item->sku_description,
                'priority'          => $item->priority,
                'sku_class'         => $item->sku_class,
                't_mode'            => $item->t_mode,
                'qty'               => $item->qty,
                'z_total_weight'    => $item->z_total_weight,
                'z_total_volume'    => $item->z_total_volume,
                'action'            => 'DELETED',
                'edited_by'         => $created_by,
                'edited_at'         => now(),
            ]);

            /** -----------------------------
             * 2️ SOFT DELETE
             * -----------------------------*/
            $item->delete(); // uses SoftDeletes
        });

        return response()->json([
            'status'  => 'success',
            'message' => 'Item deleted successfully'
        ]);
    }
	
	///Summary Approval / Reject
	/*public function loadSummaryApproval()
	{		
		// Fetch summaries (unqualified first) to approve / reject
		$loads = LoadSummary::where('is_qualified', 0)
				->where('approval_status','SENT_FOR_APPROVAL')
				->orderBy('id', 'desc')
				->get();
		return view('admin.loadoptimizer.approve_reject_load_summary', compact('loads'));
	}*/
	
	public function loadSummaryApproval()
	{
		/**
		 * ===============================
		 * AUTO LOADS (load_summary)
		 * ===============================
		 */
		$autoLoads = LoadSummary::where('is_qualified', 0)
			->where('approval_status', 'SENT_FOR_APPROVAL')
			->select([
				'id',
				'reference_no',
				'origin_name_code',
				'destination_name_code',
				't_mode',
				'truck_code',
				'zw_util',
				'zv_util',
				'gross_util',
				'approval_status',
				'approval_remark',
				'approved_at',
				'vendor_code',
				'vendor_rank',
				DB::raw("'AUTO' as source_type")
			]);

		/**
		 * ===============================
		 * MANUAL LOADS (manual_load_summary)
		 * ===============================
		 */
		$manualLoads = \DB::table('manual_load_summary')
			->where('approval_status', 'SENT_FOR_APPROVAL')
			->select([
				'id',
				'reference_no',
				'origin_name_code',
				'destination_name_code',
				't_mode',
				'truck_code',
				DB::raw('NULL as zw_util'),
				DB::raw('NULL as zv_util'),
				DB::raw('NULL as gross_util'),
				'approval_status',
				'approval_remark',
				'approved_at',
				'vendor_code',
				'vendor_rank',
				DB::raw("'MANUAL' as source_type")
			]);

		/**
		 * ===============================
		 * MERGE + ORDER
		 * ===============================
		 */
		$loads = $autoLoads
			->unionAll($manualLoads)
			->orderBy('id', 'desc')
			->get();

		return view('admin.loadoptimizer.approve_reject_load_summary',compact('loads'));
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
			  $table = $request->source_type === 'AUTO' ? 'load_summary' : 										'manual_load_summary';
			$updated = \DB::table($table)
				->where('id', $id)
				->update([
					'approval_status' => $request->status,
					'is_qualified' => $isQualified,
					'approved_at'   => now(),
					'approved_by' => Auth::user()->id,
					'approved_by_remark' =>$request->reason
				]);

			// If no row updated invalid ID or already updated
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
	
	public function loadsummary_auto_allocation()
	{		
		/*$qualifiedloads = LoadSummary::where('is_qualified', 1)									
									->orderBy('is_qualified')
									->orderBy('reference_no')
									->get();*/
		$autoLoads = DB::table('load_summary as ls')
        ->leftJoin('truck_master as tm', 'tm.id', '=', 'ls.truck_id')
        ->where('ls.is_qualified', 1)
        ->select([
            'ls.id',
            'ls.reference_no',
            'ls.origin_name_code',
            'ls.destination_name_code',
            'ls.origin_name',
            'ls.destination_name',
            'ls.t_mode',
            'ls.truck_code',
            'tm.description as truck_type',
            'ls.total_weight',
            'ls.total_volume',
            'ls.zw_util',
            'ls.zv_util',
            'ls.gross_util',
            'ls.vendor_code',
            'ls.vendor_name',
            'ls.vendor_rank',
            'ls.sent_status',
            'ls.created_at',
			'ls.vendor_code_source',
            DB::raw("'AUTO' as source_type")
        ]);


    /* ======================
     * MANUAL INDENT
     * ====================== */
    $rateSub = DB::table('rate_master')
    ->select('t_code', 'truck_type')
    ->where('rank', 1)
    ->distinct(); // 
	$manualLoads = DB::table('manual_load_summary as ms')
    ->leftJoinSub($rateSub, 'rm', function ($join) {
        $join->on('rm.t_code', '=', 'ms.truck_code');
    })
    ->select([
        'ms.id',
        'ms.reference_no',
        'ms.origin_name_code',
        'ms.destination_name_code',
        'ms.origin_name',
        'ms.destination_name',
        'ms.t_mode',
        'ms.truck_code',
        'rm.truck_type',
        'ms.total_weight',
        'ms.total_volume',
        DB::raw('NULL as zw_util'),
        DB::raw('NULL as zv_util'),
        DB::raw('NULL as gross_util'),
        'ms.vendor_code',
        'ms.vendor_name',
        'ms.vendor_rank',
        'ms.sent_status',
        'ms.created_at',
        'ms.vendor_code_source',
        DB::raw("'MANUAL' as source_type")
    ]);

		$qualifiedloads = $autoLoads
        ->unionAll($manualLoads)
        ->orderBy('reference_no')
        ->get();
									
		return view('admin.loadoptimizer.loadsummary_auto_allocation', compact('qualifiedloads'));
	}
	
	/**
     * AJAX batch allocation
     */
    public function processAutoAllocation(Request $request)
	{
		
		// Ensure tracking row exists
		$run = DB::table('allocation_runs')
			->where('run_type', 'AUTO_ALLOCATION')
			->first();

		if (!$run) {
			DB::table('allocation_runs')->insert([
				'run_type'    => 'AUTO_ALLOCATION',
				'last_run_at' => '2026-01-01 00:00:00',
				'created_at'  => now(),
				'updated_at'  => now(),
			]);

			$run = DB::table('allocation_runs')
				->where('run_type', 'AUTO_ALLOCATION')
				->first();
		}

		//Latest manual load time
		$latestLoad = LoadSummary::max('created_at');

		// No data yet
		if (!$latestLoad) {
			return response()->json([
				'completed' => false,
				'message'   => 'No Indent exist'
			]);
		}

		// Stop if nothing new
		if ($latestLoad <= $run->last_run_at) {
			return response()->json([
				'completed' => false,
				'message'   => 'No new Indent found'
			]);
		}
		
		
		//$limit  = 10;
		//$offset = $request->offset ?? 0;
		$created_by  = Auth::user()->id;
		$createddate = now();

		$errors = [];
		$processed = 0;
		
		//Get all qualified load summary //////
		
		$loads = LoadSummary::where('is_qualified', 1)
			->where(function ($q) {
				$q->whereNull('vendor_code')
				  ->orWhere('vendor_code', '')
				  ->orWhere('vendor_code', 'NA');
			})
			->orderBy('reference_no')->get();
			//->offset($offset)
			//->limit($limit)
			

		if ($loads->isEmpty()) {
			return response()->json([
				'completed' => true,
				'processed' => 0,
				'errors'    => [],
			]);
		}

		foreach ($loads as $load) {

			try {

					DB::transaction(function () use ($load, $created_by,$createddate,&$processed) 
				{

					/* STEP 1: Fetch vendors */
					$vendors = Ratedata::where('consignor_code', trim($load->origin_name_code))
						->where('consignee_code', trim($load->destination_name_code))
						->where('t_code', trim($load->truck_code))
						->orderBy('rank')
						->get();

					if ($vendors->isEmpty()) {
						throw new \Exception('No vendor rate found');
					}

					/* STEP 2: Cycle total custom 3 */
					$cycleTotal = (int) $vendors->sum('custom3');
					if ($cycleTotal <= 0) {
						throw new \Exception('Invalid cycle total');
					}
					

					/* STEP 3: Slot calculation from custom 2 stored as in % */
					$slots = [];
					$usedSlots = 0;

					foreach ($vendors as $vendor) {
						$slot = round(($vendor->custom2 / 100) * $cycleTotal);
						$slots[$vendor->rank] = $slot;
						$usedSlots += $slot;
					}

					$remaining = $cycleTotal - $usedSlots;
					if ($remaining > 0 && isset($slots[1])) {
						$slots[1] += $remaining;
					}

					/* STEP 4: Cycle position */
					$cycleUsed = AllocationHistory::where([
						'origin_code'      => $load->origin_name_code,
						'destination_code' => $load->destination_name_code,
						'truck_type'       => $load->truck_code,
					])->count();

					$cyclePosition = $cycleUsed % $cycleTotal;

					/* STEP 5: Vendor selection */
					$running = 0;
					$selectedVendor = null;

					foreach ($vendors as $vendor) {
						$running += $slots[$vendor->rank] ?? 0;
						if ($cyclePosition < $running) {
							$selectedVendor = $vendor;
							break;
						}
					}

					if (!$selectedVendor) {
						throw new \Exception('Vendor selection failed');
					}

					/* STEP 6: Update load summary table */
					$load->vendor_name = $selectedVendor->vendor_name;
					$load->vendor_code = $selectedVendor->vendor_code;
					$load->vendor_rank = $selectedVendor->rank;
					$load->vendor_code_source = 'Auto Allocation';
					$load->vendor_code_updated_at = $createddate;
					$load->save();

					/* STEP 7: History */
					AllocationHistory::create([
						'load_summary_id' => $load->id,
						'vendor_code'     => $selectedVendor->vendor_code,
						'vendor_name'     => $selectedVendor->vendor_name,
						'vendor_rank'     => $selectedVendor->rank,
						'origin_code'     => $load->origin_name_code,
						'destination_code'=> $load->destination_name_code,
						'truck_type'      => $load->truck_code,
						'cycle_total'     => $cycleTotal,
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
        ->where('run_type', 'AUTO_ALLOCATION')
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
	/*public function editVendor($id)
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
	}*/
	
	public function editVendor(Request $request)
	{
		$id     = $request->id;
		$source = $request->source;

		if ($source === 'AUTO') {
			$load = LoadSummary::findOrFail($id);
		} else {
			$load = ManualLoadSummary::findOrFail($id);
		}

		
		$vendors = Ratedata::where([
			'consignor_code' => $load->origin_name_code,
			'consignee_code' => $load->destination_name_code,
			't_code'         => $load->truck_code,
		])
		->orderBy('rank')
		->get();
	

		return response()->json([
			'load'    => $load,
			'vendors' => $vendors,
			'source'  => $source
		]);
	}
	
	public function updateVendor(Request $request)
	{
		$request->validate([
			'load_summary_id' => 'required',
			'vendor_code'     => 'required',
			'remarks'         => 'required|min:5'
		]);

		//$load = LoadSummary::findOrFail($request->load_summary_id);
		 if ($request->allocation_source === 'AUTO') {
			$load = LoadSummary::findOrFail($request->load_summary_id);
		} else {
        $load = ManualLoadSummary::findOrFail($request->load_summary_id);
		}

		DB::transaction(function () use ($request, $load) {

			// Save history
			AllocationEditHistory::create([
				'load_summary_id' => $load->id,
				'allocation_source' => $request->allocation_source, 
				'reference_no'            => $load->reference_no,
				'old_vendor_code' => $load->vendor_code,
				'old_vendor_name' => $load->vendor_name,
				'old_vendor_code_source' => $load->vendor_code_source,
				'new_vendor_code' => $request->vendor_code,
				'new_vendor_name' => $request->vendor_name,
				'remarks'         => $request->remarks,
				'edited_by'       => Auth::id(),
				'edited_at'       => now(),
			]);

			$vedor_code_str_arr = explode("Rank",$request->vendor_code);
			$vendor_code = $vedor_code_str_arr[0];
			$vendor_rank = $vedor_code_str_arr[1];
			// Update load
			$load->vendor_code = $vendor_code;
			$load->vendor_name = $request->vendor_name;
			$load->vendor_rank = $vendor_rank;
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
		//print_r($request); exit;
		
		DB::transaction(function () use ($request) {

			//$load = LoadSummary::lockForUpdate()->find($request->load_id);
			
			if ($request->source_type === 'AUTO') {
				$load = LoadSummary::lockForUpdate()->findOrFail($request->load_id);
			} else {
				$load = ManualLoadSummary::lockForUpdate()->findOrFail($request->load_id);
			}


			if ($load->sent_status === 'sent_to_vendor') {
				throw new \Exception('Load already sent to vendor');
			}

			$vendorRank = (int) $load->vendor_rank;
			$source     = $load->vendor_code_source; // Auto Allocation | Edit manual

			/**
			 * ==================================================
			 * AUTO ALLOCATION ALWAYS SEND TO VENDOR
			 * ==================================================
			 */
			if ($source === 'Auto Allocation') {

				$this->markSentToVendor($load, $request->remarks);

				LoadSendHistory::create([
					'load_summary_id'  => $load->id,
					'vendor_code'      => $load->vendor_code,
					'reference_no'		=> $load->reference_no,
					'source_type'		=> $request->source_type,
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
					'reference_no'		=> $load->reference_no,
					'source_type'		=> $request->source_type,
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
				'reference_no'		=> $load->reference_no,
				'source_type'		=> $request->source_type,
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
	
	
	/*public function vendorLoads()
	{ 
		 
		$vendorCode = Auth::user()->vendor_code;
		
		if(!empty($vendorCode))
		{
			
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
	}*/
	
	public function vendorLoads()
	{
		$user = Auth::user();

		/**
		 * ===============================
		 * AUTO LOADS (load_summary)
		 * ===============================
		 */
		$autoLoads = LoadSummary::query()
			->whereHas('sendHistory', function ($q) use ($user) {
				if (!empty($user->vendor_code)) {
					$q->where('vendor_code', $user->vendor_code);
				}
			})
			->with(['sendHistory' => function ($q) use ($user) {
				if (!empty($user->vendor_code)) {
					$q->where('vendor_code', $user->vendor_code);
				}
				$q->latest('id')->limit(1);
			}])
			->select([
				'id',
				'reference_no',
				'origin_name_code',
				'destination_name_code',
				't_mode',
				'truck_code',
				'zw_util',
				'zv_util',
				'gross_util',
				'vendor_code',
				'vendor_name',
				'sent_remarks',
				'sent_status',
				'sent_at',
				DB::raw("'AUTO' as source_type")
			]);

		/**
		 * ===============================
		 * MANUAL LOADS (manual_load_summary)
		 * ===============================
		 */
		$manualLoadsQuery = DB::table('manual_load_summary as mls')
			->join('load_send_history as lsh', function ($join) {
				$join->on('lsh.load_summary_id', '=', 'mls.id')
					 ->where('lsh.source_type', 'MANUAL');
			});

		// Restrict only for vendor
		if (!empty($user->vendor_code)){
			$manualLoadsQuery->where('lsh.vendor_code', $user->vendor_code);
		}

		$manualLoads = $manualLoadsQuery->select([
			'mls.id',
			'mls.reference_no',
			'mls.origin_name_code',
			'mls.destination_name_code',
			'mls.t_mode',
			'mls.truck_code',
			DB::raw('NULL as zw_util'),
			DB::raw('NULL as zv_util'),
			DB::raw('NULL as gross_util'),
			'mls.vendor_code',
			'mls.vendor_name',
			'mls.sent_remarks',
			'mls.sent_status',
			'mls.sent_at',
			DB::raw("'MANUAL' as source_type")
		]);

		
		$loads = $autoLoads
			->unionAll($manualLoads)
			->orderByDesc('sent_at')
			->get();

		return view(
			'admin.loadoptimizer.allocation_sent_to_vendor',
			compact('loads')
		);
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
	

/*	public function placementStatus()
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
	}*/
	
	public function placementStatus()
	{
		$user = Auth::user();

		/**
		 * ===============================
		 * AUTO LOADS
		 * ===============================
		 */
		$autoLoads = LoadSummary::where('sent_status', 'accepted')

			->when(!empty($user->vendor_code), function ($q) use ($user) {
				$q->whereHas('sendHistory', function ($sh) use ($user) {
					$sh->where('vendor_code', $user->vendor_code);
				});
			})

			->with([
				'latestPlacement',
				'placementLogs',
				'truck',
				'sendHistory' => function ($q) use ($user) {
					if (!empty($user->vendor_code)) {
						$q->where('vendor_code', $user->vendor_code);
					}
					$q->latest('id')->limit(1);
				}
			])

			->select([
				'id',
				'reference_no',
				'origin_name_code',
				'origin_name',
				'destination_name_code',
				'destination_name',
				't_mode',
				'truck_id',
				'truck_code',
				DB::raw('NULL as truck_name'),
				'total_weight',
				'total_volume',
				'vendor_name',
				'sent_at',
				DB::raw("'AUTO' as source_type")
			]);

		/**
		 * ===============================
		 * MANUAL LOADS
		 * ===============================
		 */
		$manualLoads = DB::table('manual_load_summary as mls')

			->join('load_send_history as lsh', function ($join) use ($user) {
				$join->on('lsh.load_summary_id', '=', 'mls.id')
					 ->where('lsh.source_type', 'MANUAL');

				if (!empty($user->vendor_code)) {
					$join->where('lsh.vendor_code', $user->vendor_code);
				}
			})

			->leftJoin('load_placement_status_logs as lps', 'lps.load_summary_id', '=', 'mls.id')

			->where('mls.sent_status', 'accepted')

			->select([
				'mls.id',
				'mls.reference_no',
				'mls.origin_name_code',
				'mls.origin_name',
				'mls.destination_name_code',
				'mls.destination_name',
				'mls.t_mode',
				DB::raw('NULL as truck_id'),
				'mls.truck_code',
				'mls.truck_name',
				'mls.total_weight',
				'mls.total_volume',
				'mls.vendor_name',
				'mls.sent_at',
				DB::raw("'MANUAL' as source_type")
			]);

		/**
		 * ===============================
		 * MERGE + SORT
		 * ===============================
		 */
		$loads = $autoLoads
			->unionAll($manualLoads)
			->orderByDesc('sent_at')
			->get();

		return view(
			'admin.loadoptimizer.change_placement_status',
			compact('loads')
		);
	}
	
	///track placement status
	/*public function trackplacementStatus()
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
	}*/
	public function trackplacementStatus()
	{
		$user = Auth::user();

		/**
		 * ===============================
		 * AUTO LOADS (load_summary)
		 * ===============================
		 */
		$autoLoads = LoadSummary::where('sent_status', 'accepted')

			->when(!empty($user->vendor_code), function ($q) use ($user) {
				$q->whereHas('sendHistory', function ($sh) use ($user) {
					$sh->where('vendor_code', $user->vendor_code);
				});
			})

			->with([
				'latestPlacement',
				'placementLogs',
				'truck',
				'sendHistory' => function ($q) use ($user) {
					if (!empty($user->vendor_code)) {
						$q->where('vendor_code', $user->vendor_code);
					}
					$q->latest('id')->limit(1);
				}
			])
			->select([
				'id',
				'reference_no',
				'origin_name_code',
				'origin_name',
				'destination_name_code',
				'destination_name',
				't_mode',
				'truck_code',
				DB::raw('NULL as truck_name'),
				'total_weight',
				'total_volume',
				'vendor_name',
				'sent_at',
				DB::raw("'AUTO' as source_type")
			])
			->get();

		/**
		 * ===============================
		 * MANUAL LOADS (manual_load_summary)
		 * ===============================
		 */
		$manualLoads = DB::table('manual_load_summary as mls')
			->leftJoin('load_send_history as lsh', function ($join) use ($user) {
				$join->on('lsh.load_summary_id', '=', 'mls.id')
					 ->where('lsh.source_type', 'MANUAL');

				if (!empty($user->vendor_code)) {
					$join->where('lsh.vendor_code', $user->vendor_code);
				}
			})
			->leftJoin('load_placement_status_logs as lps', 'lps.load_summary_id', '=', 'mls.id')
			->where('mls.sent_status', 'accepted')
			->select([
				'mls.id',
				'mls.reference_no',
				'mls.origin_name_code',
				'mls.origin_name',
				'mls.destination_name_code',
				'mls.destination_name',
				'mls.t_mode',
				'mls.truck_code',
				'mls.truck_name',
				'mls.total_weight',
				'mls.total_volume',
				'mls.vendor_name',
				'mls.sent_at',
				DB::raw("'MANUAL' as source_type"),
				DB::raw('MAX(lps.placement_status) as latest_placement_status')
			])
			->groupBy(
				'mls.id',
				'mls.reference_no',
				'mls.origin_name_code',
				'mls.origin_name',
				'mls.destination_name_code',
				'mls.destination_name',
				'mls.t_mode',
				'mls.truck_code',
				'mls.truck_name',
				'mls.total_weight',
				'mls.total_volume',
				'mls.vendor_name',
				'mls.sent_at'
			)
			->get();

		/**
		 * ===============================
		 * MERGE + SORT
		 * ===============================
		 */
		$loads = $autoLoads
			->merge($manualLoads)
			->sortByDesc('sent_at')
			->values();

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
