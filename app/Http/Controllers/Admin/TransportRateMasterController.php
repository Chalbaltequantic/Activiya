<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\DB;

use App\Models\TransportRateMaster;
use App\Models\Siteplant;

use Auth;


class TransportRateMasterController extends Controller
{
    // Confirm this role ID against your application's admin roles.
    private const ADMIN_ROLE_ID = 1;

    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index(Request $request)
    {
        $title = 'Transport Rate Master Upload';
        $pagetitle = $title . ' Listing';

        return view('admin.transport_rate_master.index', compact('title', 'pagetitle'));
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
                if (count(array_filter($row, fn($value) => trim((string) $value) !== '')) === 0) {
                    continue;
                }

                /* XLS Column Mapping

                 | A : Lane Name
                 | B : From Code
                 | C : From Name
                 | D : Origin Region
                 | E : To
                 | F : To Code
                 | G : Destination Region
                 | H : Vendor Code
                 | I : Vendor Name
                 | J : Mode
                 | K : Chargeable Weight
                 | L : CFT Value
                 | M : CFT Weight
                 | N : Rate Logic / Base Rate per kg/Case
                 | O : Pickup Charges
                 | P : Delivery Charges
                 | Q : Detention Rate
                 | R : FOV on Base Rate
                 | S : Fuel Surcharge on Base Rate
                 | T : ODA
                 | U : ODA 1
                 | V : ODA 2
                 | W : Minimum Chargeable Weight/Case
                 | X : Validity Start Date
                 | Y : Validity End Date
                 | Z : Docket Charges
                 | AA : Custom
                 | AB : Custom 1
                 | AC : Custom 2
                 | AD : Custom 3
                 | AE : Custom 4
                */

                $laneName = trim((string) ($row['A'] ?? ''));
                $fromCode = trim((string) ($row['B'] ?? ''));
                $fromName = trim((string) ($row['C'] ?? ''));
                $originRegion = trim((string) ($row['D'] ?? ''));
                $toName = trim((string) ($row['E'] ?? ''));
                $toCode = trim((string) ($row['F'] ?? ''));
                $destinationRegion = trim((string) ($row['G'] ?? ''));
                $vendorCode = trim((string) ($row['H'] ?? ''));
                $vendorName = trim((string) ($row['I'] ?? ''));
                $mode = trim((string) ($row['J'] ?? ''));
                $chargeableWeight = trim((string) ($row['K'] ?? ''));
                $cftValue = trim((string) ($row['L'] ?? ''));
                $cftWeight = trim((string) ($row['M'] ?? ''));
                $rateLogic = trim((string) ($row['N'] ?? ''));
                $pickupCharges = trim((string) ($row['O'] ?? ''));
                $deliveryCharges = trim((string) ($row['P'] ?? ''));
                $detentionRate = trim((string) ($row['Q'] ?? ''));
                $fovOnBaseRate = trim((string) ($row['R'] ?? ''));
                $fuelSurchargeOnBaseRate = trim((string) ($row['S'] ?? ''));
                $oda = trim((string) ($row['T'] ?? ''));
                $oda1 = trim((string) ($row['U'] ?? ''));
                $oda2 = trim((string) ($row['V'] ?? ''));
                $minimumChargeableWeightCase = trim((string) ($row['W'] ?? ''));
                $validityStartDate = trim((string) ($row['X'] ?? ''));
                $validityEndDate = trim((string) ($row['Y'] ?? ''));
                $docketCharges = trim((string) ($row['Z'] ?? ''));
                $custom = trim((string) ($row['AA'] ?? ''));
                $custom1 = trim((string) ($row['AB'] ?? ''));
                $custom2 = trim((string) ($row['AC'] ?? ''));
                $custom3 = trim((string) ($row['AD'] ?? ''));
                $custom4 = trim((string) ($row['AE'] ?? ''));

                if ($fromCode === '') {
                    $errorRows[] = [
                        'row' => $rowNumber,
                        'reason' => 'From Code is required'
                    ];
                    continue;
                }

                if ($toCode === '') {
                    $errorRows[] = [
                        'row' => $rowNumber,
                        'reason' => 'To Code is required'
                    ];
                    continue;
                }

                if ($vendorCode === '') {
                    $errorRows[] = [
                        'row' => $rowNumber,
                        'reason' => 'Vendor Code is required'
                    ];
                    continue;
                }

                if ($mode === '') {
                    $errorRows[] = [
                        'row' => $rowNumber,
                        'reason' => 'Mode is required'
                    ];
                    continue;
                }

                if ($rateLogic === '') {
                    $errorRows[] = [
                        'row' => $rowNumber,
                        'reason' => 'Base Rate is required'
                    ];
                    continue;
                }

                if ($validityStartDate === '') {
                    $errorRows[] = [
                        'row' => $rowNumber,
                        'reason' => 'Validity Start Date is required'
                    ];
                    continue;
                }

                if ($validityEndDate === '') {
                    $errorRows[] = [
                        'row' => $rowNumber,
                        'reason' => 'Validity End Date is required'
                    ];
                    continue;
                }

                if ($chargeableWeight !== '' && !is_numeric($chargeableWeight)) {
                    $errorRows[] = [
                        'row' => $rowNumber,
                        'reason' => 'Chargeable Weight must be numeric'
                    ];
                    continue;
                }

                if ($cftValue !== '' && !is_numeric($cftValue)) {
                    $errorRows[] = [
                        'row' => $rowNumber,
                        'reason' => 'Cft Value must be numeric'
                    ];
                    continue;
                }

                if ($cftWeight !== '' && !is_numeric($cftWeight)) {
                    $errorRows[] = [
                        'row' => $rowNumber,
                        'reason' => 'Cft Weight must be numeric'
                    ];
                    continue;
                }

                if ($rateLogic !== '' && !is_numeric($rateLogic)) {
                    $errorRows[] = [
                        'row' => $rowNumber,
                        'reason' => 'Rate Logic must be numeric'
                    ];
                    continue;
                }

                if ($pickupCharges !== '' && !is_numeric($pickupCharges)) {
                    $errorRows[] = [
                        'row' => $rowNumber,
                        'reason' => 'Pickup Charges must be numeric'
                    ];
                    continue;
                }

                if ($deliveryCharges !== '' && !is_numeric($deliveryCharges)) {
                    $errorRows[] = [
                        'row' => $rowNumber,
                        'reason' => 'Delivery Charges must be numeric'
                    ];
                    continue;
                }

                if ($detentionRate !== '' && !is_numeric($detentionRate)) {
                    $errorRows[] = [
                        'row' => $rowNumber,
                        'reason' => 'Detention Rate must be numeric'
                    ];
                    continue;
                }

                if ($fovOnBaseRate !== '' && !is_numeric($fovOnBaseRate)) {
                    $errorRows[] = [
                        'row' => $rowNumber,
                        'reason' => 'Fov On Base Rate must be numeric'
                    ];
                    continue;
                }

                if ($fuelSurchargeOnBaseRate !== '' && !is_numeric($fuelSurchargeOnBaseRate)) {
                    $errorRows[] = [
                        'row' => $rowNumber,
                        'reason' => 'Fuel Surcharge On Base Rate must be numeric'
                    ];
                    continue;
                }

                if ($oda !== '' && !is_numeric($oda)) {
                    $errorRows[] = [
                        'row' => $rowNumber,
                        'reason' => 'Oda must be numeric'
                    ];
                    continue;
                }

                if ($oda1 !== '' && !is_numeric($oda1)) {
                    $errorRows[] = [
                        'row' => $rowNumber,
                        'reason' => 'Oda 1 must be numeric'
                    ];
                    continue;
                }

                if ($oda2 !== '' && !is_numeric($oda2)) {
                    $errorRows[] = [
                        'row' => $rowNumber,
                        'reason' => 'Oda 2 must be numeric'
                    ];
                    continue;
                }

                if ($minimumChargeableWeightCase !== '' && !is_numeric($minimumChargeableWeightCase)) {
                    $errorRows[] = [
                        'row' => $rowNumber,
                        'reason' => 'Minimum Chargeable Weight Case must be numeric'
                    ];
                    continue;
                }

                if ($docketCharges !== '' && !is_numeric($docketCharges)) {
                    $errorRows[] = [
                        'row' => $rowNumber,
                        'reason' => 'Docket Charges must be numeric'
                    ];
                    continue;
                }

                if (strtoupper($mode) === 'FTL') {
                    $errorRows[] = ['row' => $rowNumber, 'reason' => 'FTL rates must use the FTL Rate Master'];
                    continue;
                }

                if (!in_array(strtoupper($mode), ['PTL', 'AIR', 'ROAD', 'RAIL', 'SEA', 'COURIER'])) {
                    $errorRows[] = ['row' => $rowNumber, 'reason' => 'Invalid Transport Mode: ' . $mode];
                    continue;
                }

                $startDate = $this->formatExcelDate($validityStartDate);
                $endDate = $this->formatExcelDate($validityEndDate);

                if (!$startDate || !$endDate || $endDate < $startDate) {
                    $errorRows[] = ['row' => $rowNumber, 'reason' => 'Invalid rate validity dates'];
                    continue;
                }

                $fromPlant = Siteplant::where('plant_site_code', $fromCode)->first();
                $toPlant = Siteplant::where('plant_site_code', $toCode)->first();

                if (!$fromPlant || !$toPlant) {
                    $errorRows[] = ['row' => $rowNumber, 'reason' => 'Invalid From Code or To Code'];
                    continue;
                }

                $fromName = $fromPlant->plant_site_location_name ?? $fromPlant->plant_site_name ?? $fromName;
                $toName = $toPlant->plant_site_location_name ?? $toPlant->plant_site_name ?? $toName;

                /* Skip duplicate rate for same lane, mode, vendor and validity */
                $exists = TransportRateMaster::where('from_code', $fromCode)
                    ->where('to_code', $toCode)
                    ->where('vendor_code', $vendorCode)
                    ->where('mode', strtoupper($mode))
                    ->where('validity_start_date', $startDate)
                    ->where('validity_end_date', $endDate)
                    ->exists();

                if ($exists) {
                    $errorRows[] = ['row' => $rowNumber, 'reason' => 'Rate already exists for this lane, vendor, mode and validity'];
                    continue;
                }

                $data = [

                    'lane_name' => ($laneName === '' ? null : $laneName),
                    'from_code' => ($fromCode === '' ? null : $fromCode),
                    'from_name' => ($fromName === '' ? null : $fromName),
                    'origin_region' => ($originRegion === '' ? null : $originRegion),
                    'to_name' => ($toName === '' ? null : $toName),
                    'to_code' => ($toCode === '' ? null : $toCode),
                    'destination_region' => ($destinationRegion === '' ? null : $destinationRegion),
                    'vendor_code' => ($vendorCode === '' ? null : $vendorCode),
                    'vendor_name' => ($vendorName === '' ? null : $vendorName),
                    'mode' => strtoupper($mode),
                    'chargeable_weight' => ($chargeableWeight === '' ? null : round((float) $chargeableWeight, 2)),
                    'cft_value' => ($cftValue === '' ? null : round((float) $cftValue, 2)),
                    'cft_weight' => ($cftWeight === '' ? null : round((float) $cftWeight, 2)),
                    'rate_logic' => ($rateLogic === '' ? null : round((float) $rateLogic, 2)),
                    'pickup_charges' => ($pickupCharges === '' ? null : round((float) $pickupCharges, 2)),
                    'delivery_charges' => ($deliveryCharges === '' ? null : round((float) $deliveryCharges, 2)),
                    'detention_rate' => ($detentionRate === '' ? null : round((float) $detentionRate, 2)),
                    'fov_on_base_rate' => ($fovOnBaseRate === '' ? null : round((float) $fovOnBaseRate, 2)),
                    'fuel_surcharge_on_base_rate' => ($fuelSurchargeOnBaseRate === '' ? null : round((float) $fuelSurchargeOnBaseRate, 2)),
                    'oda' => ($oda === '' ? null : round((float) $oda, 2)),
                    'oda_1' => ($oda1 === '' ? null : round((float) $oda1, 2)),
                    'oda_2' => ($oda2 === '' ? null : round((float) $oda2, 2)),
                    'minimum_chargeable_weight_case' => ($minimumChargeableWeightCase === '' ? null : round((float) $minimumChargeableWeightCase, 2)),
                    'validity_start_date' => $startDate,
                    'validity_end_date' => $endDate,
                    'docket_charges' => ($docketCharges === '' ? null : round((float) $docketCharges, 2)),
                    'custom' => ($custom === '' ? null : $custom),
                    'custom_1' => ($custom1 === '' ? null : $custom1),
                    'custom_2' => ($custom2 === '' ? null : $custom2),
                    'custom_3' => ($custom3 === '' ? null : $custom3),
                    'custom_4' => ($custom4 === '' ? null : $custom4),
                    'created_by' => $createdBy,
                    'created_at' => $createdDate,
                    'updated_at' => $createdDate,
                ];

                TransportRateMaster::create($data);
                $insertedCount++;
            }

            DB::commit();

            if ($insertedCount === 0) {
                return redirect()->back()->withInput()->with([
                    'error' => 'No data inserted. Please correct the highlighted errors.',
                    'errorRows' => $errorRows,
                ]);
            }

            return redirect()->back()->with([
                'success' => $insertedCount . ' Transport Rate rows inserted successfully.',
                'errorRows' => $errorRows,
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    private function getFilteredRateQuery(Request $request)
    {
        $query = TransportRateMaster::query();

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('lane_name', 'like', '%' . $search . '%')
                    ->orWhere('from_code', 'like', '%' . $search . '%')
                    ->orWhere('from_name', 'like', '%' . $search . '%')
                    ->orWhere('to_code', 'like', '%' . $search . '%')
                    ->orWhere('to_name', 'like', '%' . $search . '%')
                    ->orWhere('vendor_code', 'like', '%' . $search . '%')
                    ->orWhere('vendor_name', 'like', '%' . $search . '%')
                    ->orWhere('mode', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('mode')) {
            $query->where('mode', $request->mode);
        }

        if ($request->filled('vendor_code')) {
            $query->where('vendor_code', $request->vendor_code);
        }

        if ($request->filled('from_code')) {
            $query->where('from_code', $request->from_code);
        }

        return $query;
    }

    public function listing(Request $request)
    {
        $title = 'Transport Rate Master List';
        $pagetitle = $title . ' Listing';
        $perPage = (int) $request->get('per_page', 25);

        if (!in_array($perPage, [10, 25, 50, 100])) {
            $perPage = 25;
        }

        $allowedSortColumns = [

            'lane_name',
            'from_code',
            'from_name',
            'origin_region',
            'to_name',
            'to_code',
            'destination_region',
            'vendor_code',
            'vendor_name',
            'mode',
            'chargeable_weight',
            'cft_value',
            'cft_weight',
            'rate_logic',
            'pickup_charges',
            'delivery_charges',
            'detention_rate',
            'fov_on_base_rate',
            'fuel_surcharge_on_base_rate',
            'oda',
            'oda_1',
            'oda_2',
            'minimum_chargeable_weight_case',
            'validity_start_date',
            'validity_end_date',
            'docket_charges',
            'custom',
            'custom_1',
            'custom_2',
            'custom_3',
            'custom_4',
            'created_at'
        ];

        $sortBy = $request->get('sort_by', 'created_at');
        $sortDirection = strtolower($request->get('sort_direction', 'desc'));

        if (!in_array($sortBy, $allowedSortColumns)) {
            $sortBy = 'created_at';
        }

        if (!in_array($sortDirection, ['asc', 'desc'])) {
            $sortDirection = 'desc';
        }

        $query = $this->getFilteredRateQuery($request);
        $datalist = $query->orderBy($sortBy, $sortDirection)->paginate($perPage);
        $datalist->appends($request->query());

        $vendors = TransportRateMaster::select('vendor_code', 'vendor_name')
            ->distinct()->orderBy('vendor_code')->get();

        $modes = ['PTL', 'AIR', 'ROAD', 'RAIL', 'SEA', 'COURIER'];
        $userRole = Auth::user()->role_id ?? null;

        return view('admin.transport_rate_master.list', compact(
            'pagetitle', 'title', 'datalist', 'vendors', 'modes',
            'perPage', 'userRole', 'sortBy', 'sortDirection'
        ));
    }

    public function datalist(Request $request)
    {
        return $this->listing($request);
    }

    public function export(Request $request)
    {
        try {
            $query = $this->getFilteredRateQuery($request);
            $datalist = $query->orderBy('created_at', 'desc')->get();

            if ($datalist->count() === 0) {
                return redirect()->back()->with('error', 'No Transport Rate Master data found for download.');
            }

            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Transport Rate Master');

            $sheet->setCellValue('A1', 'Lane Name');
            $sheet->setCellValue('B1', 'From Code');
            $sheet->setCellValue('C1', 'From Name');
            $sheet->setCellValue('D1', 'Origin Region');
            $sheet->setCellValue('E1', 'To');
            $sheet->setCellValue('F1', 'To Code');
            $sheet->setCellValue('G1', 'Destination Region');
            $sheet->setCellValue('H1', 'Vendor Code');
            $sheet->setCellValue('I1', 'Vendor Name');
            $sheet->setCellValue('J1', 'Mode');
            $sheet->setCellValue('K1', 'Chargeable Weight');
            $sheet->setCellValue('L1', 'CFT Value');
            $sheet->setCellValue('M1', 'CFT Weight');
            $sheet->setCellValue('N1', 'Rate Logic / Base Rate per kg/Case');
            $sheet->setCellValue('O1', 'Pickup Charges');
            $sheet->setCellValue('P1', 'Delivery Charges');
            $sheet->setCellValue('Q1', 'Detention Rate');
            $sheet->setCellValue('R1', 'FOV on Base Rate');
            $sheet->setCellValue('S1', 'Fuel Surcharge on Base Rate');
            $sheet->setCellValue('T1', 'ODA');
            $sheet->setCellValue('U1', 'ODA 1');
            $sheet->setCellValue('V1', 'ODA 2');
            $sheet->setCellValue('W1', 'Minimum Chargeable Weight/Case');
            $sheet->setCellValue('X1', 'Validity Start Date');
            $sheet->setCellValue('Y1', 'Validity End Date');
            $sheet->setCellValue('Z1', 'Docket Charges');
            $sheet->setCellValue('AA1', 'Custom');
            $sheet->setCellValue('AB1', 'Custom 1');
            $sheet->setCellValue('AC1', 'Custom 2');
            $sheet->setCellValue('AD1', 'Custom 3');
            $sheet->setCellValue('AE1', 'Custom 4');

            $xlsRow = 2;

            foreach ($datalist as $rate) {

                $sheet->setCellValue('A' . $xlsRow, $rate->lane_name);
                $sheet->setCellValueExplicit('B' . $xlsRow, $rate->from_code, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                $sheet->setCellValue('C' . $xlsRow, $rate->from_name);
                $sheet->setCellValue('D' . $xlsRow, $rate->origin_region);
                $sheet->setCellValue('E' . $xlsRow, $rate->to_name);
                $sheet->setCellValueExplicit('F' . $xlsRow, $rate->to_code, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                $sheet->setCellValue('G' . $xlsRow, $rate->destination_region);
                $sheet->setCellValueExplicit('H' . $xlsRow, $rate->vendor_code, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                $sheet->setCellValue('I' . $xlsRow, $rate->vendor_name);
                $sheet->setCellValue('J' . $xlsRow, $rate->mode);
                $sheet->setCellValue('K' . $xlsRow, $rate->chargeable_weight);
                $sheet->setCellValue('L' . $xlsRow, $rate->cft_value);
                $sheet->setCellValue('M' . $xlsRow, $rate->cft_weight);
                $sheet->setCellValue('N' . $xlsRow, $rate->rate_logic);
                $sheet->setCellValue('O' . $xlsRow, $rate->pickup_charges);
                $sheet->setCellValue('P' . $xlsRow, $rate->delivery_charges);
                $sheet->setCellValue('Q' . $xlsRow, $rate->detention_rate);
                $sheet->setCellValue('R' . $xlsRow, $rate->fov_on_base_rate);
                $sheet->setCellValue('S' . $xlsRow, $rate->fuel_surcharge_on_base_rate);
                $sheet->setCellValue('T' . $xlsRow, $rate->oda);
                $sheet->setCellValue('U' . $xlsRow, $rate->oda_1);
                $sheet->setCellValue('V' . $xlsRow, $rate->oda_2);
                $sheet->setCellValue('W' . $xlsRow, $rate->minimum_chargeable_weight_case);
                $sheet->setCellValue('X' . $xlsRow, $rate->validity_start_date);
                $sheet->setCellValue('Y' . $xlsRow, $rate->validity_end_date);
                $sheet->setCellValue('Z' . $xlsRow, $rate->docket_charges);
                $sheet->setCellValue('AA' . $xlsRow, $rate->custom);
                $sheet->setCellValue('AB' . $xlsRow, $rate->custom_1);
                $sheet->setCellValue('AC' . $xlsRow, $rate->custom_2);
                $sheet->setCellValue('AD' . $xlsRow, $rate->custom_3);
                $sheet->setCellValue('AE' . $xlsRow, $rate->custom_4);
                $xlsRow++;
            }

            $sheet->getStyle('A1:AE1')->getFont()->setBold(true);
            $sheet->getStyle('A1:AE1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('A1:AE1')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
            $sheet->getStyle('A1:AE1')->getAlignment()->setWrapText(true);
            $sheet->freezePane('A2');
            $sheet->setAutoFilter('A1:AE' . ($xlsRow - 1));

            foreach (range('A', 'Z') as $column) {
                $sheet->getColumnDimension($column)->setAutoSize(true);
            }

            foreach (['AA', 'AB', 'AC', 'AD', 'AE'] as $column) {
                $sheet->getColumnDimension($column)->setAutoSize(true);
            }

            $fileName = 'Transport_Rate_Master_' . date('Y-m-d_H-i-s') . '.xlsx';
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

            return response()->streamDownload(function () use ($writer) {
                $writer->save('php://output');
            }, $fileName, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ]);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Export Error: ' . $e->getMessage());
        }
    }

    public function template()
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Transport Rate Master');

        $sheet->setCellValue('A1', 'Lane Name');
        $sheet->setCellValue('B1', 'From Code');
        $sheet->setCellValue('C1', 'From Name');
        $sheet->setCellValue('D1', 'Origin Region');
        $sheet->setCellValue('E1', 'To');
        $sheet->setCellValue('F1', 'To Code');
        $sheet->setCellValue('G1', 'Destination Region');
        $sheet->setCellValue('H1', 'Vendor Code');
        $sheet->setCellValue('I1', 'Vendor Name');
        $sheet->setCellValue('J1', 'Mode');
        $sheet->setCellValue('K1', 'Chargeable Weight');
        $sheet->setCellValue('L1', 'CFT Value');
        $sheet->setCellValue('M1', 'CFT Weight');
        $sheet->setCellValue('N1', 'Rate Logic / Base Rate per kg/Case');
        $sheet->setCellValue('O1', 'Pickup Charges');
        $sheet->setCellValue('P1', 'Delivery Charges');
        $sheet->setCellValue('Q1', 'Detention Rate');
        $sheet->setCellValue('R1', 'FOV on Base Rate');
        $sheet->setCellValue('S1', 'Fuel Surcharge on Base Rate');
        $sheet->setCellValue('T1', 'ODA');
        $sheet->setCellValue('U1', 'ODA 1');
        $sheet->setCellValue('V1', 'ODA 2');
        $sheet->setCellValue('W1', 'Minimum Chargeable Weight/Case');
        $sheet->setCellValue('X1', 'Validity Start Date');
        $sheet->setCellValue('Y1', 'Validity End Date');
        $sheet->setCellValue('Z1', 'Docket Charges');
        $sheet->setCellValue('AA1', 'Custom');
        $sheet->setCellValue('AB1', 'Custom 1');
        $sheet->setCellValue('AC1', 'Custom 2');
        $sheet->setCellValue('AD1', 'Custom 3');
        $sheet->setCellValue('AE1', 'Custom 4');

        $sheet->getStyle('A1:AE1')->getFont()->setBold(true);
        $sheet->freezePane('A2');

        $fileName = 'Transport_Rate_Master_Template.xlsx';
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $fileName);
    }

    public function manualupload()
    {
        $title = 'Transport Rate Manual Upload';
        $pagetitle = $title;
        $modes = ['PTL', 'AIR', 'ROAD', 'RAIL', 'SEA', 'COURIER'];

        return view('admin.transport_rate_master.manual', compact('title', 'pagetitle', 'modes'));
    }

    public function manual()
    {
        return $this->manualupload();
    }

    public function save_manual_data(Request $request)
    {
        $createdBy = Auth::user()->id;
        $createdDate = now();


        $laneName = $request->input('lane_name', []);
        $fromCode = $request->input('from_code', []);
        $fromName = $request->input('from_name', []);
        $originRegion = $request->input('origin_region', []);
        $toName = $request->input('to_name', []);
        $toCode = $request->input('to_code', []);
        $destinationRegion = $request->input('destination_region', []);
        $vendorCode = $request->input('vendor_code', []);
        $vendorName = $request->input('vendor_name', []);
        $mode = $request->input('mode', []);
        $chargeableWeight = $request->input('chargeable_weight', []);
        $cftValue = $request->input('cft_value', []);
        $cftWeight = $request->input('cft_weight', []);
        $rateLogic = $request->input('rate_logic', []);
        $pickupCharges = $request->input('pickup_charges', []);
        $deliveryCharges = $request->input('delivery_charges', []);
        $detentionRate = $request->input('detention_rate', []);
        $fovOnBaseRate = $request->input('fov_on_base_rate', []);
        $fuelSurchargeOnBaseRate = $request->input('fuel_surcharge_on_base_rate', []);
        $oda = $request->input('oda', []);
        $oda1 = $request->input('oda_1', []);
        $oda2 = $request->input('oda_2', []);
        $minimumChargeableWeightCase = $request->input('minimum_chargeable_weight_case', []);
        $validityStartDate = $request->input('validity_start_date', []);
        $validityEndDate = $request->input('validity_end_date', []);
        $docketCharges = $request->input('docket_charges', []);
        $custom = $request->input('custom', []);
        $custom1 = $request->input('custom_1', []);
        $custom2 = $request->input('custom_2', []);
        $custom3 = $request->input('custom_3', []);
        $custom4 = $request->input('custom_4', []);

        $count = count($fromCode);
        $insertedCount = 0;
        $errorRows = [];

        for ($i = 0; $i < $count; $i++) {
            $rowNumber = $i + 1;

            if (empty($fromCode[$i]) && empty($toCode[$i]) && empty($vendorCode[$i]) && empty($mode[$i])) {
                continue;
            }

            try {

                if (trim((string) ($fromCode[$i] ?? '')) === '') {
                    $errorRows[] = ['row' => $rowNumber, 'reason' => 'From Code is required'];
                    continue;
                }

                if (trim((string) ($toCode[$i] ?? '')) === '') {
                    $errorRows[] = ['row' => $rowNumber, 'reason' => 'To Code is required'];
                    continue;
                }

                if (trim((string) ($vendorCode[$i] ?? '')) === '') {
                    $errorRows[] = ['row' => $rowNumber, 'reason' => 'Vendor Code is required'];
                    continue;
                }

                if (trim((string) ($mode[$i] ?? '')) === '') {
                    $errorRows[] = ['row' => $rowNumber, 'reason' => 'Mode is required'];
                    continue;
                }

                if (trim((string) ($rateLogic[$i] ?? '')) === '') {
                    $errorRows[] = ['row' => $rowNumber, 'reason' => 'Base Rate is required'];
                    continue;
                }

                if (trim((string) ($validityStartDate[$i] ?? '')) === '') {
                    $errorRows[] = ['row' => $rowNumber, 'reason' => 'Validity Start Date is required'];
                    continue;
                }

                if (trim((string) ($validityEndDate[$i] ?? '')) === '') {
                    $errorRows[] = ['row' => $rowNumber, 'reason' => 'Validity End Date is required'];
                    continue;
                }

                if (($chargeableWeight[$i] ?? '') !== '' && !is_numeric($chargeableWeight[$i])) {
                    $errorRows[] = ['row' => $rowNumber, 'reason' => 'Chargeable Weight must be numeric'];
                    continue;
                }

                if (($cftValue[$i] ?? '') !== '' && !is_numeric($cftValue[$i])) {
                    $errorRows[] = ['row' => $rowNumber, 'reason' => 'Cft Value must be numeric'];
                    continue;
                }

                if (($cftWeight[$i] ?? '') !== '' && !is_numeric($cftWeight[$i])) {
                    $errorRows[] = ['row' => $rowNumber, 'reason' => 'Cft Weight must be numeric'];
                    continue;
                }

                if (($rateLogic[$i] ?? '') !== '' && !is_numeric($rateLogic[$i])) {
                    $errorRows[] = ['row' => $rowNumber, 'reason' => 'Rate Logic must be numeric'];
                    continue;
                }

                if (($pickupCharges[$i] ?? '') !== '' && !is_numeric($pickupCharges[$i])) {
                    $errorRows[] = ['row' => $rowNumber, 'reason' => 'Pickup Charges must be numeric'];
                    continue;
                }

                if (($deliveryCharges[$i] ?? '') !== '' && !is_numeric($deliveryCharges[$i])) {
                    $errorRows[] = ['row' => $rowNumber, 'reason' => 'Delivery Charges must be numeric'];
                    continue;
                }

                if (($detentionRate[$i] ?? '') !== '' && !is_numeric($detentionRate[$i])) {
                    $errorRows[] = ['row' => $rowNumber, 'reason' => 'Detention Rate must be numeric'];
                    continue;
                }

                if (($fovOnBaseRate[$i] ?? '') !== '' && !is_numeric($fovOnBaseRate[$i])) {
                    $errorRows[] = ['row' => $rowNumber, 'reason' => 'Fov On Base Rate must be numeric'];
                    continue;
                }

                if (($fuelSurchargeOnBaseRate[$i] ?? '') !== '' && !is_numeric($fuelSurchargeOnBaseRate[$i])) {
                    $errorRows[] = ['row' => $rowNumber, 'reason' => 'Fuel Surcharge On Base Rate must be numeric'];
                    continue;
                }

                if (($oda[$i] ?? '') !== '' && !is_numeric($oda[$i])) {
                    $errorRows[] = ['row' => $rowNumber, 'reason' => 'Oda must be numeric'];
                    continue;
                }

                if (($oda1[$i] ?? '') !== '' && !is_numeric($oda1[$i])) {
                    $errorRows[] = ['row' => $rowNumber, 'reason' => 'Oda 1 must be numeric'];
                    continue;
                }

                if (($oda2[$i] ?? '') !== '' && !is_numeric($oda2[$i])) {
                    $errorRows[] = ['row' => $rowNumber, 'reason' => 'Oda 2 must be numeric'];
                    continue;
                }

                if (($minimumChargeableWeightCase[$i] ?? '') !== '' && !is_numeric($minimumChargeableWeightCase[$i])) {
                    $errorRows[] = ['row' => $rowNumber, 'reason' => 'Minimum Chargeable Weight Case must be numeric'];
                    continue;
                }

                if (($docketCharges[$i] ?? '') !== '' && !is_numeric($docketCharges[$i])) {
                    $errorRows[] = ['row' => $rowNumber, 'reason' => 'Docket Charges must be numeric'];
                    continue;
                }

                $fromPlant = Siteplant::where('plant_site_code', trim($fromCode[$i]))->first();
                $toPlant = Siteplant::where('plant_site_code', trim($toCode[$i]))->first();

                if (!$fromPlant || !$toPlant) {
                    $errorRows[] = ['row' => $rowNumber, 'reason' => 'Invalid From Code or To Code'];
                    continue;
                }

                $transportMode = strtoupper(trim($mode[$i]));

                if (!in_array($transportMode, ['PTL', 'AIR', 'ROAD', 'RAIL', 'SEA', 'COURIER'])) {
                    $errorRows[] = ['row' => $rowNumber, 'reason' => 'Invalid Transport Mode'];
                    continue;
                }

                $startDate = $this->formatExcelDate($validityStartDate[$i] ?? null);
                $endDate = $this->formatExcelDate($validityEndDate[$i] ?? null);

                if (!$startDate || !$endDate || $endDate < $startDate) {
                    $errorRows[] = ['row' => $rowNumber, 'reason' => 'Invalid rate validity dates'];
                    continue;
                }

                $exists = TransportRateMaster::where('from_code', trim($fromCode[$i]))
                    ->where('to_code', trim($toCode[$i]))
                    ->where('vendor_code', trim($vendorCode[$i]))
                    ->where('mode', $transportMode)
                    ->where('validity_start_date', $startDate)
                    ->where('validity_end_date', $endDate)
                    ->exists();

                if ($exists) {
                    $errorRows[] = ['row' => $rowNumber, 'reason' => 'Rate already exists for this lane, vendor, mode and validity'];
                    continue;
                }

                $data = [

                    'lane_name' => trim((string) ($laneName[$i] ?? '')) ?: null,
                    'from_code' => trim((string) ($fromCode[$i] ?? '')) ?: null,
                    'from_name' => $fromPlant->plant_site_location_name ?? $fromPlant->plant_site_name ?? null,
                    'origin_region' => trim((string) ($originRegion[$i] ?? '')) ?: null,
                    'to_name' => $toPlant->plant_site_location_name ?? $toPlant->plant_site_name ?? null,
                    'to_code' => trim((string) ($toCode[$i] ?? '')) ?: null,
                    'destination_region' => trim((string) ($destinationRegion[$i] ?? '')) ?: null,
                    'vendor_code' => trim((string) ($vendorCode[$i] ?? '')) ?: null,
                    'vendor_name' => trim((string) ($vendorName[$i] ?? '')) ?: null,
                    'mode' => $transportMode,
                    'chargeable_weight' => (($chargeableWeight[$i] ?? '') === '' ? null : round((float) $chargeableWeight[$i], 2)),
                    'cft_value' => (($cftValue[$i] ?? '') === '' ? null : round((float) $cftValue[$i], 2)),
                    'cft_weight' => (($cftWeight[$i] ?? '') === '' ? null : round((float) $cftWeight[$i], 2)),
                    'rate_logic' => (($rateLogic[$i] ?? '') === '' ? null : round((float) $rateLogic[$i], 2)),
                    'pickup_charges' => (($pickupCharges[$i] ?? '') === '' ? null : round((float) $pickupCharges[$i], 2)),
                    'delivery_charges' => (($deliveryCharges[$i] ?? '') === '' ? null : round((float) $deliveryCharges[$i], 2)),
                    'detention_rate' => (($detentionRate[$i] ?? '') === '' ? null : round((float) $detentionRate[$i], 2)),
                    'fov_on_base_rate' => (($fovOnBaseRate[$i] ?? '') === '' ? null : round((float) $fovOnBaseRate[$i], 2)),
                    'fuel_surcharge_on_base_rate' => (($fuelSurchargeOnBaseRate[$i] ?? '') === '' ? null : round((float) $fuelSurchargeOnBaseRate[$i], 2)),
                    'oda' => (($oda[$i] ?? '') === '' ? null : round((float) $oda[$i], 2)),
                    'oda_1' => (($oda1[$i] ?? '') === '' ? null : round((float) $oda1[$i], 2)),
                    'oda_2' => (($oda2[$i] ?? '') === '' ? null : round((float) $oda2[$i], 2)),
                    'minimum_chargeable_weight_case' => (($minimumChargeableWeightCase[$i] ?? '') === '' ? null : round((float) $minimumChargeableWeightCase[$i], 2)),
                    'validity_start_date' => $startDate,
                    'validity_end_date' => $endDate,
                    'docket_charges' => (($docketCharges[$i] ?? '') === '' ? null : round((float) $docketCharges[$i], 2)),
                    'custom' => trim((string) ($custom[$i] ?? '')) ?: null,
                    'custom_1' => trim((string) ($custom1[$i] ?? '')) ?: null,
                    'custom_2' => trim((string) ($custom2[$i] ?? '')) ?: null,
                    'custom_3' => trim((string) ($custom3[$i] ?? '')) ?: null,
                    'custom_4' => trim((string) ($custom4[$i] ?? '')) ?: null,
                    'created_by' => $createdBy,
                    'created_at' => $createdDate,
                    'updated_at' => $createdDate,
                ];

                TransportRateMaster::create($data);
                $insertedCount++;

            } catch (\Exception $e) {
                $errorRows[] = ['row' => $rowNumber, 'reason' => $e->getMessage()];
                continue;
            }
        }

        if ($insertedCount === 0) {
            return redirect()->back()->withInput()->with([
                'error' => 'No data inserted. Please correct row errors.',
                'errorRows' => $errorRows,
            ]);
        }

        return redirect()->back()->with([
            'success' => $insertedCount . ' Transport Rate rows inserted successfully.',
            'errorRows' => $errorRows,
        ]);
    }

    public function saveManual(Request $request)
    {
        return $this->save_manual_data($request);
    }

    /* AJAX FETCH PLANT */
    public function fetchPlantData(Request $request)
    {
        try {
            $plantCode = trim((string) ($request->plant_code ?? $request->code ?? ''));

            if ($plantCode === '') {
                return response()->json(['error' => 'Plant Code is required']);
            }

            $plant = Siteplant::where('plant_site_code', $plantCode)->first();

            if (!$plant) {
                return response()->json(['error' => 'Invalid Plant Code: ' . $plantCode]);
            }

            return response()->json([
                'plant_name' => $plant->plant_site_location_name ?? $plant->plant_site_name ?? '',
                'name' => $plant->plant_site_location_name ?? $plant->plant_site_name ?? '',
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function lookup(Request $request)
    {
        return $this->fetchPlantData($request);
    }

    /* EDIT TRANSPORT RATE */
    public function edit($id)
    {
        if (!$this->isAdminUser()) {
            abort(403, 'You are not authorized to edit Transport Rate Master.');
        }

        $title = 'Edit Transport Rate Master';
        $pagetitle = $title;
        $rate = TransportRateMaster::findOrFail($id);

        return view('admin.transport_rate_master.edit', compact('title', 'pagetitle', 'rate'));
    }

    /* UPDATE TRANSPORT RATE */
    public function update(Request $request, $id)
    {
        if (!$this->isAdminUser()) {
            abort(403, 'You are not authorized to update Transport Rate Master.');
        }

        $request->validate([
            'from_code' => 'required|max:20',
            'to_code' => 'required|max:20',
            'vendor_code' => 'required|max:20',
            'mode' => 'required|in:PTL,AIR,ROAD,RAIL,SEA,COURIER',
            'rate_logic' => 'required|numeric|min:0',
            'validity_start_date' => 'required|date',
            'validity_end_date' => 'required|date|after_or_equal:validity_start_date',
        ]);

        try {
            $rate = TransportRateMaster::findOrFail($id);
            $fromPlant = Siteplant::where('plant_site_code', trim($request->from_code))->first();
            $toPlant = Siteplant::where('plant_site_code', trim($request->to_code))->first();

            if (!$fromPlant || !$toPlant) {
                return redirect()->back()->withInput()->with('error', 'Invalid From Code or To Code');
            }

            $exists = TransportRateMaster::where('from_code', trim($request->from_code))
                ->where('to_code', trim($request->to_code))
                ->where('vendor_code', trim($request->vendor_code))
                ->where('mode', strtoupper(trim($request->mode)))
                ->where('validity_start_date', $request->validity_start_date)
                ->where('validity_end_date', $request->validity_end_date)
                ->where('id', '!=', $id)
                ->exists();

            if ($exists) {
                return redirect()->back()->withInput()->with('error', 'Rate already exists for this lane, vendor, mode and validity');
            }

            $rate->lane_name = $request->lane_name;
            $rate->from_code = $request->from_code;
            $rate->from_name = $fromPlant->plant_site_location_name ?? $fromPlant->plant_site_name ?? null;
            $rate->origin_region = $request->origin_region;
            $rate->to_name = $toPlant->plant_site_location_name ?? $toPlant->plant_site_name ?? null;
            $rate->to_code = $request->to_code;
            $rate->destination_region = $request->destination_region;
            $rate->vendor_code = $request->vendor_code;
            $rate->vendor_name = $request->vendor_name;
            $rate->mode = strtoupper(trim($request->mode));
			
            $rate->chargeable_weight = $request->chargeable_weight === null || $request->chargeable_weight === '' ? null : round((float) $request->chargeable_weight, 2);
            $rate->cft_value = $request->cft_value === null || $request->cft_value === '' ? null : round((float) $request->cft_value, 2);
            $rate->cft_weight = $request->cft_weight === null || $request->cft_weight === '' ? null : round((float) $request->cft_weight, 2);
            $rate->rate_logic = $request->rate_logic === null || $request->rate_logic === '' ? null : round((float) $request->rate_logic, 2);
            $rate->pickup_charges = $request->pickup_charges === null || $request->pickup_charges === '' ? null : round((float) $request->pickup_charges, 2);
            $rate->delivery_charges = $request->delivery_charges === null || $request->delivery_charges === '' ? null : round((float) $request->delivery_charges, 2);
            $rate->detention_rate = $request->detention_rate === null || $request->detention_rate === '' ? null : round((float) $request->detention_rate, 2);
            $rate->fov_on_base_rate = $request->fov_on_base_rate === null || $request->fov_on_base_rate === '' ? null : round((float) $request->fov_on_base_rate, 2);
            $rate->fuel_surcharge_on_base_rate = $request->fuel_surcharge_on_base_rate === null || $request->fuel_surcharge_on_base_rate === '' ? null : round((float) $request->fuel_surcharge_on_base_rate, 2);
            $rate->oda = $request->oda === null || $request->oda === '' ? null : round((float) $request->oda, 2);
            $rate->oda_1 = $request->oda_1 === null || $request->oda_1 === '' ? null : round((float) $request->oda_1, 2);
            $rate->oda_2 = $request->oda_2 === null || $request->oda_2 === '' ? null : round((float) $request->oda_2, 2);
            $rate->minimum_chargeable_weight_case = $request->minimum_chargeable_weight_case === null || $request->minimum_chargeable_weight_case === '' ? null : round((float) $request->minimum_chargeable_weight_case, 2);
            $rate->validity_start_date = $request->validity_start_date;
            $rate->validity_end_date = $request->validity_end_date;
            $rate->docket_charges = $request->docket_charges === null || $request->docket_charges === '' ? null : round((float) $request->docket_charges, 2);
            $rate->custom = $request->custom;
            $rate->custom_1 = $request->custom_1;
            $rate->custom_2 = $request->custom_2;
            $rate->custom_3 = $request->custom_3;
            $rate->custom_4 = $request->custom_4;
            $rate->updated_at = now();
            $rate->save();

            return redirect()->route('admin.transport-rate-master.list')
                ->with('success', 'Transport Rate Master updated successfully.');

        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Update Error: ' . $e->getMessage());
        }
    }

    /* DELETE TRANSPORT RATE */
    public function delete(Request $request, $id)
    {
        if (!$this->isAdminUser()) {
            return response()->json([
                'status' => false,
                'message' => 'You are not authorized to delete Transport Rate Master.'
            ], 403);
        }
        try {
            $rate = TransportRateMaster::findOrFail($id);
            $rate->delete();

            return response()->json([
                'status' => true,
                'message' => 'Transport Rate Master deleted successfully.'
            ]);

        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function destroy(Request $request, $id)
    {
        return $this->delete($request, $id);
    }

    private function isAdminUser()
    {
        $user = Auth::user();
        if (!$user) {
            return false;
        }

        return (int) $user->role_id === self::ADMIN_ROLE_ID;
    }

    /* Convert Excel numeric dates and standard date strings to Y-m-d */
    private function formatExcelDate($value)
    {
        if ($value === null || trim((string) $value) === '') {
            return null;
        }
        try {
            if (is_numeric($value)) {
                return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value)->format('Y-m-d');
            }

            return \Carbon\Carbon::parse($value)->format('Y-m-d');

        } catch (\Exception $e) {
            return null;
        }
    }
}

