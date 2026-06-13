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
use App\Models\DigiwimEgr;


use Auth;


class DigiwimEgrController extends Controller
{  

    public function __construct()
    {       
		$this->middleware('auth:admin'); 
    }

	
	public function index()
    {
        $records = DigiwimEgr::latest()->paginate(20);
        return view('admin.digiwim_egr.index', compact('records'));
    }

    public function create()
    {
        return view('admin.digiwim_egr.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'inward_date' => 'nullable|date',
            'inward_time' => 'nullable',
            'purpose_of_entry' => 'nullable|string|max:255',
            'supplier_name' => 'nullable|string|max:255',
            'supplier_location' => 'nullable|string|max:255',
            'invoice_challan_no' => 'nullable|string|max:255',
            'invoice_challan_date' => 'nullable|date',
            'vendor_name' => 'nullable|string|max:255',
            'truck_no' => 'nullable|string|max:100',
            'lr_cn_no' => 'nullable|string|max:255',
            'driver_mobile_no' => 'nullable|string|max:20',
            'custom' => 'nullable|string',
        ]);

        DigiwimEgr::create($request->all());

        return redirect()
            ->route('admin.digiwim-egr.index')
            ->with('success', 'EGR entry created successfully.');
    }

    public function edit($id)
    {
        $record = DigiwimEgr::findOrFail($id);
        return view('admin.digiwim_egr.edit', compact('record'));
    }

    public function update(Request $request, $id)
    {
        $record = DigiwimEgr::findOrFail($id);

        $request->validate([
            'inward_date' => 'nullable|date',
            'inward_time' => 'nullable',
            'purpose_of_entry' => 'nullable|string|max:255',
            'supplier_name' => 'nullable|string|max:255',
            'supplier_location' => 'nullable|string|max:255',
            'invoice_challan_no' => 'nullable|string|max:255',
            'invoice_challan_date' => 'nullable|date',
            'vendor_name' => 'nullable|string|max:255',
            'truck_no' => 'nullable|string|max:100',
            'lr_cn_no' => 'nullable|string|max:255',
            'driver_mobile_no' => 'nullable|string|max:20',
            'custom' => 'nullable|string',
        ]);

        $record->update($request->all());

        return redirect()
            ->route('admin.digiwim-egr.index')
            ->with('success', 'EGR entry updated successfully.');
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
