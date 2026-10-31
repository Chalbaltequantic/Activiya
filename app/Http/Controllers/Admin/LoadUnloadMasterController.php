<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LoadUnloadMaster;
use App\Models\Siteplant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class LoadUnloadMasterController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index(Request $request)
    {
        $title = 'Load/Unload Master Upload';
        $pagetitle = $title;

        return view('admin.load_unload_master.index', compact('title', 'pagetitle'));
    }

    public function import(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:xls,xlsx',
        ]);

        $createdBy = Auth::guard('admin')->id();
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

                // XLS mapping: A through O. Keep column order identical to the template.
                $fromCode = trim((string) ($row['A'] ?? '')); // From Code
                $fromName = trim((string) ($row['B'] ?? '')); // From Name
                $originRegion = trim((string) ($row['C'] ?? '')); // Origin Region
                $toName = trim((string) ($row['D'] ?? '')); // To Name
                $toCode = trim((string) ($row['E'] ?? '')); // To Code
                $destinationRegion = trim((string) ($row['F'] ?? '')); // Destination Region
                $vendorCode = trim((string) ($row['G'] ?? '')); // Vendor Code
                $vendorName = trim((string) ($row['H'] ?? '')); // Vendor Name
                $mode = trim((string) ($row['I'] ?? '')); // Mode
                $truckCode = trim((string) ($row['J'] ?? '')); // Truck Code
                $truckDescription = trim((string) ($row['K'] ?? '')); // Truck Description
                $chargeType = trim((string) ($row['L'] ?? '')); // Charge Type (Load/Unload)
                $rateType = trim((string) ($row['M'] ?? '')); // Rate Type (Fix/Per Case/KG)
                $rate = trim((string) ($row['N'] ?? '')); // Rate
                $loadUnloadLimit = trim((string) ($row['O'] ?? '')); // Load/Unload Limit

                if ($fromCode === '' && $toCode === '' && $vendorCode === '' && $truckCode === '') {
                    continue;
                }

                if ($fromCode === '') {
                    $errorRows[] = ['row' => $rowNumber, 'reason' => 'From Code is required.'];
                    continue;
                }

                if ($toCode === '') {
                    $errorRows[] = ['row' => $rowNumber, 'reason' => 'To Code is required.'];
                    continue;
                }

                if ($vendorCode === '') {
                    $errorRows[] = ['row' => $rowNumber, 'reason' => 'Vendor Code is required.'];
                    continue;
                }

                if ($mode === '') {
                    $errorRows[] = ['row' => $rowNumber, 'reason' => 'Mode is required.'];
                    continue;
                }

                if ($truckCode === '') {
                    $errorRows[] = ['row' => $rowNumber, 'reason' => 'Truck Code is required.'];
                    continue;
                }

                if ($chargeType === '') {
                    $errorRows[] = ['row' => $rowNumber, 'reason' => 'Charge Type (Load/Unload) is required.'];
                    continue;
                }

                if ($rateType === '') {
                    $errorRows[] = ['row' => $rowNumber, 'reason' => 'Rate Type (Fix/Per Case/KG) is required.'];
                    continue;
                }

                if ($rate === '') {
                    $errorRows[] = ['row' => $rowNumber, 'reason' => 'Rate is required.'];
                    continue;
                }

                if (!in_array(strtoupper($mode), ['FTL', 'PTL', 'AIR', 'ROAD', 'RAIL', 'SEA', 'COURIER'], true)) {
                    $errorRows[] = ['row' => $rowNumber, 'reason' => 'Invalid Mode.'];
                    continue;
                }

                $mode = strtoupper($mode);
                $chargeType = ucfirst(strtolower($chargeType));
                $rateType = strtolower($rateType) === 'fix' ? 'Fix' : (strtolower($rateType) === 'per case' ? 'Per Case' : (strtolower($rateType) === 'kg' ? 'KG' : $rateType));

                if (!in_array($chargeType, ['Load', 'Unload'], true) || !in_array($rateType, ['Fix', 'Per Case', 'KG'], true)) {
                    $errorRows[] = ['row' => $rowNumber, 'reason' => 'Invalid Charge Type or Rate Type.'];
                    continue;
                }

                if (!is_numeric($rate) || (float) $rate < 0 || ($loadUnloadLimit !== '' && (!is_numeric($loadUnloadLimit) || (float) $loadUnloadLimit < 0))) {
                    $errorRows[] = ['row' => $rowNumber, 'reason' => 'Rate and Load/Unload Limit must be non-negative numbers.'];
                    continue;
                }

                $fromPlant = Siteplant::where('plant_site_code', $fromCode)->first();
                $toPlant = Siteplant::where('plant_site_code', $toCode)->first();
                $vendor = DB::table('vendors')->where('vendor_code', $vendorCode)->first();
                $truck = DB::table('truck_master')->where('code', $truckCode)->first();

                if (!$fromPlant || !$toPlant || !$vendor || !$truck) {
                    $errorRows[] = ['row' => $rowNumber, 'reason' => 'From Code, To Code, Vendor Code or Truck Code was not found in its master.'];
                    continue;
                }

                $fromName = $fromPlant->plant_site_location_name ?: ($fromPlant->plant_site_name ?? '');
                $toName = $toPlant->plant_site_location_name ?: ($toPlant->plant_site_name ?? '');
                $vendorName = $vendor->vendor_name;
                $truckDescription = $truck->description;

                $exists = LoadUnloadMaster::where('from_code', $fromCode)
                    ->where('to_code', $toCode)
                    ->where('vendor_code', $vendorCode)
                    ->where('mode', $mode)
                    ->where('truck_code', $truckCode)
                    ->where('charge_type', $chargeType)
                    ->where('rate_type', $rateType)
                    ->exists();

                if ($exists) {
                    $errorRows[] = ['row' => $rowNumber, 'reason' => 'Duplicate load/unload rate for the same lane, vendor, mode, truck, charge and rate type.'];
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
                    'charge_type' => $chargeType,
                    'rate_type' => $rateType,
                    'rate' => round((float) $rate, 4),
                    'load_unload_limit' => ($loadUnloadLimit === '' ? null : round((float) $loadUnloadLimit, 4)),
                    'created_by' => $createdBy,
                ];

                LoadUnloadMaster::create($data);
                $insertedCount++;
            }

            DB::commit();

            return redirect()->route('admin.load-unload-master.index')
                ->with('success', $insertedCount . ' load/unload rate(s) imported successfully.')
                ->with('errorRows', $errorRows);
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    private function getFilteredLoadUnloadQuery(Request $request)
    {
        $query = LoadUnloadMaster::query();

        if ($request->filled('mode')) {
            $query->where('mode', $request->mode);
        }

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('from_code', 'like', '%' . $search . '%')
                    ->orWhere('to_code', 'like', '%' . $search . '%')
                    ->orWhere('vendor_code', 'like', '%' . $search . '%')
                    ->orWhere('vendor_name', 'like', '%' . $search . '%')
                    ->orWhere('truck_code', 'like', '%' . $search . '%')
                    ->orWhere('truck_description', 'like', '%' . $search . '%');
            });
        }

        return $query;
    }

    public function datalist(Request $request)
    {
        $title = 'Load/Unload Master List';
        $pagetitle = $title;
        $perPage = min(max((int) $request->input('per_page', 25), 1), 100);
        $datalist = $this->getFilteredLoadUnloadQuery($request)->orderByDesc('id')->paginate($perPage)->withQueryString();

        return view('admin.load_unload_master.list', compact('title', 'pagetitle', 'datalist'));
    }

    public function export(Request $request)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Load Unload Master');

        $sheet->setCellValue('A1', 'From Code');
        $sheet->setCellValue('B1', 'From Name');
        $sheet->setCellValue('C1', 'Origin Region');
        $sheet->setCellValue('D1', 'To Name');
        $sheet->setCellValue('E1', 'To Code');
        $sheet->setCellValue('F1', 'Destination Region');
        $sheet->setCellValue('G1', 'Vendor Code');
        $sheet->setCellValue('H1', 'Vendor Name');
        $sheet->setCellValue('I1', 'Mode');
        $sheet->setCellValue('J1', 'Truck Code');
        $sheet->setCellValue('K1', 'Truck Description');
        $sheet->setCellValue('L1', 'Charge Type (Load/Unload)');
        $sheet->setCellValue('M1', 'Rate Type (Fix/Per Case/KG)');
        $sheet->setCellValue('N1', 'Rate');
        $sheet->setCellValue('O1', 'Load/Unload Limit');

        $rowNumber = 2;
        $records = $this->getFilteredLoadUnloadQuery($request)->orderBy('id')->cursor();

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
            $sheet->setCellValue('L' . $rowNumber, $record->charge_type);
            $sheet->setCellValue('M' . $rowNumber, $record->rate_type);
            $sheet->setCellValue('N' . $rowNumber, $record->rate);
            $sheet->setCellValue('O' . $rowNumber, $record->load_unload_limit);
            $rowNumber++;
        }

        $sheet->getStyle('A1:O1')->getFont()->setBold(true);
        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, 'load_unload_master.xlsx');
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
        $sheet->setCellValue('F1', 'Destination Region');
        $sheet->setCellValue('G1', 'Vendor Code');
        $sheet->setCellValue('H1', 'Vendor Name');
        $sheet->setCellValue('I1', 'Mode');
        $sheet->setCellValue('J1', 'Truck Code');
        $sheet->setCellValue('K1', 'Truck Description');
        $sheet->setCellValue('L1', 'Charge Type (Load/Unload)');
        $sheet->setCellValue('M1', 'Rate Type (Fix/Per Case/KG)');
        $sheet->setCellValue('N1', 'Rate');
        $sheet->setCellValue('O1', 'Load/Unload Limit');

        $sheet->getStyle('A1:O1')->getFont()->setBold(true);
        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, 'load_unload_master_template.xlsx');
    }

    public function manualupload()
    {
        $title = 'Load/Unload Master Manual Upload';
        $pagetitle = $title;

        return view('admin.load_unload_master.manual', compact('title', 'pagetitle'));
    }

    public function save_manual_data(Request $request)
    {
        $request->validate([
            'mode' => 'required|in:FTL,PTL,AIR,ROAD,RAIL,SEA,COURIER',
        ]);

        $mode = $request->input('mode');
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
        $chargeType = $request->input('charge_type', []);
        $rateType = $request->input('rate_type', []);
        $rate = $request->input('rate', []);
        $loadUnloadLimit = $request->input('load_unload_limit', []);

        $count = count($fromCode);
        $createdBy = Auth::guard('admin')->id();
        $insertedCount = 0;
        $errorRows = [];

        try {
            DB::beginTransaction();

            for ($i = 0; $i < $count; $i++) {
                $fromCodeValue = trim((string) ($fromCode[$i] ?? ''));
                $fromNameValue = trim((string) ($fromName[$i] ?? ''));
                $originRegionValue = trim((string) ($originRegion[$i] ?? ''));
                $toNameValue = trim((string) ($toName[$i] ?? ''));
                $toCodeValue = trim((string) ($toCode[$i] ?? ''));
                $destinationRegionValue = trim((string) ($destinationRegion[$i] ?? ''));
                $vendorCodeValue = trim((string) ($vendorCode[$i] ?? ''));
                $vendorNameValue = trim((string) ($vendorName[$i] ?? ''));
                $truckCodeValue = trim((string) ($truckCode[$i] ?? ''));
                $truckDescriptionValue = trim((string) ($truckDescription[$i] ?? ''));
                $chargeTypeValue = trim((string) ($chargeType[$i] ?? ''));
                $rateTypeValue = trim((string) ($rateType[$i] ?? ''));
                $rateValue = trim((string) ($rate[$i] ?? ''));
                $loadUnloadLimitValue = trim((string) ($loadUnloadLimit[$i] ?? ''));

                if ($fromCodeValue === '' && $toCodeValue === '' && $vendorCodeValue === '' && $truckCodeValue === '') {
                    continue;
                }

                if ($fromCodeValue === '') {
                    $errorRows[] = ['row' => $i + 1, 'reason' => 'From Code is required.'];
                    continue;
                }

                if ($toCodeValue === '') {
                    $errorRows[] = ['row' => $i + 1, 'reason' => 'To Code is required.'];
                    continue;
                }

                if ($vendorCodeValue === '') {
                    $errorRows[] = ['row' => $i + 1, 'reason' => 'Vendor Code is required.'];
                    continue;
                }

                if ($truckCodeValue === '') {
                    $errorRows[] = ['row' => $i + 1, 'reason' => 'Truck Code is required.'];
                    continue;
                }

                if ($chargeTypeValue === '') {
                    $errorRows[] = ['row' => $i + 1, 'reason' => 'Charge Type (Load/Unload) is required.'];
                    continue;
                }

                if ($rateTypeValue === '') {
                    $errorRows[] = ['row' => $i + 1, 'reason' => 'Rate Type (Fix/Per Case/KG) is required.'];
                    continue;
                }

                if ($rateValue === '') {
                    $errorRows[] = ['row' => $i + 1, 'reason' => 'Rate is required.'];
                    continue;
                }

                if (!in_array($chargeTypeValue, ['Load', 'Unload'], true) || !in_array($rateTypeValue, ['Fix', 'Per Case', 'KG'], true)) {
                    $errorRows[] = ['row' => $i + 1, 'reason' => 'Invalid Charge Type or Rate Type.'];
                    continue;
                }

                if (!is_numeric($rateValue) || (float) $rateValue < 0 || ($loadUnloadLimitValue !== '' && (!is_numeric($loadUnloadLimitValue) || (float) $loadUnloadLimitValue < 0))) {
                    $errorRows[] = ['row' => $i + 1, 'reason' => 'Rate and Load/Unload Limit must be non-negative numbers.'];
                    continue;
                }

                $fromPlant = Siteplant::where('plant_site_code', $fromCodeValue)->first();
                $toPlant = Siteplant::where('plant_site_code', $toCodeValue)->first();
                $vendor = DB::table('vendors')->where('vendor_code', $vendorCodeValue)->first();
                $truck = DB::table('truck_master')->where('code', $truckCodeValue)->first();

                if (!$fromPlant || !$toPlant || !$vendor || !$truck) {
                    $errorRows[] = ['row' => $i + 1, 'reason' => 'Invalid From Code, To Code, Vendor Code or Truck Code.'];
                    continue;
                }

                $exists = LoadUnloadMaster::where('from_code', $fromCodeValue)
                    ->where('to_code', $toCodeValue)
                    ->where('vendor_code', $vendorCodeValue)
                    ->where('mode', $mode)
                    ->where('truck_code', $truckCodeValue)
                    ->where('charge_type', $chargeTypeValue)
                    ->where('rate_type', $rateTypeValue)
                    ->exists();

                if ($exists) {
                    $errorRows[] = ['row' => $i + 1, 'reason' => 'Duplicate load/unload rate.'];
                    continue;
                }

                $data = [
                    'from_code' => $fromCodeValue,
                    'from_name' => ($fromPlant->plant_site_location_name ?: ($fromPlant->plant_site_name ?? '')),
                    'origin_region' => $originRegionValue,
                    'to_name' => ($toPlant->plant_site_location_name ?: ($toPlant->plant_site_name ?? '')),
                    'to_code' => $toCodeValue,
                    'destination_region' => $destinationRegionValue,
                    'vendor_code' => $vendorCodeValue,
                    'vendor_name' => $vendor->vendor_name,
                    'mode' => $mode,
                    'truck_code' => $truckCodeValue,
                    'truck_description' => $truck->description,
                    'charge_type' => $chargeTypeValue,
                    'rate_type' => $rateTypeValue,
                    'rate' => round((float) $rateValue, 4),
                    'load_unload_limit' => ($loadUnloadLimitValue === '' ? null : round((float) $loadUnloadLimitValue, 4)),
                    'created_by' => $createdBy,
                ];

                LoadUnloadMaster::create($data);
                $insertedCount++;
            }

            DB::commit();

            return redirect()->route('admin.load-unload-master.manual')
                ->with('success', $insertedCount . ' load/unload rate(s) saved successfully.')
                ->with('errorRows', $errorRows);
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function fetchMasterData(Request $request)
    {
        $type = $request->input('type');
        $code = trim((string) $request->input('code', ''));

        if ($code === '') {
            return response()->json(['error' => 'Code is required.'], 422);
        }

        if ($type === 'from_code' || $type === 'to_code') {
            $plant = Siteplant::where('plant_site_code', $code)->first();

            if (!$plant) {
                return response()->json(['error' => 'Plant code not found.'], 404);
            }

            return response()->json(['name' => $plant->plant_site_location_name ?: ($plant->plant_site_name ?? '')]);
        }

        if ($type === 'vendor_code') {
            $vendor = DB::table('vendors')->where('vendor_code', $code)->first();

            if (!$vendor) {
                return response()->json(['error' => 'Vendor code not found.'], 404);
            }

            return response()->json(['name' => $vendor->vendor_name]);
        }

        if ($type === 'truck_code') {
            $truck = DB::table('truck_master')->where('code', $code)->first();

            if (!$truck) {
                return response()->json(['error' => 'Truck code not found.'], 404);
            }

            return response()->json(['name' => $truck->description]);
        }

        return response()->json(['error' => 'Invalid lookup type.'], 422);
    }

    public function edit($id)
    {
        $title = 'Edit Load/Unload Master';
        $pagetitle = $title;
        $rate = LoadUnloadMaster::findOrFail($id);

        return view('admin.load_unload_master.edit', compact('title', 'pagetitle', 'rate'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'from_code' => 'required|string|max:20',
            'to_code' => 'required|string|max:20',
            'vendor_code' => 'required|string|max:20',
            'truck_code' => 'required|string|max:20',
            'mode' => 'required|in:FTL,PTL,AIR,ROAD,RAIL,SEA,COURIER',
            'charge_type' => 'required|in:Load,Unload',
            'rate_type' => 'required|in:Fix,Per Case,KG',
            'rate' => 'required|numeric|min:0',
            'load_unload_limit' => 'nullable|numeric|min:0',
        ]);

        $rate = LoadUnloadMaster::findOrFail($id);
        $fromPlant = Siteplant::where('plant_site_code', $request->from_code)->first();
        $toPlant = Siteplant::where('plant_site_code', $request->to_code)->first();
        $vendor = DB::table('vendors')->where('vendor_code', $request->vendor_code)->first();
        $truck = DB::table('truck_master')->where('code', $request->truck_code)->first();

        if (!$fromPlant || !$toPlant || !$vendor || !$truck) {
            return back()->withInput()->with('error', 'Invalid From Code, To Code, Vendor Code or Truck Code.');
        }

        $duplicate = LoadUnloadMaster::where('from_code', $request->from_code)
            ->where('to_code', $request->to_code)
            ->where('vendor_code', $request->vendor_code)
            ->where('mode', $request->mode)
            ->where('truck_code', $request->truck_code)
            ->where('charge_type', $request->charge_type)
            ->where('rate_type', $request->rate_type)
            ->where('id', '!=', $id)
            ->exists();

        if ($duplicate) {
            return back()->withInput()->with('error', 'A load/unload rate with this combination already exists.');
        }

        $rate->from_code = $request->from_code;
        $rate->from_name = ($fromPlant->plant_site_location_name ?: ($fromPlant->plant_site_name ?? ''));
        $rate->origin_region = $request->origin_region;
        $rate->to_name = ($toPlant->plant_site_location_name ?: ($toPlant->plant_site_name ?? ''));
        $rate->to_code = $request->to_code;
        $rate->destination_region = $request->destination_region;
        $rate->vendor_code = $request->vendor_code;
        $rate->vendor_name = $vendor->vendor_name;
        $rate->mode = $request->mode;
        $rate->truck_code = $request->truck_code;
        $rate->truck_description = $truck->description;
        $rate->charge_type = $request->charge_type;
        $rate->rate_type = $request->rate_type;
        $rate->rate = round((float) $request->rate, 4);
        $rate->load_unload_limit = ($request->filled('load_unload_limit') ? round((float) $request->load_unload_limit, 4) : null);

        $rate->save();

        return redirect()->route('admin.load-unload-master.list')->with('success', 'Load/Unload rate updated successfully.');
    }

    public function delete(Request $request, $id)
    {
        $rate = LoadUnloadMaster::findOrFail($id);
        $rate->delete();

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Load/Unload rate deleted successfully.']);
        }

        return redirect()->route('admin.load-unload-master.list')->with('success', 'Load/Unload rate deleted successfully.');
    }
}
