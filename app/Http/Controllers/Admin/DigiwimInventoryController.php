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

use Barryvdh\DomPDF\Facade\Pdf;

use App\Models\Material;
use App\Models\Admin;
use App\Models\Ratedata;
use App\Models\TruckMaster;
use App\Models\DigiWim;
use App\Models\DigiwimOperation;
use App\Models\DigiwimOperationItem;
use App\Models\Vendor;
use App\Models\Siteplant;

use Auth;


class DigiwimInventoryController extends Controller
{
   

    public function __construct()
    {
       
		$this->middleware('auth:admin'); 
    }

	


	public function index(Request $request)
{
    $location = $request->location;
    $date     = $request->date;

    $ledger = $this->ledgerBaseQuery($location, $date);

    $records = DB::query()
        ->fromSub($ledger, 'ledger')
        ->select([
            'material_code',
            'material_description',
            'division',
            'brand',
            'sub_brand',
            'uom',
            'piece_per_box',
            'mrp',
            'weight',
            'volume',
            'storage_plant_code',
            'storage_plant_name',
            'storage_plant_location',
            'batch_no',
            'mfg_date',
            'expiry_date',
            'bin_no',

            DB::raw('SUM(COALESCE(inward_qty,0)) as total_inward_qty'),
            DB::raw('SUM(COALESCE(outward_qty,0)) as total_outward_qty'),
            DB::raw('(SUM(COALESCE(inward_qty,0)) - SUM(COALESCE(outward_qty,0))) as available_qty'),
        ])
        ->groupBy(
            'material_code',
            'material_description',
            'division',
            'brand',
            'sub_brand',
            'uom',
            'piece_per_box',
            'mrp',
            'weight',
            'volume',
            'storage_plant_code',
            'storage_plant_name',
            'storage_plant_location',
            'batch_no',
            'mfg_date',
            'expiry_date',
            'bin_no'
        )
        ->havingRaw('(SUM(COALESCE(inward_qty,0)) - SUM(COALESCE(outward_qty,0))) != 0')
        ->orderBy('material_code', 'asc')
        ->get();

    return view('admin.digiwim_inventory.index', compact('records', 'location', 'date'));
}

private function ledgerBaseQuery($location = null, $date = null)
{
    /*
    |--------------------------------------------------------------------------
    | INWARD = digi_wim
    |--------------------------------------------------------------------------
    */
    $inward = DB::table('digi_wim as dw')
        ->leftJoin('materials as m', function ($join) {
            $join->on(
                DB::raw('m.material_code COLLATE utf8mb4_general_ci'),
                '=',
                DB::raw('dw.m_code COLLATE utf8mb4_general_ci')
            );
        })
        ->leftJoin('site_plants as sp', function ($join) {
            $join->on(
                DB::raw('sp.plant_site_code COLLATE utf8mb4_general_ci'),
                '=',
                DB::raw('dw.consignee_code COLLATE utf8mb4_general_ci')
            );
        })
        ->select([
            DB::raw("CAST('INWARD' AS CHAR CHARACTER SET utf8mb4) COLLATE utf8mb4_general_ci as movement_type"),
            'dw.created_at',

            DB::raw('dw.m_code COLLATE utf8mb4_general_ci as material_code'),
            DB::raw('dw.material_descriptions COLLATE utf8mb4_general_ci as material_description'),

            DB::raw('m.division COLLATE utf8mb4_general_ci as division'),
            DB::raw('m.brand COLLATE utf8mb4_general_ci as brand'),
            DB::raw('m.sub_brand COLLATE utf8mb4_general_ci as sub_brand'),
            DB::raw('m.uom COLLATE utf8mb4_general_ci as uom'),

            DB::raw('m.piece_per_box as piece_per_box'),
            DB::raw('NULL as mrp'),
            DB::raw('m.gross_weight_kg as weight'),
            DB::raw('m.volume_cft as volume'),

            DB::raw('dw.invoice_challan_no COLLATE utf8mb4_general_ci as invoice_challan_no'),
            DB::raw('dw.invoice_challan_date as invoice_date'),

            DB::raw('sp.plant_site_code COLLATE utf8mb4_general_ci as storage_plant_code'),
            DB::raw('sp.plant_site_name COLLATE utf8mb4_general_ci as storage_plant_name'),
            DB::raw('sp.city COLLATE utf8mb4_general_ci as storage_plant_location'),

            DB::raw('dw.batch_no COLLATE utf8mb4_general_ci as batch_no'),
            DB::raw('dw.mfg_date as mfg_date'),
            DB::raw('dw.expiry_date as expiry_date'),

            DB::raw('CAST(COALESCE(dw.qty_units,0) AS DECIMAL(12,2)) as inward_qty'),
            DB::raw('CAST(0 AS DECIMAL(12,2)) as outward_qty'),

            DB::raw('CAST(NULL AS CHAR CHARACTER SET utf8mb4) COLLATE utf8mb4_general_ci as bin_no'),
            DB::raw('CAST(NULL AS CHAR CHARACTER SET utf8mb4) COLLATE utf8mb4_general_ci as remarks'),
        ]);

    /*
    |--------------------------------------------------------------------------
    | OUTWARD = digiwim_preloading
    |--------------------------------------------------------------------------
    */
    $outward = DB::table('digiwim_preloading as pl')
        ->leftJoin('materials as m', function ($join) {
            $join->on(
                DB::raw('m.material_code COLLATE utf8mb4_general_ci'),
                '=',
                DB::raw('pl.material_code COLLATE utf8mb4_general_ci')
            );
        })
        ->leftJoin('site_plants as sp', function ($join) {
            $join->on(
                DB::raw('sp.plant_site_code COLLATE utf8mb4_general_ci'),
                '=',
                DB::raw('pl.consignee_code COLLATE utf8mb4_general_ci')
            );
        })
        ->select([
            DB::raw("CAST('OUTWARD' AS CHAR CHARACTER SET utf8mb4) COLLATE utf8mb4_general_ci as movement_type"),
            'pl.created_at',

            DB::raw('pl.material_code COLLATE utf8mb4_general_ci as material_code'),
            DB::raw('pl.material_description COLLATE utf8mb4_general_ci as material_description'),

            DB::raw('m.division COLLATE utf8mb4_general_ci as division'),
            DB::raw('m.brand COLLATE utf8mb4_general_ci as brand'),
            DB::raw('m.sub_brand COLLATE utf8mb4_general_ci as sub_brand'),
            DB::raw('m.uom COLLATE utf8mb4_general_ci as uom'),

            DB::raw('m.piece_per_box as piece_per_box'),
            DB::raw('NULL as mrp'),
            DB::raw('m.gross_weight_kg as weight'),
            DB::raw('m.volume_cft as volume'),

            DB::raw('pl.invoice_challan_no COLLATE utf8mb4_general_ci as invoice_challan_no'),
            DB::raw('CAST(NULL AS DATE) as invoice_date'),

            DB::raw('sp.plant_site_code COLLATE utf8mb4_general_ci as storage_plant_code'),
            DB::raw('sp.plant_site_name COLLATE utf8mb4_general_ci as storage_plant_name'),
            DB::raw('sp.city COLLATE utf8mb4_general_ci as storage_plant_location'),

            DB::raw('pl.batch_no COLLATE utf8mb4_general_ci as batch_no'),
            DB::raw('pl.mfg_date as mfg_date'),
            DB::raw('pl.expiry_date as expiry_date'),

            DB::raw('CAST(0 AS DECIMAL(12,2)) as inward_qty'),
            DB::raw('CAST(COALESCE(pl.qty,0) AS DECIMAL(12,2)) as outward_qty'),

            DB::raw('pl.bin_no COLLATE utf8mb4_general_ci as bin_no'),
            DB::raw('pl.remarks COLLATE utf8mb4_general_ci as remarks'),
        ]);

    if (!empty($location)) {
        $inward->where(function ($q) use ($location) {
            $q->where('sp.plant_site_code', 'LIKE', "%{$location}%")
                ->orWhere('sp.plant_site_name', 'LIKE', "%{$location}%")
                ->orWhere('sp.city', 'LIKE', "%{$location}%");
        });

        $outward->where(function ($q) use ($location) {
            $q->where('sp.plant_site_code', 'LIKE', "%{$location}%")
                ->orWhere('sp.plant_site_name', 'LIKE', "%{$location}%")
                ->orWhere('sp.city', 'LIKE', "%{$location}%");
        });
    }

    if (!empty($date)) {
        $inward->whereDate('dw.created_at', '<=', $date);
        $outward->whereDate('pl.created_at', '<=', $date);
    }

    return $inward->unionAll($outward);
}
}
