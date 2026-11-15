<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\DB;

use App\Models\DigiwimBinMaster;
use App\Models\Siteplant;

use Auth;

class DigiwimBinMasterController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index(Request $request)
    {
        $title = 'BIN Master Upload';
        $pagetitle = $title . ' Listing';

        return view('admin.digiwim_bin_master.index',
            compact('title', 'pagetitle')
        );
    }


    public function import(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:xls,xlsx'
        ]);

        $file = $request->file('excel_file');

        $spreadsheet = IOFactory::load($file->getPathname());

        $sheet = $spreadsheet->getActiveSheet();

        $rows = $sheet->toArray(
            null,
            true,
            true,
            true
        );

        $createdBy = Auth::user()->id;
        $createdDate = now();
        $errorRows = [];
        $insertedCount = 0;

        DB::beginTransaction();
        try {
            foreach ($rows as $index => $row) {

                /* Skip header */
                if ($index == 1) {
                    continue;
                }

                $rowNumber = $index;

                /* Skip blank XLS rows */

                if (count(array_filter($row,fn($value) =>trim((string) $value) !== '')) === 0) 
				{
                    continue;
                }


                /* XLS Column Mapping
                
                | A : Plant Code
                | B : Plant Name
                | C : BIN No
                | D : BIN Type
                | E : BIN Status
                | F : Storage Location
                | G : Storage Section
                | H : BIN Location
                | I : BIN Length
                | J : BIN Width
                | K : BIN Height
                | L : BIN Volume CFT Cap
                | M : BIN Volume CFT Cap 2
                | N  :BIN Weight KG Cap
                | O : BIN Weight KG Cap 2
                | P : custom1
                | Q : custom2
                | R : custom3
                | S : custom4
                | T : custom5
                */


                $plantCode = trim(($row['A'] ?? ''));
                $binNo = trim(($row['C'] ?? ''));

                if ($plantCode === '') {
                    $errorRows[] = [
                        'row' => $rowNumber,
                        'reason' => 'Plant Code is required'
                    ];
                    continue;
                }


                if ($binNo === '') {
                    $errorRows[] = [
                        'row' => $rowNumber,
                        'reason' => 'BIN No is required'
                    ];
                    continue;
                }
                if ( $row['I'] === null || trim( $row['I']) === '')
				{
                    $errorRows[] = [
                        'row' => $rowNumber,
                        'reason' => 'BIN Length is required'
                    ];
                    continue;
                }


                if ($row['J'] === null ||trim($row['J']) === '') 
				{
					$errorRows[] = [
                        'row' => $rowNumber,
                        'reason' => 'BIN Width is required'
                    ];
                    continue;
                }

                if ($row['K'] === null ||trim($row['K']) === '') 
				{
                    $errorRows[] = [
                        'row' => $rowNumber,
                        'reason' => 'BIN Height is required'
                    ];
                    continue;
                }
                if ($row['N'] === null ||trim($row['N']) === '') 
				{
                    $errorRows[] = [
                        'row' => $rowNumber,
                        'reason' => 'BIN Weight KG Cap is required'
                    ];
                    continue;
                }
                if (!is_numeric($row['I'])) {
					$errorRows[] = [
                        'row' => $rowNumber,
                        'reason' => 'BIN Length must be numeric'
                    ];
                    continue;
                }
                if (!is_numeric($row['J'])) {

                    $errorRows[] = [
                        'row' => $rowNumber,
                        'reason' => 'BIN Width must be numeric'
                    ];
                    continue;
                }


                if (!is_numeric($row['K'])) {

                    $errorRows[] = [
                        'row' => $rowNumber,
                        'reason' => 'BIN Height must be numeric'
                    ];

                    continue;
                }


                if (!is_numeric($row['N'])) {

                    $errorRows[] = [
                        'row' => $rowNumber,
                        'reason' => 'BIN Weight KG Cap must be numeric'
                    ];

                    continue;
                }

                $plant = Siteplant::where('plant_site_code', $plantCode )->first();


                if (!$plant) {
                    $errorRows[] = [
                        'row' => $rowNumber,
                        'reason' =>
                            'Invalid Plant Code: ' .
                            $plantCode
                    ];
                    continue;
                }


                $plantName = $plant->plant_site_location_name?? $plant->plant_site_name?? '';


                $binStatus = trim(($row['E'] ?? 'Active'));

                if ($binStatus === ''){
                    $binStatus = 'Active';
                }


                $exists = DigiwimBinMaster::where('bin_no',$binNo)->exists();


                if ($exists) {
                    $errorRows[] = [
                        'row' => $rowNumber,
                        'reason' =>
                            'BIN No ' .
                            $binNo .
                            ' already exists for Plant '
                    ];

                    continue;
                }

                $length = (float) $row['I'];
                $width = (float) $row['J'];
                $height =(float) $row['K'];
                $volumeCft =  ($length / 12) *($width / 12) * ($height / 12);


                /* volumecft +30% */

                $volumeCftCap2 = $volumeCft * 1.30;
                $weight =(float) $row['N'];
                $weightCap2 =$weight * 1.30;

                $data = [
                    'plant_code' => $plantCode,

                    'plant_name' => $plantName,

                    'bin_no' => $binNo,

                    'bin_type' => $row['D'] ?? null,

                    'bin_status' => $binStatus,

                    'storage_location' => $row['F'] ?? null,

                    'storage_section' => $row['G'] ?? null,

                    'bin_location' => $row['H'] ?? null,

                    'bin_length' => round($length, 2),

                    'bin_width' => round($width, 2),

                    'bin_height' => round($height, 2),

                    'bin_volume_cft_cap' => round($volumeCft, 3),

                    'bin_volume_cft_cap_2' => round($volumeCftCap2, 3),

                    'bin_weight_kg_cap' => round($weight, 2),

                    'bin_weight_kg_cap_2' => round($weightCap2, 2),

                    'custom1' => $row['P'] ?? null,

                    'custom2' => $row['Q'] ?? null,

                    'custom3' => $row['R'] ?? null,

                    'custom4' => $row['S'] ?? null,

                    'custom5' => $row['T'] ?? null,

                    'created_by' => $createdBy,

                    'created_at' => $createdDate,

                    'updated_at' => $createdDate,
                ];


                DigiwimBinMaster::create(
                    $data
                );


                $insertedCount++;
            }


            DB::commit();


            /*
            |--------------------------------------------------------------------------
            | Nothing inserted
            |--------------------------------------------------------------------------
            */

            if ($insertedCount === 0) {

                return redirect()
                    ->back()
                    ->withInput()
                    ->with([ 'error' => 'No data inserted. Please correct the highlighted errors.',
							'errorRows' =>$errorRows,
                    ]);
            }


            return redirect()->back()->with([
                    'success' => $insertedCount .' BIN rows inserted successfully.',
                    'errorRows' => $errorRows,
                ]);


        } catch (\Exception $e) {

            DB::rollback();

            return redirect()->back()->with('error','Error: ' .$e->getMessage() );
        }
    }

    public function datalist(Request $request)
    {
        $title = 'BIN Master List';

        $pagetitle = $title . ' Listing';


        $datalist = DigiwimBinMaster::orderBy(     'created_at',     'desc' )->get();


        return view( 'admin.digiwim_bin_master.datalist', compact('pagetitle', 'title', 'datalist' ));
    }

    public function manualupload()
    {
        return view(
            'admin.digiwim_bin_master.manualupload'
        );
    }

    public function save_manual_data(Request $request) 
	{

        $createdBy =Auth::user()->id;

        $createdDate =now();


        $plantCode =$request->input(    'plant_code',    []);

        $binNo =$request->input(    'bin_no',    []);

        $binType =$request->input(    'bin_type',    []);

        $binStatus =$request->input(    'bin_status',    []);

        $storageLocation =$request->input(    'storage_location',    []);

        $storageSection =$request->input(    'storage_section',    []);

        $binLocation =$request->input(    'bin_location',    []);

        $binLength =$request->input(    'bin_length',    []);

        $binWidth =$request->input(    'bin_width',    []);

        $binHeight =$request->input(    'bin_height',    []);

        $binWeight =$request->input(    'bin_weight_kg_cap',    []);

        $custom1 =$request->input(    'custom1',    []);

        $custom2 =$request->input(    'custom2',    []);

        $custom3 =$request->input(    'custom3',    []);

        $custom4 =$request->input(    'custom4',    []);

        $custom5 =$request->input(    'custom5',    []);


        $count =count($plantCode);


        $insertedCount = 0;

        $errorRows = [];


        for ($i = 0;$i < $count;$i++
        ) {
			$rowNumber =    $i + 1;

            if ( empty($plantCode[$i]) &&  empty($binNo[$i]) && empty($binLength[$i]) && empty($binWidth[$i]) &&  empty($binHeight[$i]) && empty($binWeight[$i])) 
				{
					continue;
				}
            try {
                
				if (empty($plantCode[$i] )) {
                    $errorRows[] = [ 'row' => $rowNumber,'reason' => 'Plant Code is required' ];
                    continue;
                }


                if (empty( $binNo[$i] )) {
                    $errorRows[] = [
                        'row' => $rowNumber,
                        'reason' =>
                            'BIN No is required'
                    ];
                    continue;
                }


                if ( $binLength[$i] === null || $binLength[$i] === '' ) {
                    $errorRows[] = [
                        'row' => $rowNumber,
                        'reason' =>
                            'BIN Length is required'
                    ];

                    continue;
                }


                if ($binWidth[$i] === null || $binWidth[$i] === '') {

                    $errorRows[] = [
                        'row' => $rowNumber,
                        'reason' =>
                            'BIN Width is required'
                    ];

                    continue;
                }


                if ($binHeight[$i] === null ||$binHeight[$i] === '') {

                    $errorRows[] = [
                        'row' => $rowNumber,
                        'reason' =>
                            'BIN Height is required'
                    ];
                    continue;
                }


                if ($binWeight[$i] === null || $binWeight[$i] === '') {

                    $errorRows[] = [
                        'row' => $rowNumber,
                        'reason' =>
                            'BIN Weight is required'
                    ];

                    continue;
                }

                $plant = Siteplant::where( 'plant_site_code', trim($plantCode[$i]))->first();


                if (!$plant) {
                    $errorRows[] = [
                        'row' => $rowNumber,
                        'reason' =>
                            'Invalid Plant Code: ' .
                            $plantCode[$i]
                    ];

                    continue;
                }

                $exists =
                    DigiwimBinMaster::where('bin_no', trim( $binNo[$i]) )->exists();


                if ($exists) {
                    $errorRows[] = [
                        'row' => $rowNumber,
                        'reason' =>
                            'BIN No ' .
                            $binNo[$i] .
                            ' already exists for Plant ' .
                            $plantCode[$i]
                    ];

                    continue;
                }

                if (!is_numeric(  $binLength[$i] )) {

                    $errorRows[] = [
                        'row' => $rowNumber,
						'reason' =>
                            'BIN Length must be numeric'
                    ];
                    continue;
                }


                if ( !is_numeric( $binWidth[$i]) ) {

                    $errorRows[] = [
                        'row' =>$rowNumber,
                        'reason' =>
                            'BIN Width must be numeric'
                    ];

                    continue;
                }


                if (!is_numeric( $binHeight[$i])) {

                    $errorRows[] = [
                        'row' =>$rowNumber,
						'reason' =>
                            'BIN Height must be numeric'
                    ];

                    continue;
                }


                if (!is_numeric($binWeight[$i] )) {

                    $errorRows[] = [
                        'row' =>$rowNumber,
						'reason' =>
                            'BIN Weight must be numeric'
                    ];

                    continue;
                }

                $length =   (float) $binLength[$i];
                $width =(float)$binWidth[$i];
                $height =(float)$binHeight[$i];

                $volumeCft = ($length / 12) *($width / 12) *($height / 12);

                $volumeCftCap2 = $volumeCft * 1.30;


                $weight = (float) $binWeight[$i];
                $weightCap2 = $weight * 1.30;

                $status = $binStatus[$i];

                $data = [ 
				'plant_code' => $plantCode[$i],
                'plant_name' => $plant->plant_site_location_name ?? $plant->plant_site_name ?? null,
                 'bin_no' => $binNo[$i],
				 'bin_type' => $binType[$i]?? null,
				 'bin_status' =>  $status,
				'storage_location' =>  $storageLocation[$i]  ?? null,
				'storage_section' =>  $storageSection[$i]  ?? null,
				'bin_location' =>  $binLocation[$i]  ?? null,
				'bin_length' =>  round($length,2),
				'bin_width' =>  round($width,2),
				'bin_height' =>  round($height,2),
				'bin_volume_cft_cap' =>  round($volumeCft, 2),
				'bin_volume_cft_cap_2' =>  round($volumeCftCap2,2),
				'bin_weight_kg_cap' =>  round( $weight, 2),
				'bin_weight_kg_cap_2' =>  round($weightCap2,2),
				'custom1' =>  $custom1[$i]  ?? null,
				'custom2' =>  $custom2[$i]  ?? null,
				'custom3' =>  $custom3[$i]  ?? null,
				'custom4' =>  $custom4[$i]  ?? null,
				'custom5' =>  $custom5[$i]  ?? null,
				'created_by' =>  $createdBy,
				'created_at' =>  $createdDate,
				'updated_at' =>  $createdDate,
                ];


                DigiwimBinMaster::create( $data);
                $insertedCount++;
            } catch (\Exception $e) {
                $errorRows[] = [
                    'row' =>  $rowNumber,
                    'reason' =>  $e->getMessage()
                ];
                continue;
            }
        }


        if ($insertedCount === 0) {
            return redirect()->back()->withInput()->with([
                    'error' =>  'No data inserted. Please correct row errors.',
                    'errorRows' =>  $errorRows,
                ]);
        }


        return redirect()->back()
            ->with(['success' =>  $insertedCount .' BIN rows inserted successfully.',
                'errorRows' =>$errorRows,
            ]);
    }


    public function fetchPlantData(Request $request ) 
	{
        try {

            $plantCode = trim($request->plant_code);
            if (  $plantCode === '' ) {
                return response()
                    ->json([  'error' => 'Plant Code is required' ]);
            }


            $plant = Siteplant::where('plant_site_code',$plantCode)->first();
            if (!$plant) {
                return response()
                    ->json([
							'error' => 'Invalid Plant Code: ' .      $plantCode
                    ]);
            }


            return response()
                ->json([
                    'plant_name' => $plant->plant_site_location_name  ?? $plant->plant_site_name  ?? '',
                ]);


        } catch (\Exception $e) {

            return response()
                ->json([
                    'error' =>  $e->getMessage()

                ], 500);
        }
    }
}