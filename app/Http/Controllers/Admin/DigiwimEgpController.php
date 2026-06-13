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


use App\Models\Admin;
use App\Models\DigiwimEgp;


use Auth;


class DigiwimEgpController extends Controller
{  

    public function __construct()
    {       
		$this->middleware('auth:admin'); 
    }

	
	public function index()
    {
        $records = DigiwimEgp::latest()->paginate(20);
        return view('admin.digiwim_egp.index', compact('records'));
    }

    public function create()
    {
        return view('admin.digiwim_egp.form');
    }

    public function store(Request $request)
	{
		$request->validate([

			'purpose_of_entry' => 'nullable|max:255',

			'outward_date' => 'nullable|date',
			'outward_time' => 'nullable',

			'customer_name' => 'nullable|max:255',
			'customer_location' => 'nullable|max:255',

			'invoice_challan_no' => 'nullable|max:255',
			'invoice_challan_date' => 'nullable|date',

			'vendor_name' => 'nullable|max:255',

			'truck_no' => 'nullable|max:100',
			'lr_cn_no' => 'nullable|max:255',

			'driver_mobile_no' => 'nullable|max:20',

			'custom' => 'nullable',
		]);

		DigiwimEgp::create([

			'purpose_of_entry' => $request->purpose_of_entry,

			'outward_date' => $request->outward_date,
			'outward_time' => $request->outward_time,

			'customer_name' => $request->customer_name,
			'customer_location' => $request->customer_location,

			'invoice_challan_no' => $request->invoice_challan_no,
			'invoice_challan_date' => $request->invoice_challan_date,

			'vendor_name' => $request->vendor_name,

			'truck_no' => $request->truck_no,
			'lr_cn_no' => $request->lr_cn_no,

			'driver_mobile_no' => $request->driver_mobile_no,

			'custom' => $request->custom,

			'added_by' => auth()->id()
		]);

		return redirect()
			->route('admin.digiwim-egp.index')
			->with('success','EGP Entry Created Successfully');
	}

    public function edit($id)
    {
        $record = DigiwimEgp::findOrFail($id);
        return view('admin.digiwim_egp.form', compact('record'));
    }

    public function update(Request $request, $id)
	{
		$record = DigiwimEgp::findOrFail($id);

		$record->update([

			'purpose_of_entry' => $request->purpose_of_entry,

			'outward_date' => $request->outward_date,
			'outward_time' => $request->outward_time,

			'customer_name' => $request->customer_name,
			'customer_location' => $request->customer_location,

			'invoice_challan_no' => $request->invoice_challan_no,
			'invoice_challan_date' => $request->invoice_challan_date,

			'vendor_name' => $request->vendor_name,

			'truck_no' => $request->truck_no,
			'lr_cn_no' => $request->lr_cn_no,

			'driver_mobile_no' => $request->driver_mobile_no,

			'custom' => $request->custom,

			'updated_by' => auth()->id()
		]);

		return redirect()
			->route('admin.digiwim-egp.index')
			->with('success','EGP Entry Updated Successfully');
	}

    public function destroy($id)
    {
        $record = DigiwimEgr::findOrFail($id);
        $record->delete();

        return redirect()
            ->route('admin.digiwim-egr.index')
            ->with('success', 'EGR entry deleted successfully.');
    }

		
	
}
