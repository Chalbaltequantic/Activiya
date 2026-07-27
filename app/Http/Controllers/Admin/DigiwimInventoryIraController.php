<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DigiwimInventoryIra;
use App\Models\DigiwimInventoryIraDetail;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Throwable;

class DigiwimInventoryIraController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    private function inventoryKeyExpression(string $alias = 'inventory'): string
    {
        return "
            SHA2(
                CONCAT_WS(
                    '|',
                    COALESCE({$alias}.material_code, ''),
                    COALESCE({$alias}.storage_plant_code, ''),
                    COALESCE({$alias}.storage_plant_location, ''),
                    COALESCE({$alias}.batch_no, ''),
                    COALESCE(DATE_FORMAT({$alias}.mfg_date, '%Y-%m-%d'), ''),
                    COALESCE(DATE_FORMAT({$alias}.expiry_date, '%Y-%m-%d'), '')
                ),
                256
            )
        ";
    }

    private function inventorySummaryQuery(): Builder
    {
        return DB::table('digiwim_inventory_view as inventory')
            ->select([
                DB::raw($this->inventoryKeyExpression('inventory') . ' as inventory_key'),
                'inventory.material_code',
                'inventory.material_description',
                'inventory.division',
                'inventory.brand',
                'inventory.sub_brand',
                'inventory.uom',
                'inventory.piece_per_box',
                'inventory.mrp',
                'inventory.weight',
                'inventory.volume',
                'inventory.storage_plant_code',
                'inventory.storage_plant_name',
                'inventory.storage_plant_location',
                'inventory.batch_no',
                'inventory.mfg_date',
                'inventory.expiry_date',
                DB::raw('SUM(COALESCE(inventory.total_inward_qty, 0)) as total_inward_qty'),
                DB::raw('SUM(COALESCE(inventory.total_outward_qty, 0)) as total_outward_qty'),
                DB::raw('SUM(COALESCE(inventory.available_qty, 0)) as available_qty'),
            ])
            ->groupBy(
                'inventory.material_code',
                'inventory.material_description',
                'inventory.division',
                'inventory.brand',
                'inventory.sub_brand',
                'inventory.uom',
                'inventory.piece_per_box',
                'inventory.mrp',
                'inventory.weight',
                'inventory.volume',
                'inventory.storage_plant_code',
                'inventory.storage_plant_name',
                'inventory.storage_plant_location',
                'inventory.batch_no',
                'inventory.mfg_date',
                'inventory.expiry_date'
            );
    }

    private function findInventoryByKey(string $inventoryKey): ?object
    {
        return DB::query()
            ->fromSub($this->inventorySummaryQuery(), 'inventory_summary')
            ->where('inventory_key', $inventoryKey)
            ->first();
    }

    public function index(Request $request)
    {
        $title = 'Inventory IRA';
        $pagetitle = 'Inventory IRA Listing';

        $pendingTotals = DB::table('digiwim_inventory_ira as ira')
            ->leftJoin('digiwim_inventory_ira_details as detail', function ($join) {
                $join->on('detail.digiwim_inventory_ira_id', '=', 'ira.id')
                    ->whereNull('detail.deleted_at');
            })
            ->whereNull('ira.deleted_at')
            ->where('ira.status', 'pending')
            ->select([
                'ira.inventory_key',
                'ira.id as ira_id',
                DB::raw('COUNT(detail.id) as activity_count'),
                DB::raw('SUM(COALESCE(detail.qty_unit, 0)) as total_qty_unit'),
                DB::raw('SUM(COALESCE(detail.qty_case, 0)) as total_qty_case'),
            ])
            ->groupBy('ira.inventory_key', 'ira.id');

        $query = DB::query()
            ->fromSub($this->inventorySummaryQuery(), 'inventory_summary')
            ->leftJoinSub($pendingTotals, 'pending_totals', function ($join) {
                $join->on('pending_totals.inventory_key', '=', 'inventory_summary.inventory_key');
            })
            ->where('inventory_summary.available_qty', '<>', 0)
            ->whereNotExists(function ($subQuery) {
                $subQuery->selectRaw('1')
                    ->from('digiwim_inventory_ira as completed_ira')
                    ->whereColumn('completed_ira.inventory_key', 'inventory_summary.inventory_key')
                    ->where('completed_ira.status', 'completed')
                    ->whereNull('completed_ira.deleted_at');
            })
            ->select([
                'inventory_summary.*',
                DB::raw('COALESCE(pending_totals.ira_id, 0) as ira_id'),
                DB::raw('COALESCE(pending_totals.activity_count, 0) as activity_count'),
                DB::raw('COALESCE(pending_totals.total_qty_unit, 0) as total_qty_unit'),
                DB::raw('COALESCE(pending_totals.total_qty_case, 0) as total_qty_case'),
            ]);

        if ($request->filled('plant_code')) {
            $query->where('inventory_summary.storage_plant_code', $request->plant_code);
        }

        if ($request->filled('plant_location')) {
            $query->where('inventory_summary.storage_plant_location', $request->plant_location);
        }

        if ($request->filled('search')) {
            $search = trim((string) $request->search);
            $query->where(function ($subQuery) use ($search) {
                $subQuery->where('inventory_summary.material_code', 'like', "%{$search}%")
                    ->orWhere('inventory_summary.material_description', 'like', "%{$search}%")
                    ->orWhere('inventory_summary.brand', 'like', "%{$search}%")
                    ->orWhere('inventory_summary.sub_brand', 'like', "%{$search}%")
                    ->orWhere('inventory_summary.batch_no', 'like', "%{$search}%");
            });
        }

        $datalist = $query->orderBy('inventory_summary.material_code')->paginate(25)->withQueryString();

        $plants = DB::query()->fromSub($this->inventorySummaryQuery(), 'x')
            ->whereNotNull('storage_plant_code')->where('storage_plant_code', '<>', '')
            ->distinct()->orderBy('storage_plant_code')->pluck('storage_plant_code');

        $locations = DB::query()->fromSub($this->inventorySummaryQuery(), 'x')
            ->whereNotNull('storage_plant_location')->where('storage_plant_location', '<>', '')
            ->distinct()->orderBy('storage_plant_location')->pluck('storage_plant_location');

        return view('admin.digiwim_inventory_ira.index', compact(
            'title', 'pagetitle', 'datalist', 'plants', 'locations'
        ));
    }

    public function addActivity(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'inventory_key' => ['required', 'string', 'size:64', 'regex:/^[a-f0-9]{64}$/i'],
            'qty_unit' => ['nullable', 'numeric', 'gt:0'],
            'qty_case' => ['nullable', 'numeric', 'gt:0'],
            'bin_no' => ['nullable', 'string', 'max:150'],
            'remarks' => ['nullable', 'string', 'max:5000'],
        ]);

        $qtyUnit = $request->filled('qty_unit') ? (float) $validated['qty_unit'] : null;
        $qtyCase = $request->filled('qty_case') ? (float) $validated['qty_case'] : null;
        $binNo = trim((string) ($validated['bin_no'] ?? ''));
        $remarks = trim((string) ($validated['remarks'] ?? ''));

        if ($qtyUnit === null && $qtyCase === null && $binNo === '' && $remarks === '') {
            throw ValidationException::withMessages([
                'activity' => 'Enter Qty Unit, Qty Case, BIN No. or Remarks.',
            ]);
        }

        $inventory = $this->findInventoryByKey($validated['inventory_key']);

        if (!$inventory) {
            return response()->json([
                'status' => 'error',
                'message' => 'Inventory record was not found or has changed.',
            ], 404);
        }

        try {
            [$ira, $summary] = DB::transaction(function () use (
                $validated, $inventory, $qtyUnit, $qtyCase, $binNo, $remarks
            ) {
                $ira = DigiwimInventoryIra::query()
                    ->where('inventory_key', $validated['inventory_key'])
                    ->lockForUpdate()
                    ->first();

                if ($ira && $ira->status === 'completed') {
                    throw ValidationException::withMessages([
                        'inventory_key' => 'This IRA has already been completed.',
                    ]);
                }

                if (!$ira) {
                    $ira = DigiwimInventoryIra::create([
                        'digiwim_preloading_id' => null,
                        'inventory_key' => $inventory->inventory_key,
                        'material_code' => $inventory->material_code,
                        'material_description' => $inventory->material_description,
                        'division' => $inventory->division,
                        'brand' => $inventory->brand,
                        'sub_brand' => $inventory->sub_brand,
                        'uom' => $inventory->uom,
                        'piece_per_box' => $inventory->piece_per_box,
                        'mrp' => $inventory->mrp,
                        'weight' => $inventory->weight,
                        'volume' => $inventory->volume,
                        'storage_plant_code' => $inventory->storage_plant_code,
                        'storage_plant_name' => $inventory->storage_plant_name,
                        'storage_plant_location' => $inventory->storage_plant_location,
                        'batch_no' => $inventory->batch_no,
                        'mfg_date' => $inventory->mfg_date,
                        'expiry_date' => $inventory->expiry_date,
                        'inventory_qty' => $inventory->available_qty,
                        'status' => 'pending',
                        'started_by' => Auth::guard('admin')->id(),
                        'started_at' => now(),
                    ]);
                }

                DigiwimInventoryIraDetail::create([
                    'digiwim_inventory_ira_id' => $ira->id,
                    'qty_unit' => $qtyUnit,
                    'qty_case' => $qtyCase,
                    'bin_no' => $binNo !== '' ? $binNo : null,
                    'remarks' => $remarks !== '' ? $remarks : null,
                    'activity_by' => Auth::guard('admin')->id(),
                    'activity_at' => now(),
                ]);

                $summary = DigiwimInventoryIraDetail::query()
                    ->where('digiwim_inventory_ira_id', $ira->id)
                    ->selectRaw('COUNT(id) as activity_count')
                    ->selectRaw('SUM(COALESCE(qty_unit, 0)) as total_qty_unit')
                    ->selectRaw('SUM(COALESCE(qty_case, 0)) as total_qty_case')
                    ->first();

                return [$ira, $summary];
            });

            return response()->json([
                'status' => 'success',
                'message' => 'IRA activity added successfully.',
                'ira_id' => $ira->id,
                'activity_count' => (int) ($summary->activity_count ?? 0),
                'total_qty_unit' => number_format((float) ($summary->total_qty_unit ?? 0), 3, '.', ''),
                'total_qty_case' => number_format((float) ($summary->total_qty_case ?? 0), 3, '.', ''),
            ]);
        } catch (ValidationException $exception) {
            throw $exception;
        } catch (Throwable $exception) {
            Log::error('IRA add activity failed', ['exception' => $exception]);

            return response()->json([
                'status' => 'error',
                'message' => 'Unable to add IRA activity. Check laravel.log.',
            ], 500);
        }
    }

    public function endActivity(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'inventory_key' => ['required', 'string', 'size:64', 'regex:/^[a-f0-9]{64}$/i'],
        ]);

        try {
            $ira = DB::transaction(function () use ($validated) {
                $ira = DigiwimInventoryIra::query()
                    ->where('inventory_key', $validated['inventory_key'])
                    ->lockForUpdate()
                    ->first();

                if (!$ira) {
                    throw ValidationException::withMessages([
                        'inventory_key' => 'Add at least one IRA activity before ending.',
                    ]);
                }

                if ($ira->status === 'completed') {
                    throw ValidationException::withMessages([
                        'inventory_key' => 'This IRA is already completed.',
                    ]);
                }

                if (!$ira->activities()->exists()) {
                    throw ValidationException::withMessages([
                        'inventory_key' => 'Add at least one IRA activity before ending.',
                    ]);
                }

                $ira->update([
                    'status' => 'completed',
                    'ended_by' => Auth::guard('admin')->id(),
                    'ended_at' => now(),
                ]);

                return $ira;
            });

            return response()->json([
                'status' => 'success',
                'message' => 'IRA completed successfully.',
                'ira_id' => $ira->id,
            ]);
        } catch (ValidationException $exception) {
            throw $exception;
        } catch (Throwable $exception) {
            Log::error('IRA end activity failed', ['exception' => $exception]);

            return response()->json([
                'status' => 'error',
                'message' => 'Unable to complete IRA. Check laravel.log.',
            ], 500);
        }
    }

    public function history(Request $request)
    {
        $title = 'IRA History';
        $pagetitle = 'Completed IRA History';

        $query = DigiwimInventoryIra::query()
            ->withCount('activities')
            ->withSum('activities as total_qty_unit', 'qty_unit')
            ->withSum('activities as total_qty_case', 'qty_case')
            ->where('status', 'completed');

        if ($request->filled('plant_code')) {
            $query->where('storage_plant_code', $request->plant_code);
        }

        if ($request->filled('plant_location')) {
            $query->where('storage_plant_location', $request->plant_location);
        }

        if ($request->filled('search')) {
            $search = trim((string) $request->search);
            $query->where(function ($subQuery) use ($search) {
                $subQuery->where('material_code', 'like', "%{$search}%")
                    ->orWhere('material_description', 'like', "%{$search}%")
                    ->orWhere('batch_no', 'like', "%{$search}%")
                    ->orWhere('storage_plant_code', 'like', "%{$search}%")
                    ->orWhere('storage_plant_location', 'like', "%{$search}%");
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('ended_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('ended_at', '<=', $request->date_to);
        }

        $historyList = $query->latest('ended_at')->paginate(25)->withQueryString();

        $plants = DigiwimInventoryIra::query()->where('status', 'completed')
            ->whereNotNull('storage_plant_code')->where('storage_plant_code', '<>', '')
            ->distinct()->orderBy('storage_plant_code')->pluck('storage_plant_code');

        $locations = DigiwimInventoryIra::query()->where('status', 'completed')
            ->whereNotNull('storage_plant_location')->where('storage_plant_location', '<>', '')
            ->distinct()->orderBy('storage_plant_location')->pluck('storage_plant_location');

        return view('admin.digiwim_inventory_ira.history', compact(
            'title', 'pagetitle', 'historyList', 'plants', 'locations'
        ));
    }

    public function viewActivities(int $id): JsonResponse
    {
        $ira = DigiwimInventoryIra::query()
            ->with(['activities.activityByAdmin'])
            ->findOrFail($id);

        return response()->json([
            'status' => 'success',
            'ira' => [
                'material_code' => $ira->material_code,
                'material_description' => $ira->material_description,
                'plant_code' => $ira->storage_plant_code,
                'plant_location' => $ira->storage_plant_location,
                'batch_no' => $ira->batch_no,
                'inventory_qty' => $ira->inventory_qty,
                'started_at' => optional($ira->started_at)->format('d-m-Y h:i A'),
                'ended_at' => optional($ira->ended_at)->format('d-m-Y h:i A'),
            ],
            'activities' => $ira->activities->map(function ($activity) {
                return [
                    'qty_unit' => $activity->qty_unit,
                    'qty_case' => $activity->qty_case,
                    'bin_no' => $activity->bin_no,
                    'remarks' => $activity->remarks,
                    'activity_by' => $activity->activityByAdmin->name
                        ?? $activity->activityByAdmin->full_name
                        ?? $activity->activity_by
                        ?? '-',
                    'activity_at' => optional($activity->activity_at)->format('d-m-Y h:i A'),
                ];
            })->values(),
        ]);
    }

    public function inventoryBook(Request $request)
    {
        $title = 'Inventory Book Vs IRA';
        $pagetitle = 'Inventory Book Vs IRA';

        $iraTotals = DB::table('digiwim_inventory_ira as ira')
            ->leftJoin('digiwim_inventory_ira_details as detail', function ($join) {
                $join->on('detail.digiwim_inventory_ira_id', '=', 'ira.id')
                    ->whereNull('detail.deleted_at');
            })
            ->whereNull('ira.deleted_at')
            ->select([
                'ira.id as ira_id','ira.inventory_key','ira.material_code','ira.material_description','ira.division',
                'ira.brand','ira.sub_brand','ira.uom','ira.piece_per_box','ira.mrp','ira.storage_plant_code',
                'ira.storage_plant_name','ira.storage_plant_location','ira.batch_no','ira.mfg_date','ira.expiry_date',
                'ira.inventory_qty as snapshot_inventory_qty','ira.status','ira.started_at','ira.ended_at',
                DB::raw('SUM(COALESCE(detail.qty_unit, 0)) as total_ira_qty'),
                DB::raw('SUM(COALESCE(detail.qty_case, 0)) as total_ira_case_qty'),
                DB::raw('COUNT(detail.id) as activity_count'),
            ])
            ->groupBy(
                'ira.id','ira.inventory_key','ira.material_code','ira.material_description','ira.division','ira.brand',
                'ira.sub_brand','ira.uom','ira.piece_per_box','ira.mrp','ira.storage_plant_code','ira.storage_plant_name',
                'ira.storage_plant_location','ira.batch_no','ira.mfg_date','ira.expiry_date','ira.inventory_qty',
                'ira.status','ira.started_at','ira.ended_at'
            );

        $inventoryKeys = DB::query()->fromSub($this->inventorySummaryQuery(), 'inventory_keys')->select('inventory_key');
        $iraKeys = DB::table('digiwim_inventory_ira')->whereNull('deleted_at')->whereNotNull('inventory_key')->select('inventory_key');
        $allKeys = $inventoryKeys->union($iraKeys);

        $query = DB::query()
            ->fromSub($allKeys, 'all_keys')
            ->leftJoinSub($this->inventorySummaryQuery(), 'inventory', function ($join) {
                $join->on('inventory.inventory_key', '=', 'all_keys.inventory_key');
            })
            ->leftJoinSub($iraTotals, 'ira', function ($join) {
                $join->on('ira.inventory_key', '=', 'all_keys.inventory_key');
            })
            ->select([
                'all_keys.inventory_key',
                DB::raw('COALESCE(inventory.material_code, ira.material_code) as material_code'),
                DB::raw('COALESCE(inventory.material_description, ira.material_description) as material_description'),
                DB::raw('COALESCE(inventory.division, ira.division) as division'),
                DB::raw('COALESCE(inventory.brand, ira.brand) as brand'),
                DB::raw('COALESCE(inventory.sub_brand, ira.sub_brand) as sub_brand'),
                DB::raw('COALESCE(inventory.uom, ira.uom) as uom'),
                DB::raw('COALESCE(inventory.piece_per_box, ira.piece_per_box) as piece_per_box'),
                DB::raw('COALESCE(inventory.mrp, ira.mrp) as mrp'),
                DB::raw('COALESCE(inventory.storage_plant_code, ira.storage_plant_code) as storage_plant_code'),
                DB::raw('COALESCE(inventory.storage_plant_name, ira.storage_plant_name) as storage_plant_name'),
                DB::raw('COALESCE(inventory.storage_plant_location, ira.storage_plant_location) as storage_plant_location'),
                DB::raw('COALESCE(inventory.batch_no, ira.batch_no) as batch_no'),
                DB::raw('COALESCE(inventory.available_qty, ira.snapshot_inventory_qty, 0) as inventory_qty'),
                DB::raw('COALESCE(ira.total_ira_qty, 0) as total_ira_qty'),
                DB::raw('COALESCE(ira.total_ira_case_qty, 0) as total_ira_case_qty'),
                DB::raw('(COALESCE(inventory.available_qty, ira.snapshot_inventory_qty, 0) - COALESCE(ira.total_ira_qty, 0)) as difference_qty'),
                DB::raw('COALESCE(ira.activity_count, 0) as activity_count'),
                'ira.ira_id','ira.status','ira.started_at','ira.ended_at',
            ]);

        if ($request->filled('plant_code')) {
            $plant = $request->plant_code;
            $query->where(function ($subQuery) use ($plant) {
                $subQuery->where('inventory.storage_plant_code', $plant)->orWhere('ira.storage_plant_code', $plant);
            });
        }

        if ($request->filled('plant_location')) {
            $location = $request->plant_location;
            $query->where(function ($subQuery) use ($location) {
                $subQuery->where('inventory.storage_plant_location', $location)->orWhere('ira.storage_plant_location', $location);
            });
        }

        if ($request->filled('status')) {
            if ($request->status === 'not_started') {
                $query->whereNull('ira.ira_id');
            } else {
                $query->where('ira.status', $request->status);
            }
        }

        if ($request->filled('search')) {
            $search = trim((string) $request->search);
            $query->where(function ($subQuery) use ($search) {
                $subQuery->where('inventory.material_code', 'like', "%{$search}%")
                    ->orWhere('ira.material_code', 'like', "%{$search}%")
                    ->orWhere('inventory.material_description', 'like', "%{$search}%")
                    ->orWhere('ira.material_description', 'like', "%{$search}%")
                    ->orWhere('inventory.batch_no', 'like', "%{$search}%")
                    ->orWhere('ira.batch_no', 'like', "%{$search}%");
            });
        }

        $inventoryList = $query->orderByRaw('COALESCE(inventory.material_code, ira.material_code) ASC')
            ->paginate(25)->withQueryString();

        $plants = DB::query()->fromSub($this->inventorySummaryQuery(), 'x')
            ->whereNotNull('storage_plant_code')->where('storage_plant_code', '<>', '')
            ->distinct()->orderBy('storage_plant_code')->pluck('storage_plant_code');

        $locations = DB::query()->fromSub($this->inventorySummaryQuery(), 'x')
            ->whereNotNull('storage_plant_location')->where('storage_plant_location', '<>', '')
            ->distinct()->orderBy('storage_plant_location')->pluck('storage_plant_location');

        return view('admin.digiwim_inventory_ira.inventory_book', compact(
            'title', 'pagetitle', 'inventoryList', 'plants', 'locations'
        ));
    }

    public function report(string $inventoryKey)
    {
        $inventory = $this->findInventoryByKey($inventoryKey);
        $ira = DigiwimInventoryIra::query()
            ->with(['activities.activityByAdmin', 'startedByAdmin', 'endedByAdmin'])
            ->where('inventory_key', $inventoryKey)
            ->first();

        if (!$inventory && !$ira) {
            abort(404, 'Inventory record not found.');
        }

        $identity = $inventory ?: $ira;
        $bookQty = (float) ($inventory->available_qty ?? $ira->inventory_qty ?? 0);
        $iraQty = (float) ($ira?->activities->sum('qty_unit') ?? 0);
        $differenceQty = $bookQty - $iraQty;
        $title = 'Inventory Detail Report';
        $pagetitle = 'Inventory Detail Report';

        return view('admin.digiwim_inventory_ira.report', compact(
            'title','pagetitle','inventoryKey','identity','inventory','ira','bookQty','iraQty','differenceQty'
        ));
    }
}
