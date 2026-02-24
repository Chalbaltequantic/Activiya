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

use App\Models\TruckMaster;

use Auth;

class TruckMasterController extends Controller
{
    
	  //
	public function __construct()
    {
        $this->middleware('auth:admin');     
    }
	public function index()
    {
        $trucks = TruckMaster::all();
        return view('admin.truck_master.index', compact('trucks'));
    }

    public function create()
    {
        return view('admin.truck_master.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required',
            'description' => 'required',
            'short_name' => 'required',
            'length' => 'required',
            'width' => 'required',
            'height' => 'required',
            'weight_capacity' => 'required',
            'max_volume_capacity' => 'required',
            'min_capacity' => 'required',
            'utilities' => 'required',
            'status' => 'required'
            
        ]);
        	
		$created_by = Auth::user()->id; 
		 try {
				Truckmaster::create([
				'code' => $request->code,
				'description' => $request->description,
				'short_name' => $request->short_name,
				'length' => $request->length,
				'width' => $request->width,
				'height' => $request->height,
				'weight_capacity' => $request->weight_capacity,
				'max_volume_capacity' => $request->max_volume_capacity,
				'min_capacity' => $request->min_capacity,
				't_body' => $request->t_body,
				'utilities' => $request->utilities,
				'created_at' => Carbon::now(),
				'status' => $request->status,
				'created_by' => $created_by
			
			]);
			 return redirect()->route('admin.truck_master.index')->with('success', 'Truck data inserted successfully.');
		 }
		 catch (\Exception $e) {
           return redirect()->back()->with('error', 'Error: Something went wrong');
        }
			
    }

    public function show(TruckMaster $truck_master)
    {
		$trucks = TruckMaster::all();
	   return view('admin.truck_master.index', compact('trucks'));
    }

    public function edit(TruckMaster $truck_master)
    {
		//print_r($truck_master); 
		
		$id = $truck_master->id;
		$truck_master = TruckMaster::findOrFail($id);
        return view('admin.truck_master.edit', compact('truck_master'));
    }

    public function update(Request $request, TruckMaster $truck_master)
    {
            $validated = $request->validate([
            'code' => 'required',
            'description' => 'required',
            'short_name' => 'required',
            'length' => 'required',
            'width' => 'required',
            'height' => 'required',
            'weight_capacity' => 'required',
            'max_volume_capacity' => 'required',
            'min_capacity' => 'required',
			'utilities'=>'required',
            'status' => 'required'
            
        ]);
        	
		$created_by = Auth::user()->id; 
		$id = $request->id;
		 try {
			Truckmaster::find($id)->update([
		
				'code' => $request->code,
				'description' => $request->description,
				'short_name' => $request->short_name,
				'length' => $request->length,
				'width' => $request->width,
				'height' => $request->height,
				'weight_capacity' => $request->weight_capacity,
				'max_volume_capacity' => $request->max_volume_capacity,
				'min_capacity' => $request->min_capacity,
				't_body' => $request->t_body,
				'utilities' => $request->utilities,
				'updated_at_at' => Carbon::now(),
				'status' => $request->status,
				'updated_by' => $created_by
			
			]);
			 return redirect()->route('admin.truck_master.index')->with('success', 'Truck data updated successfully.');
		 }
		 catch (\Exception $e) {
           return redirect()->back()->with('error', 'Error: Something went wrong');
        }
			
    }

    public function destroy(TruckMaster $truck_master)
    {
        $truck_master->delete();
        return redirect()->route('admin.truck_master.index')->with('success', 'Truck deleted successfully.');
    }
}