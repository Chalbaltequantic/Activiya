<?php

namespace App\Services;

use DB;
use App\Models\LoadSummary;
use App\Models\Loadoptimizer;
use App\Models\LoadSummaryItem;
use App\Models\LoadOptimizerItemHistory;
use Auth;
class LoadOptimizerService
{
    public function generate(string $referenceNo, bool $isEdit = false)
    {
        DB::transaction(function () use ($referenceNo, $isEdit) {

            /** 1️ Load SKUs */
            $skus = Loadoptimizer::where('reference_no', $referenceNo)
					->whereNull('deleted_at')
					->orderByDesc('priority')
					->orderByDesc('z_total_weight')
					->lockForUpdate()
					->get();

            if ($skus->isEmpty()) {
                throw new \Exception("No SKU found for {$referenceNo}");
            }

            $origin      = $skus->first()->origin_name_code;
            $destination = $skus->first()->destination_name_code;
            $tMode       = $skus->first()->t_mode;

            /** 2️ EDIT FLOW → ARCHIVE */
            if ($isEdit) {
                $this->archiveSummary($referenceNo);
            }

            /** 3️ DELETE ONLY CURRENT ACTIVE */
            LoadSummary::where('reference_no', 'LIKE', $referenceNo.'%')
                ->delete();

            $remaining = $skus->values();
            $child = 0;

            /** 4️ BULK BREAK LOOP */
            while ($remaining->isNotEmpty()) {

                $totalWeight = $remaining->sum('z_total_weight');

                /** 5️ SELECT BEST TRUCK */
                $truck = $this->bestTruck($origin, $destination, $totalWeight);

                if (!$truck) {
                    $this->insertRateError($referenceNo, $child, $origin, $destination, $tMode, $remaining);
                    break;
                }

                /** 6️ FIT SKUs */
                $fit = collect();
                $w = 0;
                $v = 0;

                foreach ($remaining as $sku) {
                    if (
                        ($w + $sku->z_total_weight) <= $truck->weight_capacity &&
                        ($v + $sku->z_total_volume) <= ($truck->max_volume_capacity * 1.05)
                    ) {
                        $fit->push($sku);
                        $w += $sku->z_total_weight;
                        $v += $sku->z_total_volume;
                    }
                }

                if ($fit->isEmpty()) {
                    break;
                }

                /** 7️ CREATE SUMMARY */
                $ref = $child === 0 ? $referenceNo : "{$referenceNo}-C{$child}";
				$is_qualified = ($w / $truck->weight_capacity) >= 0.9 ? 1 : 0;
				$approvalstatus = ($is_qualified)==1?'APPROVED':'PENDING';
				
                $summary = LoadSummary::create([
                    'reference_no' => $ref,
					'parent_reference_no' => $child === 0 ? "" : $referenceNo,
                    'origin_name_code' => $origin,
                    'destination_name_code' => $destination,
                    't_mode' => $tMode,
                    'truck_id' => $truck->id,
                    'truck_code' => $truck->code,
                    'total_weight' => $w,
                    'total_volume' => $v,
                    'zw_util' => round(($w / $truck->weight_capacity) * 100, 2),
                    'zv_util' => round(($v / $truck->max_volume_capacity) * 100, 2),
                    'gross_util' => max(
                        round(($w / $truck->weight_capacity) * 100, 2),
                        round(($v / $truck->max_volume_capacity) * 100, 2)
                    ),
                    'is_qualified' => $is_qualified,
                    'approval_status' => $approvalstatus,
                    'status' => 'ACTIVE'
                ]);

                /** 8️ MAP SKUs */
                foreach ($fit as $sku) {
                    LoadSummaryItem::create([
                        'load_summary_id' => $summary->id,
                        'reference_no' => $ref,
                        'load_optimizer_id' => $sku->id,
                        'sku_code' => $sku->sku_code,
                        'qty' => $sku->qty,
                        'sku_description' => $sku->sku_description,
                        'weight' => $sku->z_total_weight,
                        'volume' => $sku->z_total_volume,
                    ]);
                }

                /** 9️ REMOVE USED */
                $remaining = $remaining->reject(
                    fn($i) => $fit->pluck('id')->contains($i->id)
                )->values();

                $child++;
            }
        });
    }

    /** BEST TRUCK */
    private function bestTruck($origin, $destination, $weight)
    {
        return DB::table('truck_master as tm')
            ->join('rate_master as rm', function ($j) use ($origin, $destination) {
                $j->on('rm.t_code', '=', 'tm.code')
                  ->where('rm.consignor_code', $origin)
                  ->where('rm.consignee_code', $destination)
                  ->where('rm.rank', 1);
            })
            ->where('tm.weight_capacity', '<=', $weight)
            ->orderByDesc('tm.weight_capacity')
            ->orderBy('rm.a_amount')
            ->select('tm.*')
            ->first();
    }

    /**  RATE ERROR */
    private function insertRateError($referenceNo, $child, $origin, $destination, $tMode, $remaining)
    {
        LoadSummary::create([
            'reference_no' => $child === 0 ? $referenceNo : "{$referenceNo}-C{$child}",
            'parent_reference_no' => $child === 0 ? "" : $referenceNo,
            'origin_name_code' => $origin,
            'destination_name_code' => $destination,
            't_mode' => $tMode,
            'total_weight' => $remaining->sum('z_total_weight'),
            'total_volume' => $remaining->sum('z_total_volume'),
            'status' => 'RATE_NOT_FOUND',
        ]);
    }

    /** HISTORY */
    private function archiveSummary(string $referenceNo)
    {
        $rows = LoadSummary::where('reference_no', 'LIKE', $referenceNo.'%')->get();
        foreach ($rows as $r) {
            LoadSummaryHistory::create($r->toArray());
        }
    }
	
	public function updateAndRebuildSummary(int $summaryId, array $items)
    {
        DB::transaction(function () use ($summaryId, $items) {

            $summary = LoadSummary::findOrFail($summaryId);
            $parentRef = explode('-', $summary->reference_no)[0];
            $userId = auth()->id();

            /** -------------------------------
             * 1️ UPDATE / INSERT load_optimizer
             * -------------------------------*/
            foreach ($items as $row) {

                if (!empty($row['load_optimizer_id'])) {

                    $optimizer = Loadoptimizer::findOrFail($row['load_optimizer_id']);

                    // HISTORY
                    LoadOptimizerHistory::create([
                        'load_optimizer_id' => $optimizer->id,
                        'old_data'          => json_encode($optimizer->toArray()),
                        'edited_by'         => $userId,
                        'edited_at'         => now(),
                    ]);

                    $optimizer->update([
                        'sku_code'       => $row['sku_code'],
                        'priority'       => $row['priority'],
                        'qty'            => $row['qty'],
                        'z_total_weight' => $row['z_total_weight'],
                        'z_total_volume' => $row['z_total_volume'],
                    ]);

                } else {
                    // NEW SKU
                    Loadoptimizer::create([
                        'reference_no'   => $parentRef,
                        'sku_code'       => $row['sku_code'],
                        'priority'       => $row['priority'],
                        'qty'            => $row['qty'],
                        'z_total_weight' => $row['z_total_weight'],
                        'z_total_volume' => $row['z_total_volume'],
                    ]);
                }
            }

            /** -------------------------------
             * 2️ REBUILD SUMMARY ITEMS
             * -------------------------------*/
            LoadSummaryItem::where('load_summary_id', $summaryId)->delete();

            $optimizers = Loadoptimizer::where('reference_no', $parentRef)->get();

            foreach ($optimizers as $opt) {
                LoadSummaryItem::create([
                    'load_summary_id'   => $summaryId,
                    'reference_no'      => $summary->reference_no,
                    'load_optimizer_id'=> $opt->id,
                ]);
            }

            /** -------------------------------
             * 3️ RECALCULATE SUMMARY (NO DELETE)
             * -------------------------------*/
            $this->recalculateSummary($summary);
        });
    }

    private function recalculateSummary(LoadSummary $summary)
    {
        $items = LoadSummaryItem::with('optimizer')
            ->where('load_summary_id', $summary->id)
            ->get();

        $totalWeight = $items->sum(fn($i) => $i->optimizer->z_total_weight);
        $totalVolume = $items->sum(fn($i) => $i->optimizer->z_total_volume);

        $zw = round(($totalWeight / $summary->truck->weight_capacity) * 100, 2);
        $zv = round(($totalVolume / $summary->truck->max_volume_capacity) * 100, 2);

        $summary->update([
            'total_weight' => $totalWeight,
            'total_volume' => $totalVolume,
            'zw_util'      => $zw,
            'zv_util'      => $zv,
            'gross_util'   => max($zw, $zv),
            'is_qualified' => max($zw, $zv) >= 90,
        ]);
    }
	
	///////////////////
	
	public function updateLoadOptimizerItems(string $referenceNo, array $items)
    {
        $summary = LoadSummary::where('reference_no', $referenceNo)->firstOrFail();

        // ALWAYS USE PARENT
        $parentRef = $summary->parent_reference_no ?? $summary->reference_no;

        // -------------------------
        // 1. UPDATE / INSERT MASTER (load_optimizer)
        // -------------------------
        foreach ($items as $row) {

            if (!empty($row['load_optimizer_id'])) {
                // Update existing master
				
				$optimizer = LoadOptimizer::find($row['load_optimizer_id']);
					if (!$optimizer) {
						continue;
					}

				// 1️ STORE FULL SNAPSHOT
				LoadOptimizerItemHistory::create([
					'load_optimizer_id'       => $optimizer->id,
					'reference_no'            => $optimizer->reference_no,
					'origin_name_code'        => $optimizer->origin_name_code,
					'destination_name_code'   => $optimizer->destination_name_code,
					'origin_name'             => $optimizer->origin_name,
					'destination_city'        => $optimizer->destination_city,
					'sku_code'                => $optimizer->sku_code,
					'sku_description'         => $optimizer->sku_description,
					'priority'                => $optimizer->priority,
					'sku_class'               => $optimizer->sku_class,
					't_mode'                  => $optimizer->t_mode,
					'required_delivery_date'  => $optimizer->required_delivery_date,
					'qty'                     => $optimizer->qty,
					'z_total_weight'          => $optimizer->z_total_weight,
					'z_total_volume'          => $optimizer->z_total_volume,
					'edited_by'               => Auth::id(),
					'edited_at'               => now(),
					'created_at'              => now(),
					'updated_at'              => now(),
				]);				
                $optimizer->update([
						'sku_code'        => $row['sku_code'],
						'sku_description' => $row['sku_description'],
						'priority'        => $row['priority'],
						'qty'             => $row['qty'],
						'z_total_weight'  => $row['z_weight'],
						'z_total_volume'  => $row['z_volume'],
						'updated_at'      => now(),
						'updated_by'      => Auth::id(),
					]);
            } else {
				
				// New SKU added
                LoadOptimizer::create([
						'reference_no'             => $parentRef,
						'origin_name_code'         => $row['origin_name_code'],
						'destination_name_code'    => $row['destination_name_code'],
						'origin_name'              => $row['origin_name'],
						'destination_city'         => $row['destination_city'],
						'sku_code'                 => $row['sku_code'],
						'sku_description'          => $row['sku_description'],
						'priority'                 => $row['priority'],
						'sku_class'                => $row['sku_class'],
						't_mode'                   => $row['t_mode'],
						'qty'                      => $row['qty'],
						'z_total_weight'           => $row['z_weight'],
						'z_total_volume'           => $row['z_volume'],
						'created_at'			   =>now(),
						'updated_by'			   => Auth::id()
					]);
            }
        }

        // -------------------------
        // 2. DELETE OLD SUMMARY ITEMS
        // -------------------------
        LoadSummaryItem::where('load_summary_id', $summary->id)->delete();

        // -------------------------
        // 3. INSERT NEW SUMMARY ITEMS
        // -------------------------
        $totalWeight = 0;
        $totalVolume = 0;

        foreach ($items as $row) {
            if (empty($row['sku_code'])) continue;

            LoadSummaryItem::create([
                'load_summary_id'  => $summary->id,
                'reference_no'     => $referenceNo,
                'load_optimizer_id'=> $row['load_optimizer_id'] ?? null,
                'sku_code'         => $row['sku_code'],
                'sku_description'  => $row['sku_description'],
                'qty'              => $row['qty'],
                'weight'           => $row['z_weight'],
                'volume'           => $row['z_volume'],
				'created_at' 	   => now(),
				'updated_by' 	   => Auth::id()
            ]);

            $totalWeight += $row['z_weight'];
            $totalVolume += $row['z_volume'];
        }

        // -------------------------
        // 4. UPDATE SUMMARY TOTALS
        // -------------------------
        $truck = DB::table('truck_master')->find($summary->truck_id);

        $zw = round(($totalWeight / $truck->weight_capacity) * 100, 2);
        $zv = round(($totalVolume / $truck->max_volume_capacity) * 100, 2);
		$is_qualified = ($totalWeight / $truck->weight_capacity) >= 0.9 ? 1 : 0;
        $summary->update([
            'total_weight' => $totalWeight,
            'total_volume' => $totalVolume,
            'zw_util'      => $zw,
            'zv_util'      => $zv,
            'gross_util'   => max($zw, $zv),
			'is_qualified' => $is_qualified,
            'updated_by'   => Auth::id()
        ]);
    }
	
	
}