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
use Auth;


class MaterialController extends Controller
{
    //
	public function __construct()
    {
        $this->middleware('auth:admin');     
    }
	
	public function index(Request $request)
    {
        $title = 'Material Data Upload';
        $pagetitle = $title.' Listing';
             
        return view('admin.materialdata.index',compact(['pagetitle','title']));
    }
	
	public function materialdatalist(Request $request)
    {
        $title = 'material Data Upload';
        $pagetitle = $title.' Listing';
       $user_role = Auth::user()->role_id;
		$data = $request->all();        
	    $materialdatalist = Material::orderBy('created_at', 'desc')->get();       
        return view('admin.materialdata.materialdatalist',compact(['pagetitle','title','materialdatalist','user_role']));
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
		
		$created_by = Auth::user()->id; 
		
		$createddate = date('Y-m-d');

		$errorRows = [];
		$insertedCount = 0;
		$validData = [];

        DB::beginTransaction();
        try {
            foreach ($rows as $index => $row) {
                if ($index == 1) continue; // skip header row
				
				 $rowNumber = $index + 1;
				if (count(array_filter($row, fn($value) => trim((string)$value) !== '')) === 0) {
						continue;
					}
				   $data = [
                    'material_code' => $row['A'] ?? null,
                    'material_description' => $row['B'] ?? null,
                    'uom' => $row['C'] ?? null,
                    'division' => $row['D'] ?? null,
                    'piece_per_box' => $row['E'] ?? null,
                    'length_cm' => $row['F'] ?? null,
                    'width_cm' => $row['G'] ?? null,
                    'height_cm' => $row['H'] ?? null,
                    'net_weight_kg' => $row['I'] ?? null,
                    'gross_weight_kg' => $row['J'] ?? null,
                    'volume_cft' => $row['K'] ?? null,
                    'category' => $row['L'] ?? null,
                    'pallets' => $row['M'] ??  null,
                    'brand' => $row['N'] ?? null,
                    'sub_brand' => $row['O'] ?? null,
                    'thickness_load' => $row['P'] ?? null,
                    'sequence' => $row['Q'] ?? null,
                    'associated' => $row['R'] ?? null,
                    'parent' => $row['S'] ?? null,
                    'child' => $row['T'] ?? null,
                    'created_at' => $createddate,
                    'created_by' => Auth::user()->id,
                    'status' => '1'
                ];
           
		   // ---- INSERT ----
           Material::create($data);
			$insertedCount++;
			
			
            }

            DB::commit();
			
			 if ($insertedCount === 0) {
            // No data inserted
            return back()
                ->with([
                    'errorRows' => $errorRows,
                    'error' => 'Please correct the highlighted errors.',
                ]);
        }

        // Success, maybe partial insert
        return redirect()->back()->with([
            'success' => "$insertedCount rows inserted successfully.",
            'failedRows' => $errorRows
        ]);
			
			
           // return redirect()->back()->with('success', 'Excel imported successfully!');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
	
	public function getMaterialdataDetails($id)
	{
		$Materialdata = Material::find($id);
		$userid = auth()->user()->id; //get loggedin user id		
		return view('admin.materialdata.editMaterialdata', compact('Materialdata'));
	}

	public function save_Materialdata(Request $request)
	{
		$validatedData = $request->validate(
			[
				'material_code' => 'required',
				'material_description' => 'required',
			],
			[
				'material_code.required' => 'Please enter material name',
				'material_description.required' => 'Please enter material description',
			]
		);
			$id = $request->id;
			
			
			Material::find($id)->update([
				'material_code' => $request->material_code,
				'material_description' => $request->material_description,
				'uom' => $request->uom,
				'division' => $request->division,
				'piece_per_box' => $request->piece_per_box,
				'length_cm' => $request->length_cm,
				'width_cm' => $request->width_cm,
				'height_cm' => $request->height_cm,
				'net_weight_kg' => $request->net_weight_kg,
				'gross_weight_kg' => $request->gross_weight_kg,
				'volume_cft' => $request->volume_cft,
				'category' => $request->category,
				'pallets' => $request->pallets,
				'brand' => $request->brand,
				'sub_brand' => $request->sub_brand,
				'thickness_load' => $request->thickness_load,
				'sequence' => $request->sequence,
				'associated' => $request->associated,
				'parent' => $request->parent,
				'child' => $request->child,
				'updated_at' => Carbon::now(),
				'updated_by' => Auth::user()->id,
				'status' => $request->status,
			]);
			return Redirect('/admin/materialdata')->with('success', 'Data updated successfully!');
		
	}
	
	
	public function DeleteMaterialData($id)
	{
		//$Materialdata = Material::find($id);		
		Material::find($id)->delete();
		return Redirect('/admin/materialdata')->with('success', 'Data deleted successfully!');
	}
	
	
	//manual Upload
	
	public function manualupload()
	{
		$userid = auth()->user()->id; //get loggedin user id		
		return view('admin.materialdata.manualupload');
	}

	public function save_manual_Materialdata(Request $request)
	{
		
		$created_by = Auth::user()->id; 
		
		$createddate = date('Y-m-d');
		$material_code     = $request->input('material_code', []);
		$material_description     = $request->input('material_description', []);
		$uom = $request->input('uom', []);
		$division = $request->input('division', []);
		$piece_per_box = $request->input('piece_per_box', []);
		$length_cm = $request->input('length_cm', []);
		$width_cm = $request->input('width_cm', []);
		$height_cm = $request->input('height_cm', []);
		$net_weight_kg = $request->input('net_weight_kg', []);
		$gross_weight_kg = $request->input('gross_weight_kg', []);
		$volume_cft = $request->input('volume_cft', []);
		$category = $request->input('category', []);
		$pallets = $request->input('pallets', []);
		$brand = $request->input('brand', []);
		$sub_brand = $request->input('sub_brand', []);
		$thickness = $request->input('thickness', []);
		$load_sequence = $request->input('load_sequence', []);
		$associated = $request->input('associated', []);
		$parent = $request->input('parent', []);
		$child = $request->input('child', []);
		
		$count = count($material_code);
		
		$errorRows = [];
		$insertedCount = 0;
		$validData = [];

        DB::beginTransaction();
        try {
            for ($i = 0; $i < $count; $i++) {

                $rowNumber = $i + 1;
				
				if(!empty($material_code[$i]))
				{
					
					
					$data = [
						'material_code' => $material_code[$i] ?? null,
						'material_description' => $material_description[$i] ?? null,
						'uom' => $uom[$i] ?? null,
						'division' => $division[$i] ?? null,
						'piece_per_box' => $piece_per_box[$i] ?? null,
						'length_cm' => $length_cm[$i] ?? null,
						'width_cm' => $width_cm[$i] ?? null,
						'height_cm' => $height_cm[$i] ?? null,
						'net_weight_kg' => $net_weight_kg[$i] ?? null,
						'gross_weight_kg' => $gross_weight_kg[$i] ?? null,
						'volume_cft' => $volume_cft[$i] ?? null,
						'category' => $category[$i] ?? null,
						'pallets' => $pallets[$i] ?? null,
						'brand' => $brand[$i] ?? null,
						'sub_brand' =>  $sub_brand[$i] ?? null,
						'thickness' => $thickness[$i] ?? null,
						'load_sequence' => $load_sequence[$i] ?? null,
						'associated' => $associated[$i] ?? null,
						'parent' => $parent[$i] ?? null,
						'child' => $child[$i] ?? null,
						'created_at' => $createddate,
						'created_by' => Auth::user()->id,
						'status' => '1'
					];
					// ---- INSERT ----
					Material::create($data);
					$insertedCount++;
				}
            }
            DB::commit();
			
			if ($insertedCount === 0) {
            // No data inserted
            return back()
                ->withInput()
                ->with([
                    'errorRows' => $errorRows,
                    'error' => 'No data inserted. Please correct the highlighted errors.',
                ]);
				
				
        }

        // Success, maybe partial insert
        return redirect()->back()->with([
            'success' => "$insertedCount rows inserted successfully.",
            'failedRows' => $errorRows
        ]);
		
			
        } catch (\Exception $e) {
            DB::rollback();
           // return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
			return redirect()->back()->with('error', 'Error: Something went wrong.');
        }
		
	}
	
}
