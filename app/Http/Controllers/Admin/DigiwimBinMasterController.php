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
        return view('admin.digiwim_bin_master.index',compact('title', 'pagetitle' ));
    }


    public function import(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:xls,xlsx'
        ]);

        $file = $request->file('excel_file');

        $spreadsheet = IOFactory::load($file->getPathname());

        $sheet = $spreadsheet->getActiveSheet();

        $rows = $sheet->toArray( null, true,true, true );

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

                DigiwimBinMaster::create($data);
                $insertedCount++;
            }


            DB::commit();

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

    private function getFilteredBinQuery( Request $request ) 
	{

        $query =  DigiwimBinMaster::query();

        if ($request->filled('search') ) 
		{

            $search =trim( $request->search);

            $query->where(
                function ($q) use ($search) {
                    $q->where('plant_code', 'like','%' . $search . '%')
                    ->orWhere('plant_name', 'like', '%' . $search . '%' )
                    ->orWhere( 'bin_no', 'like', '%' . $search . '%')
                    ->orWhere('bin_type','like','%' . $search . '%' )
                    ->orWhere( 'bin_status','like','%' . $search . '%' )
                    ->orWhere( 'storage_location','like', '%' . $search . '%')
                    ->orWhere('storage_section','like', '%' . $search . '%' )
                    ->orWhere('bin_location', 'like','%' . $search . '%' );
                }
            );
        }

        if ( $request->filled('plant_code') ) {
            $query->where('plant_code',$request->plant_code );
        }

		if ($request->filled('bin_status') ) {
            $query->where('bin_status', $request->bin_status);
        }
        return $query;
    }

    public function datalist(Request $request)
	{
		$title = 'BIN Master List';
		$pagetitle = $title . ' Listing';
		$perPage = (int) $request->get('per_page', 25);
		if (!in_array($perPage, [10, 25, 50, 100])) {
			$perPage = 25;
		}

		$allowedSortColumns = [

			'plant_code',
			'plant_name',
			'bin_no',
			'bin_type',
			'bin_status',
			'storage_location',
			'storage_section',
			'bin_location',
			'bin_length',
			'bin_width',
			'bin_height',
			'bin_volume_cft_cap',
			'bin_volume_cft_cap_2',
			'bin_weight_kg_cap',
			'bin_weight_kg_cap_2',
			'custom1',
			'custom2',
			'custom3',
			'custom4',
			'custom5',
			'created_at'
		];


		$sortBy = $request->get('sort_by','created_at');

		$sortDirection = strtolower(
			$request->get('sort_direction','desc')
		);


		if (!in_array($sortBy, $allowedSortColumns)) {
			$sortBy = 'created_at';
		}
		if (!in_array($sortDirection, ['asc', 'desc'])) {
			$sortDirection = 'desc';
		}
		
		$query = $this->getFilteredBinQuery($request);

		$datalist = $query->orderBy($sortBy,$sortDirection)->paginate($perPage);

		$datalist->appends($request->query());
		$plants = DigiwimBinMaster::select('plant_code','plant_name')
			->distinct()->orderBy('plant_code')->get();

		$userRole = Auth::user()->role_id ?? null;


		return view('admin.digiwim_bin_master.datalist',
			compact('pagetitle','title','datalist','plants','perPage','userRole','sortBy','sortDirection')
		);
	}

    public function export(Request $request) {

        try {
            $query = $this->getFilteredBinQuery($request );
            $datalist = $query->orderBy('created_at','desc')->get();
            if ( $datalist->count() === 0 ) {
                return redirect()->back()->with('error', 'No BIN Master data found for download.');
            }
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();


            $sheet =  $spreadsheet->getActiveSheet();
            $sheet->setTitle('BIN Master');
            $sheet->setCellValue('A1','Plant Code');
            $sheet->setCellValue('B1','Plant Name' );
            $sheet->setCellValue('C1', 'BIN No.');            
			$sheet->setCellValue('D1','BIN Type');            
			$sheet->setCellValue('E1', 'BIN Status' );
            $sheet->setCellValue('F1','Storage Location (Virtual)');
            $sheet->setCellValue('G1','Storage Section (Floor)');
            $sheet->setCellValue( 'H1','BIN Location' );
            $sheet->setCellValue('I1', 'BIN Length (Inch)');
            $sheet->setCellValue('J1','BIN Width (Inch)');
            $sheet->setCellValue('K1', 'BIN Height (Inch)');
            $sheet->setCellValue('L1','BIN Volume (CFT) Cap');
            $sheet->setCellValue('M1','BIN Volume (CFT) Cap 2');
            $sheet->setCellValue('N1','BIN Weight (KG) Cap' );
            $sheet->setCellValue('O1','BIN Weight (KG) Cap 2');
            $sheet->setCellValue('P1','Custom1' );
            $sheet->setCellValue('Q1','Custom2' );
            $sheet->setCellValue('R1','Custom3');
            $sheet->setCellValue('S1','Custom4');
            $sheet->setCellValue('T1','Custom5' );

            $xlsRow = 2;
            foreach ($datalist as $bin) 
			{

				$sheet->setCellValueExplicit('A' . $xlsRow, $bin->plant_code,    \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
				
				$sheet->setCellValue('B' . $xlsRow, $bin->plant_name);

				$sheet->setCellValueExplicit(    'C' . $xlsRow,    $bin->bin_no,    \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);

				$sheet->setCellValue(    'D' . $xlsRow,    $bin->bin_type);

				$sheet->setCellValue(    'E' . $xlsRow,    $bin->bin_status);

				$sheet->setCellValue(    'F' . $xlsRow,    $bin->storage_location);

				$sheet->setCellValue(    'G' . $xlsRow,    $bin->storage_section);

				$sheet->setCellValue(    'H' . $xlsRow,    $bin->bin_location);

				$sheet->setCellValue(    'I' . $xlsRow,    $bin->bin_length);

				$sheet->setCellValue(    'J' . $xlsRow,    $bin->bin_width);

				$sheet->setCellValue(    'K' . $xlsRow,    $bin->bin_height);

				$sheet->setCellValue(    'L' . $xlsRow,    $bin->bin_volume_cft_cap);

				$sheet->setCellValue(    'M' . $xlsRow,    $bin->bin_volume_cft_cap_2);

				$sheet->setCellValue(    'N' . $xlsRow,    $bin->bin_weight_kg_cap);

				$sheet->setCellValue(    'O' . $xlsRow,    $bin->bin_weight_kg_cap_2);

				$sheet->setCellValue(    'P' . $xlsRow,    $bin->custom1);

				$sheet->setCellValue(    'Q' . $xlsRow,    $bin->custom2);

				$sheet->setCellValue(    'R' . $xlsRow,    $bin->custom3);

				$sheet->setCellValue(    'S' . $xlsRow,    $bin->custom4);

				$sheet->setCellValue(    'T' . $xlsRow,    $bin->custom5);

				$xlsRow++;
            }

            $sheet->getStyle( 'A1:T1')->getFont()->setBold(true);

            $sheet->getStyle( 'A1:T1')->getAlignment()->setHorizontal(    \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);


            $sheet->getStyle( 'A1:T1')->getAlignment()->setVertical(    \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);


            $sheet->getStyle( 'A1:T1')->getAlignment()->setWrapText(    true);

            $sheet->freezePane('A2');

            $sheet->setAutoFilter('A1:T' .($xlsRow - 1));


            foreach (range('A', 'T')as $column) 
			{
					$sheet->getColumnDimension($column)->setAutoSize(true);
            }
            $fileName ='BIN_Master_' .date('Y-m-d_H-i-s') .'.xlsx';
            $writer =new \PhpOffice\PhpSpreadsheet\Writer\Xlsx(    $spreadsheet);

            return response()->streamDownload(
					function () use ($writer) {
						$writer->save('php://output');
					}, $fileName,[ 'Content-Type' =>            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', ]);

        } catch (\Exception $e) {
            return redirect()->back()->with('error','Export Error: ' . $e->getMessage());
        }
    }

    public function manualupload()
    {
        return view( 'admin.digiwim_bin_master.manualupload');
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


    /* AJAX FETCH PLANT */

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
				return response() ->json(['error' => 'Invalid Plant Code: ' .$plantCode]);
            }
            return response()
                ->json(['plant_name' => $plant->plant_site_location_name  ?? $plant->plant_site_name  ?? '',]);
        } catch (\Exception $e) {
            return response()
                ->json(['error' =>  $e->getMessage()], 500);
        }
    }

    /* EDIT BIN    */

    public function edit( $id)
    {

        if (!$this->isAdminUser() )
		{
			abort(403,'You are not authorized to edit BIN Master.'
            );
        }


        $title = 'Edit BIN Master';
        $pagetitle = $title;
        $bin = DigiwimBinMaster::findOrFail($id );
        return view('admin.digiwim_bin_master.edit', compact('title','pagetitle','bin' ));
    }


    /* UPDATE BIN   */

    public function update(Request $request, $id )
    {

        if ( !$this->isAdminUser()) {
            abort(403,'You are not authorized to update BIN Master.');
        }

		$request->validate([
            'plant_code' =>'required',
            'bin_no' =>'required',
            'bin_status' =>'required|in:Active,Inactive',
            'bin_length' =>'required|numeric|min:0',
            'bin_width' =>'required|numeric|min:0',
            'bin_height' =>'required|numeric|min:0',
            'bin_weight_kg_cap' =>'required|numeric|min:0',
        ]);


        try {
            $bin =DigiwimBinMaster::findOrFail($id);
            $plant =Siteplant::where('plant_site_code', trim($request->plant_code))->first();

            if (!$plant) 
			{
				return redirect()->back()->withInput()->with('error','Invalid Plant Code: ' .        $request->plant_code);
            }

           $exists =DigiwimBinMaster::where('bin_no', trim($request->bin_no))->where('id','!=',$id)->exists();


            if ($exists) 
			{
				return redirect()->back()->withInput()->with('error','BIN No ' .$request->bin_no .        ' already exists.');
            }

            $length =(float)$request->bin_length;
            $width =(float)$request->bin_width;
            $height =(float)$request->bin_height;
            $volumeCft =($length / 12) *($width / 12) *($height / 12);
            $volumeCftCap2 =$volumeCft *1.30;
            $weight =(float)$request->bin_weight_kg_cap;
            $weightCap2 =$weight *1.30;
            $bin->plant_code =trim($request->plant_code);
            $bin->plant_name =$plant->plant_site_location_name?? $plant->plant_site_name?? null;
            $bin->bin_no =trim($request->bin_no);
            $bin->bin_type =$request->bin_type;
            $bin->bin_status =$request->bin_status;
            $bin->storage_location =$request->storage_location;
            $bin->storage_section =$request->storage_section;
            $bin->bin_location =$request->bin_location;
            $bin->bin_length =round($length,2);
            $bin->bin_width =round($width,2);
            $bin->bin_height =round($height,2);
            $bin->bin_volume_cft_cap =round($volumeCft,2);
            $bin->bin_volume_cft_cap_2 =round($volumeCftCap2,2);
            $bin->bin_weight_kg_cap =round($weight, 2);
            $bin->bin_weight_kg_cap_2 =round($weightCap2, 2);
            $bin->custom1 =$request->custom1;
            $bin->custom2 =$request->custom2;
            $bin->custom3 =$request->custom3;
            $bin->custom4 =$request->custom4;
            $bin->custom5 =$request->custom5;
            $bin->updated_at =now();
            $bin->save();
            return redirect()->route('admin.binmaster.list')->with('success',    'BIN Master updated successfully.');

        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Update Error: ' . $e->getMessage());
        }
    }


    /* DELETE BIN */

    public function delete(Request $request, $id)
    {

        if (!$this->isAdminUser()) 
		{

            return response()->json(['status' =>false, 'message' =>'You are not authorized to delete BIN Master.'], 403);
        }
        try {
            $bin =DigiwimBinMaster::findOrFail($id);
            $bin->delete();
            return response()->json(['status' =>true, 'message' =>'BIN Master deleted successfully.']);
        } catch (\Exception $e) {
            return response()->json(['status' =>false, 'message' =>$e->getMessage()], 500);
        }
    }

    private function isAdminUser()
    {
        $user =  Auth::user();
        if (!$user) {
            return false;
        }
        return (int)$user->role_id === self::ADMIN_ROLE_ID;
    }

}