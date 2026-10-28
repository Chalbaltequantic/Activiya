<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\SlabMaster;
use App\Models\Siteplant;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class SlabMasterController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index(Request $request)
    {
        $title = 'Slab Master Upload';
        $pagetitle = $title;

        return view('admin.slab_master.index', compact('title', 'pagetitle'));
    }

    public function import(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:xls,xlsx',
        ]);

        $createdBy = Auth::user()->id;
        $createdDate = now();
        $errorRows = [];
        $insertedCount = 0;

        try {
            $spreadsheet = IOFactory::load($request->file('excel_file')->getRealPath());
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray(null, true, true, true);

            DB::beginTransaction();

            foreach ($rows as $rowNumber => $row) {
                if ($rowNumber == 1) {
                    continue;
                }

                // Excel columns: A through AE, in the same order as the upload template.
                $fromCode = trim((string) ($row['A'] ?? '')); // From Code
                $fromName = trim((string) ($row['B'] ?? '')); // From Name
                $originRegion = trim((string) ($row['C'] ?? '')); // Origin Region
                $toName = trim((string) ($row['D'] ?? '')); // To Name
                $toCode = trim((string) ($row['E'] ?? '')); // To Code
                $destinationRegion = trim((string) ($row['F'] ?? '')); // Des Region
                $vendorCode = trim((string) ($row['G'] ?? '')); // Vendor Code
                $vendorName = trim((string) ($row['H'] ?? '')); // Vendor Name
                $mode = trim((string) ($row['I'] ?? '')); // Mode
                $truckCode = trim((string) ($row['J'] ?? '')); // Truck Code
                $truckDescription = trim((string) ($row['K'] ?? '')); // Truck Description
                $slab1 = trim((string) ($row['L'] ?? '')); // Slab 1
                $slab2 = trim((string) ($row['M'] ?? '')); // Slab 2
                $slab3 = trim((string) ($row['N'] ?? '')); // Slab 3
                $slab4 = trim((string) ($row['O'] ?? '')); // Slab 4
                $slab5 = trim((string) ($row['P'] ?? '')); // Slab 5
                $slab6 = trim((string) ($row['Q'] ?? '')); // Slab 6
                $slab7 = trim((string) ($row['R'] ?? '')); // Slab 7
                $slab8 = trim((string) ($row['S'] ?? '')); // Slab 8
                $slab9 = trim((string) ($row['T'] ?? '')); // Slab 9
                $slab10 = trim((string) ($row['U'] ?? '')); // Slab 10
                $slab1Rate = trim((string) ($row['V'] ?? '')); // Slab 1 Rate
                $slab2Rate = trim((string) ($row['W'] ?? '')); // Slab 2 Rate
                $slab3Rate = trim((string) ($row['X'] ?? '')); // Slab 3 Rate
                $slab4Rate = trim((string) ($row['Y'] ?? '')); // Slab 4 Rate
                $slab5Rate = trim((string) ($row['Z'] ?? '')); // Slab 5 Rate
                $slab6Rate = trim((string) ($row['AA'] ?? '')); // Slab 6 Rate
                $slab7Rate = trim((string) ($row['AB'] ?? '')); // Slab 7 Rate
                $slab8Rate = trim((string) ($row['AC'] ?? '')); // Slab 8 Rate
                $slab9Rate = trim((string) ($row['AD'] ?? '')); // Slab 9 Rate
                $slab10Rate = trim((string) ($row['AE'] ?? '')); // Slab 10 Rate

                if ($fromCode == '' && $toCode == '' && $vendorCode == '' && $truckCode == '') {
                    continue;
                }

                if ($fromCode == '') {
                    $errorRows[] = ['row' => $rowNumber, 'reason' => 'From Code is required'];
                    continue;
                }

                if ($toCode == '') {
                    $errorRows[] = ['row' => $rowNumber, 'reason' => 'To Code is required'];
                    continue;
                }

                if ($vendorCode == '') {
                    $errorRows[] = ['row' => $rowNumber, 'reason' => 'Vendor Code is required'];
                    continue;
                }

                if ($mode == '') {
                    $errorRows[] = ['row' => $rowNumber, 'reason' => 'Mode is required'];
                    continue;
                }

                if ($truckCode == '') {
                    $errorRows[] = ['row' => $rowNumber, 'reason' => 'Truck Code is required'];
                    continue;
                }

                if (!in_array(strtoupper($mode), ['PTL', 'AIR', 'ROAD', 'RAIL', 'SEA', 'COURIER', 'FTL'])) {
                    $errorRows[] = ['row' => $rowNumber, 'reason' => 'Invalid transport mode'];
                    continue;
                }

                $fromPlant = Siteplant::where('plant_site_code', $fromCode)->first();
                if (!$fromPlant) {
                    $errorRows[] = ['row' => $rowNumber, 'reason' => 'Invalid From Code: ' . $fromCode];
                    continue;
                }

                $toPlant = Siteplant::where('plant_site_code', $toCode)->first();
                if (!$toPlant) {
                    $errorRows[] = ['row' => $rowNumber, 'reason' => 'Invalid To Code: ' . $toCode];
                    continue;
                }

                $fromName = $fromPlant->plant_site_location_name ?? $fromPlant->plant_site_name ?? $fromName;
                $toName = $toPlant->plant_site_location_name ?? $toPlant->plant_site_name ?? $toName;
                $mode = strtoupper($mode);

                $invalidNumber = false;

                if ($slab1 !== '' && (!is_numeric($slab1) || $slab1 < 0)) {
                    $errorRows[] = ['row' => $rowNumber, 'reason' => 'Slab 1 must be a non-negative number'];
                    $invalidNumber = true;
                }

                if ($slab2 !== '' && (!is_numeric($slab2) || $slab2 < 0)) {
                    $errorRows[] = ['row' => $rowNumber, 'reason' => 'Slab 2 must be a non-negative number'];
                    $invalidNumber = true;
                }

                if ($slab3 !== '' && (!is_numeric($slab3) || $slab3 < 0)) {
                    $errorRows[] = ['row' => $rowNumber, 'reason' => 'Slab 3 must be a non-negative number'];
                    $invalidNumber = true;
                }

                if ($slab4 !== '' && (!is_numeric($slab4) || $slab4 < 0)) {
                    $errorRows[] = ['row' => $rowNumber, 'reason' => 'Slab 4 must be a non-negative number'];
                    $invalidNumber = true;
                }

                if ($slab5 !== '' && (!is_numeric($slab5) || $slab5 < 0)) {
                    $errorRows[] = ['row' => $rowNumber, 'reason' => 'Slab 5 must be a non-negative number'];
                    $invalidNumber = true;
                }

                if ($slab6 !== '' && (!is_numeric($slab6) || $slab6 < 0)) {
                    $errorRows[] = ['row' => $rowNumber, 'reason' => 'Slab 6 must be a non-negative number'];
                    $invalidNumber = true;
                }

                if ($slab7 !== '' && (!is_numeric($slab7) || $slab7 < 0)) {
                    $errorRows[] = ['row' => $rowNumber, 'reason' => 'Slab 7 must be a non-negative number'];
                    $invalidNumber = true;
                }

                if ($slab8 !== '' && (!is_numeric($slab8) || $slab8 < 0)) {
                    $errorRows[] = ['row' => $rowNumber, 'reason' => 'Slab 8 must be a non-negative number'];
                    $invalidNumber = true;
                }

                if ($slab9 !== '' && (!is_numeric($slab9) || $slab9 < 0)) {
                    $errorRows[] = ['row' => $rowNumber, 'reason' => 'Slab 9 must be a non-negative number'];
                    $invalidNumber = true;
                }

                if ($slab10 !== '' && (!is_numeric($slab10) || $slab10 < 0)) {
                    $errorRows[] = ['row' => $rowNumber, 'reason' => 'Slab 10 must be a non-negative number'];
                    $invalidNumber = true;
                }

                if ($slab1Rate !== '' && (!is_numeric($slab1Rate) || $slab1Rate < 0)) {
                    $errorRows[] = ['row' => $rowNumber, 'reason' => 'Slab 1 Rate must be a non-negative number'];
                    $invalidNumber = true;
                }

                if ($slab2Rate !== '' && (!is_numeric($slab2Rate) || $slab2Rate < 0)) {
                    $errorRows[] = ['row' => $rowNumber, 'reason' => 'Slab 2 Rate must be a non-negative number'];
                    $invalidNumber = true;
                }

                if ($slab3Rate !== '' && (!is_numeric($slab3Rate) || $slab3Rate < 0)) {
                    $errorRows[] = ['row' => $rowNumber, 'reason' => 'Slab 3 Rate must be a non-negative number'];
                    $invalidNumber = true;
                }

                if ($slab4Rate !== '' && (!is_numeric($slab4Rate) || $slab4Rate < 0)) {
                    $errorRows[] = ['row' => $rowNumber, 'reason' => 'Slab 4 Rate must be a non-negative number'];
                    $invalidNumber = true;
                }

                if ($slab5Rate !== '' && (!is_numeric($slab5Rate) || $slab5Rate < 0)) {
                    $errorRows[] = ['row' => $rowNumber, 'reason' => 'Slab 5 Rate must be a non-negative number'];
                    $invalidNumber = true;
                }

                if ($slab6Rate !== '' && (!is_numeric($slab6Rate) || $slab6Rate < 0)) {
                    $errorRows[] = ['row' => $rowNumber, 'reason' => 'Slab 6 Rate must be a non-negative number'];
                    $invalidNumber = true;
                }

                if ($slab7Rate !== '' && (!is_numeric($slab7Rate) || $slab7Rate < 0)) {
                    $errorRows[] = ['row' => $rowNumber, 'reason' => 'Slab 7 Rate must be a non-negative number'];
                    $invalidNumber = true;
                }

                if ($slab8Rate !== '' && (!is_numeric($slab8Rate) || $slab8Rate < 0)) {
                    $errorRows[] = ['row' => $rowNumber, 'reason' => 'Slab 8 Rate must be a non-negative number'];
                    $invalidNumber = true;
                }

                if ($slab9Rate !== '' && (!is_numeric($slab9Rate) || $slab9Rate < 0)) {
                    $errorRows[] = ['row' => $rowNumber, 'reason' => 'Slab 9 Rate must be a non-negative number'];
                    $invalidNumber = true;
                }

                if ($slab10Rate !== '' && (!is_numeric($slab10Rate) || $slab10Rate < 0)) {
                    $errorRows[] = ['row' => $rowNumber, 'reason' => 'Slab 10 Rate must be a non-negative number'];
                    $invalidNumber = true;
                }

                if ($invalidNumber) {
                    continue;
                }

                $duplicate = SlabMaster::where('from_code', $fromCode)
                    ->where('to_code', $toCode)
                    ->where('vendor_code', $vendorCode)
                    ->where('mode', $mode)
                    ->where('truck_code', $truckCode)
                    ->exists();

                if ($duplicate) {
                    $errorRows[] = ['row' => $rowNumber, 'reason' => 'Slab Master already exists for this lane, vendor, mode and truck'];
                    continue;
                }

                $data = [
                    'from_code' => $fromCode,
                    'from_name' => $fromName,
                    'origin_region' => $originRegion,
                    'to_name' => $toName,
                    'to_code' => $toCode,
                    'destination_region' => $destinationRegion,
                    'vendor_code' => $vendorCode,
                    'vendor_name' => $vendorName,
                    'mode' => $mode,
                    'truck_code' => $truckCode,
                    'truck_description' => $truckDescription,
                    'slab_1' => $slab1 === '' ? null : round((float) $slab1, 4),
                    'slab_2' => $slab2 === '' ? null : round((float) $slab2, 4),
                    'slab_3' => $slab3 === '' ? null : round((float) $slab3, 4),
                    'slab_4' => $slab4 === '' ? null : round((float) $slab4, 4),
                    'slab_5' => $slab5 === '' ? null : round((float) $slab5, 4),
                    'slab_6' => $slab6 === '' ? null : round((float) $slab6, 4),
                    'slab_7' => $slab7 === '' ? null : round((float) $slab7, 4),
                    'slab_8' => $slab8 === '' ? null : round((float) $slab8, 4),
                    'slab_9' => $slab9 === '' ? null : round((float) $slab9, 4),
                    'slab_10' => $slab10 === '' ? null : round((float) $slab10, 4),
                    'slab_1_rate' => $slab1Rate === '' ? null : round((float) $slab1Rate, 4),
                    'slab_2_rate' => $slab2Rate === '' ? null : round((float) $slab2Rate, 4),
                    'slab_3_rate' => $slab3Rate === '' ? null : round((float) $slab3Rate, 4),
                    'slab_4_rate' => $slab4Rate === '' ? null : round((float) $slab4Rate, 4),
                    'slab_5_rate' => $slab5Rate === '' ? null : round((float) $slab5Rate, 4),
                    'slab_6_rate' => $slab6Rate === '' ? null : round((float) $slab6Rate, 4),
                    'slab_7_rate' => $slab7Rate === '' ? null : round((float) $slab7Rate, 4),
                    'slab_8_rate' => $slab8Rate === '' ? null : round((float) $slab8Rate, 4),
                    'slab_9_rate' => $slab9Rate === '' ? null : round((float) $slab9Rate, 4),
                    'slab_10_rate' => $slab10Rate === '' ? null : round((float) $slab10Rate, 4),
                    'created_by' => $createdBy,
                    'created_at' => $createdDate,
                    'updated_at' => $createdDate,
                ];

                SlabMaster::create($data);
                $insertedCount++;
            }

            DB::commit();

            return redirect()->back()->with('success', $insertedCount . ' slab records imported successfully.')
                ->with('errorRows', $errorRows);
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    private function getFilteredSlabQuery(Request $request)
    {
        $query = SlabMaster::query();

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('from_code', 'like', '%' . $search . '%')
                    ->orWhere('to_code', 'like', '%' . $search . '%')
                    ->orWhere('vendor_code', 'like', '%' . $search . '%')
                    ->orWhere('truck_code', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('mode')) {
            $query->where('mode', $request->mode);
        }

        if ($request->filled('vendor_code')) {
            $query->where('vendor_code', $request->vendor_code);
        }

        return $query;
    }

    public function datalist(Request $request)
    {
        $title = 'Slab Master List';
        $pagetitle = $title;
        $perPage = (int) $request->get('per_page', 25);

        if (!in_array($perPage, [10, 25, 50, 100])) {
            $perPage = 25;
        }

        $datalist = $this->getFilteredSlabQuery($request)->orderBy('id', 'desc')->paginate($perPage);
        $datalist->appends($request->query());
        $userRole = Auth::user()->role_id ?? null;

        return view('admin.slab_master.list', compact('title', 'pagetitle', 'datalist', 'perPage', 'userRole'));
    }

    public function export(Request $request)
    {
        $records = $this->getFilteredSlabQuery($request)->orderBy('id')->get();
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'From Code');
        $sheet->setCellValue('B1', 'From Name');
        $sheet->setCellValue('C1', 'Origin Region');
        $sheet->setCellValue('D1', 'To Name');
        $sheet->setCellValue('E1', 'To Code');
        $sheet->setCellValue('F1', 'Des Region');
        $sheet->setCellValue('G1', 'Vendor Code');
        $sheet->setCellValue('H1', 'Vendor Name');
        $sheet->setCellValue('I1', 'Mode');
        $sheet->setCellValue('J1', 'Truck Code');
        $sheet->setCellValue('K1', 'Truck Description');
        $sheet->setCellValue('L1', 'Slab 1');
        $sheet->setCellValue('M1', 'Slab 2');
        $sheet->setCellValue('N1', 'Slab 3');
        $sheet->setCellValue('O1', 'Slab 4');
        $sheet->setCellValue('P1', 'Slab 5');
        $sheet->setCellValue('Q1', 'Slab 6');
        $sheet->setCellValue('R1', 'Slab 7');
        $sheet->setCellValue('S1', 'Slab 8');
        $sheet->setCellValue('T1', 'Slab 9');
        $sheet->setCellValue('U1', 'Slab 10');
        $sheet->setCellValue('V1', 'Slab 1 Rate');
        $sheet->setCellValue('W1', 'Slab 2 Rate');
        $sheet->setCellValue('X1', 'Slab 3 Rate');
        $sheet->setCellValue('Y1', 'Slab 4 Rate');
        $sheet->setCellValue('Z1', 'Slab 5 Rate');
        $sheet->setCellValue('AA1', 'Slab 6 Rate');
        $sheet->setCellValue('AB1', 'Slab 7 Rate');
        $sheet->setCellValue('AC1', 'Slab 8 Rate');
        $sheet->setCellValue('AD1', 'Slab 9 Rate');
        $sheet->setCellValue('AE1', 'Slab 10 Rate');

        $rowNumber = 2;
        foreach ($records as $record) {
            $sheet->setCellValue('A' . $rowNumber, $record->from_code);
            $sheet->setCellValue('B' . $rowNumber, $record->from_name);
            $sheet->setCellValue('C' . $rowNumber, $record->origin_region);
            $sheet->setCellValue('D' . $rowNumber, $record->to_name);
            $sheet->setCellValue('E' . $rowNumber, $record->to_code);
            $sheet->setCellValue('F' . $rowNumber, $record->destination_region);
            $sheet->setCellValue('G' . $rowNumber, $record->vendor_code);
            $sheet->setCellValue('H' . $rowNumber, $record->vendor_name);
            $sheet->setCellValue('I' . $rowNumber, $record->mode);
            $sheet->setCellValue('J' . $rowNumber, $record->truck_code);
            $sheet->setCellValue('K' . $rowNumber, $record->truck_description);
            $sheet->setCellValue('L' . $rowNumber, $record->slab_1);
            $sheet->setCellValue('M' . $rowNumber, $record->slab_2);
            $sheet->setCellValue('N' . $rowNumber, $record->slab_3);
            $sheet->setCellValue('O' . $rowNumber, $record->slab_4);
            $sheet->setCellValue('P' . $rowNumber, $record->slab_5);
            $sheet->setCellValue('Q' . $rowNumber, $record->slab_6);
            $sheet->setCellValue('R' . $rowNumber, $record->slab_7);
            $sheet->setCellValue('S' . $rowNumber, $record->slab_8);
            $sheet->setCellValue('T' . $rowNumber, $record->slab_9);
            $sheet->setCellValue('U' . $rowNumber, $record->slab_10);
            $sheet->setCellValue('V' . $rowNumber, $record->slab_1_rate);
            $sheet->setCellValue('W' . $rowNumber, $record->slab_2_rate);
            $sheet->setCellValue('X' . $rowNumber, $record->slab_3_rate);
            $sheet->setCellValue('Y' . $rowNumber, $record->slab_4_rate);
            $sheet->setCellValue('Z' . $rowNumber, $record->slab_5_rate);
            $sheet->setCellValue('AA' . $rowNumber, $record->slab_6_rate);
            $sheet->setCellValue('AB' . $rowNumber, $record->slab_7_rate);
            $sheet->setCellValue('AC' . $rowNumber, $record->slab_8_rate);
            $sheet->setCellValue('AD' . $rowNumber, $record->slab_9_rate);
            $sheet->setCellValue('AE' . $rowNumber, $record->slab_10_rate);
            $rowNumber++;
        }

        $sheet->getStyle('A1:AE1')->getFont()->setBold(true);
        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, 'slab_master.xlsx');
    }

    public function template()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'From Code');
        $sheet->setCellValue('B1', 'From Name');
        $sheet->setCellValue('C1', 'Origin Region');
        $sheet->setCellValue('D1', 'To Name');
        $sheet->setCellValue('E1', 'To Code');
        $sheet->setCellValue('F1', 'Des Region');
        $sheet->setCellValue('G1', 'Vendor Code');
        $sheet->setCellValue('H1', 'Vendor Name');
        $sheet->setCellValue('I1', 'Mode');
        $sheet->setCellValue('J1', 'Truck Code');
        $sheet->setCellValue('K1', 'Truck Description');
        $sheet->setCellValue('L1', 'Slab 1');
        $sheet->setCellValue('M1', 'Slab 2');
        $sheet->setCellValue('N1', 'Slab 3');
        $sheet->setCellValue('O1', 'Slab 4');
        $sheet->setCellValue('P1', 'Slab 5');
        $sheet->setCellValue('Q1', 'Slab 6');
        $sheet->setCellValue('R1', 'Slab 7');
        $sheet->setCellValue('S1', 'Slab 8');
        $sheet->setCellValue('T1', 'Slab 9');
        $sheet->setCellValue('U1', 'Slab 10');
        $sheet->setCellValue('V1', 'Slab 1 Rate');
        $sheet->setCellValue('W1', 'Slab 2 Rate');
        $sheet->setCellValue('X1', 'Slab 3 Rate');
        $sheet->setCellValue('Y1', 'Slab 4 Rate');
        $sheet->setCellValue('Z1', 'Slab 5 Rate');
        $sheet->setCellValue('AA1', 'Slab 6 Rate');
        $sheet->setCellValue('AB1', 'Slab 7 Rate');
        $sheet->setCellValue('AC1', 'Slab 8 Rate');
        $sheet->setCellValue('AD1', 'Slab 9 Rate');
        $sheet->setCellValue('AE1', 'Slab 10 Rate');
        $sheet->getStyle('A1:AE1')->getFont()->setBold(true);
        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, 'slab_master_template.xlsx');
    }

    public function manualupload()
    {
        $title = 'Slab Master Manual Upload';
        $pagetitle = $title;

        return view('admin.slab_master.manual', compact('title', 'pagetitle'));
    }

    public function save_manual_data(Request $request)
    {
        $createdBy = Auth::user()->id;
        $createdDate = now();
        $mode = strtoupper(trim((string) $request->input('mode', '')));

        if (!in_array($mode, ['PTL', 'AIR', 'ROAD', 'RAIL', 'SEA', 'COURIER', 'FTL'])) {
            return redirect()->back()->withInput()->with('error', 'Please select a valid Transport Mode.');
        }

        $fromCode = $request->input('from_code', []);
        $fromName = $request->input('from_name', []);
        $originRegion = $request->input('origin_region', []);
        $toName = $request->input('to_name', []);
        $toCode = $request->input('to_code', []);
        $destinationRegion = $request->input('destination_region', []);
        $vendorCode = $request->input('vendor_code', []);
        $vendorName = $request->input('vendor_name', []);
        $truckCode = $request->input('truck_code', []);
        $truckDescription = $request->input('truck_description', []);
        $slab1 = $request->input('slab_1', []);
        $slab2 = $request->input('slab_2', []);
        $slab3 = $request->input('slab_3', []);
        $slab4 = $request->input('slab_4', []);
        $slab5 = $request->input('slab_5', []);
        $slab6 = $request->input('slab_6', []);
        $slab7 = $request->input('slab_7', []);
        $slab8 = $request->input('slab_8', []);
        $slab9 = $request->input('slab_9', []);
        $slab10 = $request->input('slab_10', []);
        $slab1Rate = $request->input('slab_1_rate', []);
        $slab2Rate = $request->input('slab_2_rate', []);
        $slab3Rate = $request->input('slab_3_rate', []);
        $slab4Rate = $request->input('slab_4_rate', []);
        $slab5Rate = $request->input('slab_5_rate', []);
        $slab6Rate = $request->input('slab_6_rate', []);
        $slab7Rate = $request->input('slab_7_rate', []);
        $slab8Rate = $request->input('slab_8_rate', []);
        $slab9Rate = $request->input('slab_9_rate', []);
        $slab10Rate = $request->input('slab_10_rate', []);

        $count = count($fromCode);
        $insertedCount = 0;
        $errorRows = [];

        try {
            DB::beginTransaction();

            for ($i = 0; $i < $count; $i++) {
                $rowNumber = $i + 1;

                if (trim((string) ($fromCode[$i] ?? '')) == '' && trim((string) ($toCode[$i] ?? '')) == '' && trim((string) ($vendorCode[$i] ?? '')) == '' && trim((string) ($truckCode[$i] ?? '')) == '') {
                    continue;
                }

                if (trim((string) ($fromCode[$i] ?? '')) == '') {
                    $errorRows[] = ['row' => $rowNumber, 'reason' => 'From Code is required'];
                    continue;
                }

                if (trim((string) ($toCode[$i] ?? '')) == '') {
                    $errorRows[] = ['row' => $rowNumber, 'reason' => 'To Code is required'];
                    continue;
                }

                if (trim((string) ($vendorCode[$i] ?? '')) == '') {
                    $errorRows[] = ['row' => $rowNumber, 'reason' => 'Vendor Code is required'];
                    continue;
                }

                if (trim((string) ($truckCode[$i] ?? '')) == '') {
                    $errorRows[] = ['row' => $rowNumber, 'reason' => 'Truck Code is required'];
                    continue;
                }

                $fromPlant = Siteplant::where('plant_site_code', trim($fromCode[$i]))->first();
                if (!$fromPlant) {
                    $errorRows[] = ['row' => $rowNumber, 'reason' => 'Invalid From Code: ' . $fromCode[$i]];
                    continue;
                }

                $toPlant = Siteplant::where('plant_site_code', trim($toCode[$i]))->first();
                if (!$toPlant) {
                    $errorRows[] = ['row' => $rowNumber, 'reason' => 'Invalid To Code: ' . $toCode[$i]];
                    continue;
                }

                $invalidNumber = false;

                if (trim((string) ($slab1[$i] ?? '')) !== '' && (!is_numeric($slab1[$i]) || $slab1[$i] < 0)) {
                    $errorRows[] = ['row' => $rowNumber, 'reason' => 'Slab 1 must be a non-negative number'];
                    $invalidNumber = true;
                }

                if (trim((string) ($slab2[$i] ?? '')) !== '' && (!is_numeric($slab2[$i]) || $slab2[$i] < 0)) {
                    $errorRows[] = ['row' => $rowNumber, 'reason' => 'Slab 2 must be a non-negative number'];
                    $invalidNumber = true;
                }

                if (trim((string) ($slab3[$i] ?? '')) !== '' && (!is_numeric($slab3[$i]) || $slab3[$i] < 0)) {
                    $errorRows[] = ['row' => $rowNumber, 'reason' => 'Slab 3 must be a non-negative number'];
                    $invalidNumber = true;
                }

                if (trim((string) ($slab4[$i] ?? '')) !== '' && (!is_numeric($slab4[$i]) || $slab4[$i] < 0)) {
                    $errorRows[] = ['row' => $rowNumber, 'reason' => 'Slab 4 must be a non-negative number'];
                    $invalidNumber = true;
                }

                if (trim((string) ($slab5[$i] ?? '')) !== '' && (!is_numeric($slab5[$i]) || $slab5[$i] < 0)) {
                    $errorRows[] = ['row' => $rowNumber, 'reason' => 'Slab 5 must be a non-negative number'];
                    $invalidNumber = true;
                }

                if (trim((string) ($slab6[$i] ?? '')) !== '' && (!is_numeric($slab6[$i]) || $slab6[$i] < 0)) {
                    $errorRows[] = ['row' => $rowNumber, 'reason' => 'Slab 6 must be a non-negative number'];
                    $invalidNumber = true;
                }

                if (trim((string) ($slab7[$i] ?? '')) !== '' && (!is_numeric($slab7[$i]) || $slab7[$i] < 0)) {
                    $errorRows[] = ['row' => $rowNumber, 'reason' => 'Slab 7 must be a non-negative number'];
                    $invalidNumber = true;
                }

                if (trim((string) ($slab8[$i] ?? '')) !== '' && (!is_numeric($slab8[$i]) || $slab8[$i] < 0)) {
                    $errorRows[] = ['row' => $rowNumber, 'reason' => 'Slab 8 must be a non-negative number'];
                    $invalidNumber = true;
                }

                if (trim((string) ($slab9[$i] ?? '')) !== '' && (!is_numeric($slab9[$i]) || $slab9[$i] < 0)) {
                    $errorRows[] = ['row' => $rowNumber, 'reason' => 'Slab 9 must be a non-negative number'];
                    $invalidNumber = true;
                }

                if (trim((string) ($slab10[$i] ?? '')) !== '' && (!is_numeric($slab10[$i]) || $slab10[$i] < 0)) {
                    $errorRows[] = ['row' => $rowNumber, 'reason' => 'Slab 10 must be a non-negative number'];
                    $invalidNumber = true;
                }

                if (trim((string) ($slab1Rate[$i] ?? '')) !== '' && (!is_numeric($slab1Rate[$i]) || $slab1Rate[$i] < 0)) {
                    $errorRows[] = ['row' => $rowNumber, 'reason' => 'Slab 1 Rate must be a non-negative number'];
                    $invalidNumber = true;
                }

                if (trim((string) ($slab2Rate[$i] ?? '')) !== '' && (!is_numeric($slab2Rate[$i]) || $slab2Rate[$i] < 0)) {
                    $errorRows[] = ['row' => $rowNumber, 'reason' => 'Slab 2 Rate must be a non-negative number'];
                    $invalidNumber = true;
                }

                if (trim((string) ($slab3Rate[$i] ?? '')) !== '' && (!is_numeric($slab3Rate[$i]) || $slab3Rate[$i] < 0)) {
                    $errorRows[] = ['row' => $rowNumber, 'reason' => 'Slab 3 Rate must be a non-negative number'];
                    $invalidNumber = true;
                }

                if (trim((string) ($slab4Rate[$i] ?? '')) !== '' && (!is_numeric($slab4Rate[$i]) || $slab4Rate[$i] < 0)) {
                    $errorRows[] = ['row' => $rowNumber, 'reason' => 'Slab 4 Rate must be a non-negative number'];
                    $invalidNumber = true;
                }

                if (trim((string) ($slab5Rate[$i] ?? '')) !== '' && (!is_numeric($slab5Rate[$i]) || $slab5Rate[$i] < 0)) {
                    $errorRows[] = ['row' => $rowNumber, 'reason' => 'Slab 5 Rate must be a non-negative number'];
                    $invalidNumber = true;
                }

                if (trim((string) ($slab6Rate[$i] ?? '')) !== '' && (!is_numeric($slab6Rate[$i]) || $slab6Rate[$i] < 0)) {
                    $errorRows[] = ['row' => $rowNumber, 'reason' => 'Slab 6 Rate must be a non-negative number'];
                    $invalidNumber = true;
                }

                if (trim((string) ($slab7Rate[$i] ?? '')) !== '' && (!is_numeric($slab7Rate[$i]) || $slab7Rate[$i] < 0)) {
                    $errorRows[] = ['row' => $rowNumber, 'reason' => 'Slab 7 Rate must be a non-negative number'];
                    $invalidNumber = true;
                }

                if (trim((string) ($slab8Rate[$i] ?? '')) !== '' && (!is_numeric($slab8Rate[$i]) || $slab8Rate[$i] < 0)) {
                    $errorRows[] = ['row' => $rowNumber, 'reason' => 'Slab 8 Rate must be a non-negative number'];
                    $invalidNumber = true;
                }

                if (trim((string) ($slab9Rate[$i] ?? '')) !== '' && (!is_numeric($slab9Rate[$i]) || $slab9Rate[$i] < 0)) {
                    $errorRows[] = ['row' => $rowNumber, 'reason' => 'Slab 9 Rate must be a non-negative number'];
                    $invalidNumber = true;
                }

                if (trim((string) ($slab10Rate[$i] ?? '')) !== '' && (!is_numeric($slab10Rate[$i]) || $slab10Rate[$i] < 0)) {
                    $errorRows[] = ['row' => $rowNumber, 'reason' => 'Slab 10 Rate must be a non-negative number'];
                    $invalidNumber = true;
                }

                if ($invalidNumber) {
                    continue;
                }

                $duplicate = SlabMaster::where('from_code', trim($fromCode[$i]))
                    ->where('to_code', trim($toCode[$i]))
                    ->where('vendor_code', trim($vendorCode[$i]))
                    ->where('mode', $mode)
                    ->where('truck_code', trim($truckCode[$i]))
                    ->exists();

                if ($duplicate) {
                    $errorRows[] = ['row' => $rowNumber, 'reason' => 'Duplicate slab record for this lane, vendor, mode and truck'];
                    continue;
                }

                $data = [
                    'from_code' => trim((string) ($fromCode[$i] ?? '')),
                    'from_name' => ($fromPlant->plant_site_location_name ?? $fromPlant->plant_site_name ?? ''),
                    'origin_region' => trim((string) ($originRegion[$i] ?? '')),
                    'to_name' => ($toPlant->plant_site_location_name ?? $toPlant->plant_site_name ?? ''),
                    'to_code' => trim((string) ($toCode[$i] ?? '')),
                    'destination_region' => trim((string) ($destinationRegion[$i] ?? '')),
                    'vendor_code' => trim((string) ($vendorCode[$i] ?? '')),
                    'vendor_name' => trim((string) ($vendorName[$i] ?? '')),
                    'mode' => $mode,
                    'truck_code' => trim((string) ($truckCode[$i] ?? '')),
                    'truck_description' => trim((string) ($truckDescription[$i] ?? '')),
                    'slab_1' => trim((string) ($slab1[$i] ?? '')) === '' ? null : round((float) $slab1[$i], 4),
                    'slab_2' => trim((string) ($slab2[$i] ?? '')) === '' ? null : round((float) $slab2[$i], 4),
                    'slab_3' => trim((string) ($slab3[$i] ?? '')) === '' ? null : round((float) $slab3[$i], 4),
                    'slab_4' => trim((string) ($slab4[$i] ?? '')) === '' ? null : round((float) $slab4[$i], 4),
                    'slab_5' => trim((string) ($slab5[$i] ?? '')) === '' ? null : round((float) $slab5[$i], 4),
                    'slab_6' => trim((string) ($slab6[$i] ?? '')) === '' ? null : round((float) $slab6[$i], 4),
                    'slab_7' => trim((string) ($slab7[$i] ?? '')) === '' ? null : round((float) $slab7[$i], 4),
                    'slab_8' => trim((string) ($slab8[$i] ?? '')) === '' ? null : round((float) $slab8[$i], 4),
                    'slab_9' => trim((string) ($slab9[$i] ?? '')) === '' ? null : round((float) $slab9[$i], 4),
                    'slab_10' => trim((string) ($slab10[$i] ?? '')) === '' ? null : round((float) $slab10[$i], 4),
                    'slab_1_rate' => trim((string) ($slab1Rate[$i] ?? '')) === '' ? null : round((float) $slab1Rate[$i], 4),
                    'slab_2_rate' => trim((string) ($slab2Rate[$i] ?? '')) === '' ? null : round((float) $slab2Rate[$i], 4),
                    'slab_3_rate' => trim((string) ($slab3Rate[$i] ?? '')) === '' ? null : round((float) $slab3Rate[$i], 4),
                    'slab_4_rate' => trim((string) ($slab4Rate[$i] ?? '')) === '' ? null : round((float) $slab4Rate[$i], 4),
                    'slab_5_rate' => trim((string) ($slab5Rate[$i] ?? '')) === '' ? null : round((float) $slab5Rate[$i], 4),
                    'slab_6_rate' => trim((string) ($slab6Rate[$i] ?? '')) === '' ? null : round((float) $slab6Rate[$i], 4),
                    'slab_7_rate' => trim((string) ($slab7Rate[$i] ?? '')) === '' ? null : round((float) $slab7Rate[$i], 4),
                    'slab_8_rate' => trim((string) ($slab8Rate[$i] ?? '')) === '' ? null : round((float) $slab8Rate[$i], 4),
                    'slab_9_rate' => trim((string) ($slab9Rate[$i] ?? '')) === '' ? null : round((float) $slab9Rate[$i], 4),
                    'slab_10_rate' => trim((string) ($slab10Rate[$i] ?? '')) === '' ? null : round((float) $slab10Rate[$i], 4),
                    'created_by' => $createdBy,
                    'created_at' => $createdDate,
                    'updated_at' => $createdDate,
                ];

                SlabMaster::create($data);
                $insertedCount++;
            }

            DB::commit();

            return redirect()->back()->with('success', $insertedCount . ' slab records saved successfully.')
                ->with('errorRows', $errorRows);
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function fetchPlantData(Request $request)
    {
        $plantCode = trim((string) $request->input('plant_code', ''));
        if ($plantCode == '') {
            return response()->json(['error' => 'Plant Code is required'], 422);
        }

        $plant = Siteplant::where('plant_site_code', $plantCode)->first();
        if (!$plant) {
            return response()->json(['error' => 'Invalid Plant Code: ' . $plantCode], 404);
        }

        return response()->json([
            'plant_name' => $plant->plant_site_location_name ?? $plant->plant_site_name ?? '',
        ]);
    }

    public function edit($id)
    {
        $title = 'Edit Slab Master';
        $pagetitle = $title;
        $rate = SlabMaster::findOrFail($id);

        return view('admin.slab_master.edit', compact('title', 'pagetitle', 'rate'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'from_code' => 'required|string|max:20',
            'to_code' => 'required|string|max:20',
            'vendor_code' => 'required|string|max:20',
            'truck_code' => 'required|string|max:20',
            'mode' => 'required|in:PTL,AIR,ROAD,RAIL,SEA,COURIER,FTL',
            'slab_1' => 'nullable|numeric|min:0',
            'slab_2' => 'nullable|numeric|min:0',
            'slab_3' => 'nullable|numeric|min:0',
            'slab_4' => 'nullable|numeric|min:0',
            'slab_5' => 'nullable|numeric|min:0',
            'slab_6' => 'nullable|numeric|min:0',
            'slab_7' => 'nullable|numeric|min:0',
            'slab_8' => 'nullable|numeric|min:0',
            'slab_9' => 'nullable|numeric|min:0',
            'slab_10' => 'nullable|numeric|min:0',
            'slab_1_rate' => 'nullable|numeric|min:0',
            'slab_2_rate' => 'nullable|numeric|min:0',
            'slab_3_rate' => 'nullable|numeric|min:0',
            'slab_4_rate' => 'nullable|numeric|min:0',
            'slab_5_rate' => 'nullable|numeric|min:0',
            'slab_6_rate' => 'nullable|numeric|min:0',
            'slab_7_rate' => 'nullable|numeric|min:0',
            'slab_8_rate' => 'nullable|numeric|min:0',
            'slab_9_rate' => 'nullable|numeric|min:0',
            'slab_10_rate' => 'nullable|numeric|min:0',
        ]);

        $rate = SlabMaster::findOrFail($id);
        $fromPlant = Siteplant::where('plant_site_code', trim($request->from_code))->first();
        $toPlant = Siteplant::where('plant_site_code', trim($request->to_code))->first();

        if (!$fromPlant || !$toPlant) {
            return redirect()->back()->withInput()->with('error', 'Invalid From Code or To Code.');
        }

        $duplicate = SlabMaster::where('from_code', trim($request->from_code))
            ->where('to_code', trim($request->to_code))
            ->where('vendor_code', trim($request->vendor_code))
            ->where('mode', $request->mode)
            ->where('truck_code', trim($request->truck_code))
            ->where('id', '!=', $id)
            ->exists();

        if ($duplicate) {
            return redirect()->back()->withInput()->with('error', 'Duplicate slab record for this lane, vendor, mode and truck.');
        }

        $rate->from_code = $request->from_code;
        $rate->from_name = $fromPlant->plant_site_location_name ?? $fromPlant->plant_site_name ?? '';
        $rate->origin_region = $request->origin_region;
        $rate->to_name = $toPlant->plant_site_location_name ?? $toPlant->plant_site_name ?? '';
        $rate->to_code = $request->to_code;
        $rate->destination_region = $request->destination_region;
        $rate->vendor_code = $request->vendor_code;
        $rate->vendor_name = $request->vendor_name;
        $rate->mode = $request->mode;
        $rate->truck_code = $request->truck_code;
        $rate->truck_description = $request->truck_description;
        $rate->slab_1 = $request->filled('slab_1') ? round((float) $request->slab_1, 4) : null;
        $rate->slab_2 = $request->filled('slab_2') ? round((float) $request->slab_2, 4) : null;
        $rate->slab_3 = $request->filled('slab_3') ? round((float) $request->slab_3, 4) : null;
        $rate->slab_4 = $request->filled('slab_4') ? round((float) $request->slab_4, 4) : null;
        $rate->slab_5 = $request->filled('slab_5') ? round((float) $request->slab_5, 4) : null;
        $rate->slab_6 = $request->filled('slab_6') ? round((float) $request->slab_6, 4) : null;
        $rate->slab_7 = $request->filled('slab_7') ? round((float) $request->slab_7, 4) : null;
        $rate->slab_8 = $request->filled('slab_8') ? round((float) $request->slab_8, 4) : null;
        $rate->slab_9 = $request->filled('slab_9') ? round((float) $request->slab_9, 4) : null;
        $rate->slab_10 = $request->filled('slab_10') ? round((float) $request->slab_10, 4) : null;
        $rate->slab_1_rate = $request->filled('slab_1_rate') ? round((float) $request->slab_1_rate, 4) : null;
        $rate->slab_2_rate = $request->filled('slab_2_rate') ? round((float) $request->slab_2_rate, 4) : null;
        $rate->slab_3_rate = $request->filled('slab_3_rate') ? round((float) $request->slab_3_rate, 4) : null;
        $rate->slab_4_rate = $request->filled('slab_4_rate') ? round((float) $request->slab_4_rate, 4) : null;
        $rate->slab_5_rate = $request->filled('slab_5_rate') ? round((float) $request->slab_5_rate, 4) : null;
        $rate->slab_6_rate = $request->filled('slab_6_rate') ? round((float) $request->slab_6_rate, 4) : null;
        $rate->slab_7_rate = $request->filled('slab_7_rate') ? round((float) $request->slab_7_rate, 4) : null;
        $rate->slab_8_rate = $request->filled('slab_8_rate') ? round((float) $request->slab_8_rate, 4) : null;
        $rate->slab_9_rate = $request->filled('slab_9_rate') ? round((float) $request->slab_9_rate, 4) : null;
        $rate->slab_10_rate = $request->filled('slab_10_rate') ? round((float) $request->slab_10_rate, 4) : null;
        $rate->save();

        return redirect()->route('admin.slab-master.list')->with('success', 'Slab Master updated successfully.');
    }

    public function delete(Request $request, $id)
    {
        $rate = SlabMaster::findOrFail($id);
        $rate->delete();

        return response()->json(['status' => true, 'message' => 'Slab Master deleted successfully.']);
    }
}
