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


class DigiwimLedgerController extends Controller
{
   

    public function __construct()
    {
       
		$this->middleware('auth:admin'); 
    }

	


	public function index(Request $request)
	{
		$location = $request->location;
		$date     = $request->date;

		/*
		|--------------------------------------------------------------------------
		| OUTWARD = Unloading Operation Items
		|--------------------------------------------------------------------------
		*/
		$outward = DB::table('digiwim_operation_items as oi')
			->join('digiwim_operations as op', 'op.id', '=', 'oi.operation_id')
			->leftJoin('materials as m', 'm.material_code', '=', 'oi.material_code')
			->select([
				DB::raw("'OUTWARD' as movement_type"),
				'oi.created_at',
				'oi.material_code',
				'oi.material_description',
				'm.division',
				'm.brand',
				'm.sub_brand',
				'm.uom',
				'm.piece_per_box',
				DB::raw('Null as mrp'),
				'm.weight',
				'm.volume',
				'op.invoice_challan_no',
				'op.invoice_date',
				'op.supplier_code_name as storage_plant_name',
				'op.supplier_code_name as storage_plant_code',
				'op.supplier_code_name as storage_plant_location',
				'oi.batch_no',
				DB::raw('oi.qty as outward_qty'),
				DB::raw('NULL as outward_case'),
				DB::raw('oi.bin_no as outward_bin'),
				DB::raw('NULL as inward_qty'),
				DB::raw('NULL as inward_case'),
				DB::raw('NULL as inward_bin'),
				'oi.remarks',
			])
			->where('op.operation_type', 'unloading');

		/*
		|--------------------------------------------------------------------------
		| INWARD = Preloading Table
		|--------------------------------------------------------------------------
		*/
		$inward = DB::table('digiwim_preloading as pl')
			->leftJoin('materials as m', 'm.material_code', '=', 'pl.material_code')
			->leftJoin('site_plants as sp', 'sp.plant_site_code', '=', 'pl.consignee_code')
			->select([
				DB::raw("'INWARD' as movement_type"),
				'pl.created_at',
				'pl.material_code',
				'pl.material_description',
				'm.division',
				'm.brand',
				'm.sub_brand',
				'm.uom',
				'm.piece_per_box',
				DB::raw('Null as mrp'),
				'm.weight',
				'm.volume',
				'pl.invoice_challan_no',
				DB::raw('NULL as invoice_date'),
				'sp.plant_site_code as storage_plant_code',
				'sp.plant_site_name as storage_plant_name',
				'sp.city as storage_plant_location',
				'pl.batch_no',
				DB::raw('NULL as outward_qty'),
				DB::raw('NULL as outward_case'),
				DB::raw('NULL as outward_bin'),
				DB::raw('pl.qty as inward_qty'),
				DB::raw('NULL as inward_case'),
				DB::raw('pl.bin_no as inward_bin'),
				'pl.remarks',
			]);

		/*
		|--------------------------------------------------------------------------
		| Filters
		|--------------------------------------------------------------------------
		*/
		if (!empty($location)) {
			$outward->where(function ($q) use ($location) {
				$q->where('op.supplier_code_name', 'LIKE', "%{$location}%");
			});

			$inward->where(function ($q) use ($location) {
				$q->where('sp.plant_site_code', 'LIKE', "%{$location}%")
				  ->orWhere('sp.plant_site_name', 'LIKE', "%{$location}%")
				  ->orWhere('sp.city', 'LIKE', "%{$location}%");
			});
		}

		if (!empty($date)) {
			$outward->whereDate('oi.created_at', $date);
			$inward->whereDate('pl.created_at', $date);
		}

		/*
		|--------------------------------------------------------------------------
		| Combine both table data
		|--------------------------------------------------------------------------
		*/
		$records = $outward
			->unionAll($inward)
			->orderBy('created_at', 'desc')
			->get();

		return view('admin.digiwim.ledger.index', compact('records'));
	}
	
}
