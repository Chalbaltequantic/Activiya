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

use Auth;


class LoadoptimizerController extends Controller
{
    //
	public function __construct()
    {
        $this->middleware('auth:admin');     
    }
	
	public function index(Request $request)
    {
        $title = 'Load optimizer Data Upload';
        $pagetitle = $title.' Listing';
        $userid = auth()->user()->id;    
        return view('admin.loadoptimizer.index',compact(['pagetitle','title']));
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
	
	
	 //AJAX: Fetch dependent data : city name , desination city, sku description
   
   public function fetchRowData(Request $request)
    {
        $origin = $request->origin_code;
        $destination = $request->destination_code;
        $sku = $request->sku_code;
        $qty = (float) $request->qty;
		 
		 
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
		$t_mode                = $request->t_mode ?? [];
		$qty                   = $request->qty ?? [];
		$z_total_weight        = $request->z_total_weight ?? [];
		$z_total_volume        = $request->z_total_volume ?? [];

		$count = count($origin_name_code);
		$insertedCount = 0;

		DB::beginTransaction();

		try {

			//for ($i = 0; $i < $count; $i++) {

				//if (empty($origin_name_code[$i]) || empty($destination_name_code[$i])) {
				//	continue;
				//}
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
			//return back()->with('error', $e->getMessage());
			return redirect()->back()->with('error', 'Error: Something went wrong.');
		}
	}

	////Load summary
		/*public function loadSummary()
		{
			$loads = DB::table('load_optimizer')
				->selectRaw("
					reference_no,
					origin_name_code,
					origin_name,
					destination_name_code,
					destination_city,
					t_mode,
					SUM(z_total_weight) as total_weight,
					SUM(z_total_volume) as total_volume
				")
				->groupBy(
					'reference_no',
					'origin_name_code',
					'origin_name',
					'destination_name_code',
					'destination_city',
					't_mode'
				)
				->orderBy('reference_no')
				->get();

			// Attach truck recommendation & utilization
			foreach ($loads as $load) {

				// Example truck auto selection
				$truck = \DB::table('truck_master')
					->where('weight_capacity', '<=', $load->total_weight)
					->where('max_volume_capacity', '<=', $load->total_volume)
					->orderBy('weight_capacity')
					->first();

				if ($truck) {
					$zw = round(($load->total_weight / $truck->weight_capacity) * 100);
					$zv = round(($load->total_volume / $truck->max_volume_capacity) * 100);
				} else {
					$zw = $zv = 0;
				}

				$load->truck_type = $truck->description ?? 'NA';
				$load->zw_util = $zw;
				$load->zv_util = $zv;
				$load->gross_util = max($zw, $zv);

				// Button color logic
				if ($load->gross_util < 80) {
					$load->btn_color = 'red';
				} elseif ($load->gross_util >= 81 && $load->gross_util <= 90) {
					$load->btn_color = 'yellow';
				} elseif ($load->gross_util >= 91 ) {
					$load->btn_color = 'green';
				} else {
					$load->btn_color = 'blue';
				}
			}

			return view('admin.loadoptimizer.load_summary', compact('loads'));
		}*/		
		
		public function loadsummary()
		{
			$this->generateSummary();

			// Fetch summaries (unqualified first)
			$loads = LoadSummary::where('is_qualified', 0)->orderBy('is_qualified')
				->orderBy('reference_no')
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
		/*private function generateSummary()
		{
			/**
			 * Get only NEW reference numbers
			 * (those NOT already present in load_summary)
			 */
			/*$references = DB::table('load_optimizer as lo')
				->leftJoin('load_summary as ls', 'lo.reference_no', '=', 'ls.reference_no')
				->whereNull('ls.reference_no') // exclude already summarized
				->whereNotNull('lo.reference_no')
				->select('lo.reference_no')
				->groupBy('lo.reference_no')
				->get();

			foreach ($references as $ref) {

				/**
				 * Fetch items for this reference
				 * Ordered by priority (LOW → HIGH)
				 * Higher number = lower priority (to be dropped first)
				 */
				/*$items = DB::table('load_optimizer')
					->where('reference_no', $ref->reference_no)
					->orderBy('priority', 'desc') //IMPORTANT
					->get();

				if ($items->isEmpty()) {
					continue;
				}

				$totalWeight = $items->sum('z_total_weight');
				$totalVolume = $items->sum('z_total_volume');

				/**
				 * STEP 3:
				 * Select suitable truck
				 */
			/*	$truck = DB::table('truck_master')
					->orderBy('weight_capacity')
					->get()
					->first(function ($t) use ($totalWeight, $totalVolume) {
						return $totalWeight <= $t->weight_capacity
							&& $totalVolume <= ($t->max_volume_capacity * 1.05); // volume allowed till 105%
					});

				if (!$truck) {
					continue; // no truck fits
				}

				/**
				  BULK BREAK LOGIC
				 * Remove lowest priority items until qualified
				 */
				/*foreach ($items as $item) {

					$zw = ($totalWeight / $truck->weight_capacity) * 100;
					$zv = ($totalVolume / $truck->max_volume_capacity) * 100;

					// Weight max 100%, Volume max 105%
					if ($zw <= 100 && $zv <= 105) {
						break;
					}

					//Remove lowest priority item
					$totalWeight -= $item->z_total_weight;
					$totalVolume -= $item->z_total_volume;
				}

				/**
				  * Final Utilization
				 */
			/*	$zw = round(($totalWeight / $truck->weight_capacity) * 100, 2);
				$zv = round(($totalVolume / $truck->max_volume_capacity) * 100, 2);
				$gross = max($zw, $zv);

				/**
				 * Insert into load_summary
				 */
				/*LoadSummary::create([
					'reference_no'           => $ref->reference_no,
					'origin_name_code'       => $items->first()->origin_name_code,
					'destination_name_code'  => $items->first()->destination_name_code,
					't_mode'                 => $items->first()->t_mode,
					'truck_id'               => $truck->id,
					'total_weight'           => $totalWeight,
					'total_volume'           => $totalVolume,
					'zw_util'                => $zw,
					'zv_util'                => $zv,
					'gross_util'             => $gross,
					'is_qualified'           => $gross >= 90 ? 1 : 0,
					'created_by'             => auth()->id(),
					'created_at'             => now(),
				]);
			}
		}*/

	private function generateSummary()
	{
		$references = DB::table('load_optimizer as lo')
			->leftJoin('load_summary as ls', 'lo.reference_no', '=', 'ls.reference_no')
			->whereNull('ls.reference_no')
			->whereNotNull('lo.reference_no')
			->select('lo.reference_no')
			->groupBy('lo.reference_no')
			->get();

		foreach ($references as $ref) {

			$items = DB::table('load_optimizer')
				->where('reference_no', $ref->reference_no)
				->get();

			if ($items->isEmpty()) continue;

			echo "totalwt".$totalWeight = $items->sum('z_total_weight');
			echo "totalvol".$totalVolume = $items->sum('z_total_volume');

			/**
			 * STEP 1: Select BEST truck
			 * largest capacity <= total weight
			 */
			$truck = DB::table('truck_master')
				->where('weight_capacity', '<=', $totalWeight)
				->orderBy('weight_capacity', 'desc')
				->first();

			if (!$truck) continue;

			/**
			 * STEP 2: Check fit
			 */
		echo "zw".	$zw = ($totalWeight / $truck->weight_capacity) * 100;
		echo "zv".	$zv = ($totalVolume / $truck->max_volume_capacity) * 100;
			echo "<br><br>";
			/**
			 * STEP 3: BULK BREAK (remove smallest SKU first)
			 */
			if ($zw > 100 || $zv > 105) {

				$sortedItems = $items->sortBy(function ($i) {
					return $i->z_total_weight; // smallest removed first
				});

				foreach ($sortedItems as $item) {

					$totalWeight -= $item->z_total_weight;
					$totalVolume -= $item->z_total_volume;

					$zw = ($totalWeight / $truck->weight_capacity) * 100;
					$zv = ($totalVolume / $truck->max_volume_capacity) * 100;

					if ($zw <= 100 && $zv <= 105) {
						break;
					}
				}
			}

			/**
			 * STEP 4: Final utilization
			 */
			$zw = round(($totalWeight / $truck->weight_capacity) * 100, 2);
			$zv = round(($totalVolume / $truck->max_volume_capacity) * 100, 2);
			$gross = max($zw, $zv);

			LoadSummary::create([
				'reference_no'          => $ref->reference_no,
				'origin_name_code'      => $items->first()->origin_name_code,
				'destination_name_code' => $items->first()->destination_name_code,
				't_mode'                => $items->first()->t_mode,
				'truck_id'              => $truck->id,
				'total_weight'          => $totalWeight,
				'total_volume'          => $totalVolume,
				'zw_util'               => $zw,
				'zv_util'               => $zv,
				'gross_util'            => $gross,
				'is_qualified'          => $gross >= 90 ? 1 : 0,
				'created_by'            => auth()->id(),
				'created_at'            => now(),
			]);
		}
	}
}
