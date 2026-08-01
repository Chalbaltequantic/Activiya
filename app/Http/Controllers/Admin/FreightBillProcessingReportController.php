<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use Throwable;

class FreightBillProcessingReportController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Database Table
    |--------------------------------------------------------------------------
    */

    private string $tableName = 'bill_data_upload';


    /*
    |--------------------------------------------------------------------------
    | Roles Allowed to View All Vendors
    |--------------------------------------------------------------------------
    |
    | Role 1 = Super Admin / Admin
    | Role 4 = Accounts
    |
    | All other users are restricted using:
    |
    | admins.vendor_code = bill_data_upload.vendor_code
    |
    */

    private array $allVendorAccessRoleIds = [
        1,
        4,
    ];


    /*
    |--------------------------------------------------------------------------
    | Constructor
    |--------------------------------------------------------------------------
    */

    public function __construct()
    {
        $this->middleware('auth:admin');
    }


    /*
    |--------------------------------------------------------------------------
    | Report Dashboard
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $request->validate([
            'mode'      => ['nullable', 'string', 'max:100'],
            'vendor'    => ['nullable', 'string', 'max:100'],
            'plant'     => ['nullable', 'string', 'max:100'],
            'from_date' => ['nullable', 'date'],
            'to_date'   => ['nullable', 'date', 'after_or_equal:from_date'],
        ]);

        $title = 'Freight Bill Processing Dashboard';
        $pagetitle = $title;

        $columns = $this->resolveColumns();

        if (!$columns['created_at']) {
            return redirect()
                ->back()
                ->with(
                    'error',
                    'created_at or created_date column was not found in bill_data_upload.'
                );
        }

        $report = $this->buildReport(
            $request,
            $columns
        );

        /*
        |--------------------------------------------------------------------------
        | Filter Options
        |--------------------------------------------------------------------------
        |
        | Mode and plant options are restricted for vendor users.
        |
        */

        $modes = $this->getSecuredFilterOptions(
            $columns['mode'],
            $columns
        );

        $plants = $this->getSecuredFilterOptions(
            $columns['plant'],
            $columns
        );

        /*
        |--------------------------------------------------------------------------
        | Vendor Dropdown
        |--------------------------------------------------------------------------
        |
        | Only Admin and Accounts users receive the complete vendor dropdown.
        |
        */

        $canViewAllVendors = $this->canViewAllVendors();

        if ($canViewAllVendors) {
            $vendors = $this->getVendorFilterOptions(
                $columns
            );
        } else {
            $vendors = collect();
        }

        $loggedInVendorCode = $this->getLoggedInVendorCode();

        $loggedInVendorName = $this->getLoggedInVendorName(
            $columns
        );

        return view(
            'admin.freight_bill_processing.dashboard',
            compact(
                'title',
                'pagetitle',
                'report',
                'modes',
                'vendors',
                'plants',
                'columns',
                'canViewAllVendors',
                'loggedInVendorCode',
                'loggedInVendorName'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Download Report in XLS Format
    |--------------------------------------------------------------------------
    */

    public function exportXls(Request $request)
    {
        $request->validate([
            'mode'      => ['nullable', 'string', 'max:100'],
            'vendor'    => ['nullable', 'string', 'max:100'],
            'plant'     => ['nullable', 'string', 'max:100'],
            'from_date' => ['nullable', 'date'],
            'to_date'   => ['nullable', 'date', 'after_or_equal:from_date'],
        ]);

        $columns = $this->resolveColumns();

        if (!$columns['created_at']) {
            return redirect()
                ->back()
                ->with(
                    'error',
                    'created_at or created_date column was not found in bill_data_upload.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | The Same Vendor Restriction Is Applied to XLS
        |--------------------------------------------------------------------------
        */

        $report = $this->buildReport(
            $request,
            $columns
        );

        $spreadsheet = new Spreadsheet();

        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setTitle('Freight Dashboard');


        /*
        |--------------------------------------------------------------------------
        | Report Heading
        |--------------------------------------------------------------------------
        */

        $sheet->setCellValue(
            'A1',
            'Freight Bill Processing Dashboard'
        );

        $sheet->mergeCells('A1:I1');


        /*
        |--------------------------------------------------------------------------
        | Summary
        |--------------------------------------------------------------------------
        */

        $sheet->setCellValue('A3', 'Total Shipments');
        $sheet->setCellValue('B3', 'Count');
        $sheet->setCellValue('C3', 'Value');

        $sheet->setCellValue(
            'B4',
            $report['total_count']
        );

        $sheet->setCellValue(
            'C4',
            $report['total_value']
        );

        $sheet->setCellValue('A5', 'Shipments Mode');
        $sheet->setCellValue('B5', 'Count');
        $sheet->setCellValue('C5', 'Value');

        $sheet->setCellValue(
            'B6',
            $report['mode_count']
        );

        $sheet->setCellValue(
            'C6',
            $report['mode_value']
        );


        /*
        |--------------------------------------------------------------------------
        | Applied Filters
        |--------------------------------------------------------------------------
        */

        $sheet->setCellValue('A8', 'Mode');

        $sheet->setCellValue(
            'B8',
            $request->input('mode') ?: 'All Modes'
        );

        $sheet->setCellValue('A9', 'Vendor');

        if ($this->canViewAllVendors()) {
            $selectedVendor = $request->input('vendor')
                ?: 'All Vendors';
        } else {
            $selectedVendor = $this->getLoggedInVendorName($columns)
                ?: $this->getLoggedInVendorCode()
                ?: 'Vendor';
        }

        $sheet->setCellValue(
            'B9',
            $selectedVendor
        );

        $sheet->setCellValue('A10', 'Plant');

        $sheet->setCellValue(
            'B10',
            $request->input('plant') ?: 'All Plants'
        );

        $sheet->setCellValue('A11', 'From Date');

        $sheet->setCellValue(
            'B11',
            $request->input('from_date') ?: ''
        );

        $sheet->setCellValue('A12', 'To Date');

        $sheet->setCellValue(
            'B12',
            $request->input('to_date') ?: ''
        );


        /*
        |--------------------------------------------------------------------------
        | Count Matrix Header
        |--------------------------------------------------------------------------
        */

        $sheet->setCellValue('A14', 'Count');

        $columnLetter = 'B';

        foreach ($report['buckets'] as $bucketLabel) {
            $sheet->setCellValue(
                $columnLetter . '14',
                $bucketLabel
            );

            $columnLetter++;
        }


        /*
        |--------------------------------------------------------------------------
        | Count Matrix Data
        |--------------------------------------------------------------------------
        */

        $statusLabels = $this->statusLabels();

        $rowNumber = 15;

        foreach ($statusLabels as $statusKey => $statusLabel) {
            $sheet->setCellValue(
                'A' . $rowNumber,
                $statusLabel
            );

            $columnLetter = 'B';

            foreach ($report['buckets'] as $bucketKey => $bucketLabel) {
                $sheet->setCellValue(
                    $columnLetter . $rowNumber,
                    $report['count_matrix'][$statusKey][$bucketKey]
                );

                $columnLetter++;
            }

            $rowNumber++;
        }


        /*
        |--------------------------------------------------------------------------
        | Value Matrix Header
        |--------------------------------------------------------------------------
        */

        $valueHeaderRow = 22;

        $sheet->setCellValue(
            'A' . $valueHeaderRow,
            'Value'
        );

        $columnLetter = 'B';

        foreach ($report['buckets'] as $bucketLabel) {
            $sheet->setCellValue(
                $columnLetter . $valueHeaderRow,
                $bucketLabel
            );

            $columnLetter++;
        }


        /*
        |--------------------------------------------------------------------------
        | Value Matrix Data
        |--------------------------------------------------------------------------
        */

        $rowNumber = 23;

        foreach ($statusLabels as $statusKey => $statusLabel) {
            $sheet->setCellValue(
                'A' . $rowNumber,
                $statusLabel
            );

            $columnLetter = 'B';

            foreach ($report['buckets'] as $bucketKey => $bucketLabel) {
                $sheet->setCellValue(
                    $columnLetter . $rowNumber,
                    $report['value_matrix'][$statusKey][$bucketKey]
                );

                $columnLetter++;
            }

            $rowNumber++;
        }


        /*
        |--------------------------------------------------------------------------
        | Footer Note
        |--------------------------------------------------------------------------
        */

        $sheet->setCellValue(
            'A30',
            'Pending: created_at | Received: freight_info_updated_at | ' .
            'Validated: validated_at | Returned: returned_at'
        );

        $sheet->mergeCells('A30:I30');


        /*
        |--------------------------------------------------------------------------
        | XLS Styling
        |--------------------------------------------------------------------------
        */

        $sheet->getStyle('A1:I1')
            ->getFont()
            ->setBold(true)
            ->setSize(14);

        $sheet->getStyle('A1:I1')
            ->getAlignment()
            ->setHorizontal(
                Alignment::HORIZONTAL_CENTER
            );

        $sheet->getStyle('A14:I14')
            ->getFont()
            ->setBold(true);

        $sheet->getStyle('A22:I22')
            ->getFont()
            ->setBold(true);

        $sheet->getStyle('A14:I14')
            ->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()
            ->setARGB('FCE4D6');

        $sheet->getStyle('A22:I22')
            ->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()
            ->setARGB('FCE4D6');

        $sheet->getStyle('A1:I30')
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN);

        $sheet->getStyle('C4:C6')
            ->getNumberFormat()
            ->setFormatCode('#,##0.00');

        $sheet->getStyle('B23:I27')
            ->getNumberFormat()
            ->setFormatCode('#,##0.00');

        foreach (range('A', 'I') as $column) {
            $sheet->getColumnDimension($column)
                ->setAutoSize(true);
        }


        /*
        |--------------------------------------------------------------------------
        | Download File
        |--------------------------------------------------------------------------
        */

        $filename = 'freight-bill-processing-report-'
            . now()->format('Y-m-d-His')
            . '.xls';

        $temporaryFile = storage_path(
            'app/' . $filename
        );

        $writer = new Xls(
            $spreadsheet
        );

        $writer->save(
            $temporaryFile
        );

        return response()
            ->download(
                $temporaryFile,
                $filename
            )
            ->deleteFileAfterSend(true);
    }


    /*
    |--------------------------------------------------------------------------
    | Build Complete Report
    |--------------------------------------------------------------------------
    */

    private function buildReport(
        Request $request,
        array $columns
    ): array {
        $buckets = $this->ageingBuckets();

        $statusLabels = $this->statusLabels();

        $countMatrix = [];

        $valueMatrix = [];

        /*
        |--------------------------------------------------------------------------
        | Initialise All Cells with Zero
        |--------------------------------------------------------------------------
        */

        foreach ($statusLabels as $statusKey => $statusLabel) {
            foreach ($buckets as $bucketKey => $bucketLabel) {
                $countMatrix[$statusKey][$bucketKey] = 0;

                $valueMatrix[$statusKey][$bucketKey] = 0;
            }
        }


        /*
        |--------------------------------------------------------------------------
        | Build Report Query
        |--------------------------------------------------------------------------
        */

        $query = DB::table(
            $this->tableName
        );

        /*
        |--------------------------------------------------------------------------
        | Secure Vendor Restriction
        |--------------------------------------------------------------------------
        |
        | Vendor users automatically receive:
        |
        | WHERE bill_data_upload.vendor_code = admins.vendor_code
        |
        */

        $this->applyLoggedInVendorRestriction(
            $query,
            $columns
        );


        /*
        |--------------------------------------------------------------------------
        | Admin/Accounts Vendor Filter
        |--------------------------------------------------------------------------
        */

        if (
            $this->canViewAllVendors() &&
            $request->filled('vendor') &&
            $columns['vendor_code']
        ) {
            $query->where(
                $columns['vendor_code'],
                trim((string) $request->input('vendor'))
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Mode Filter
        |--------------------------------------------------------------------------
        */

        if (
            $request->filled('mode') &&
            $columns['mode']
        ) {
            $query->where(
                $columns['mode'],
                $request->input('mode')
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Plant Filter
        |--------------------------------------------------------------------------
        */

        if (
            $request->filled('plant') &&
            $columns['plant']
        ) {
            $query->where(
                $columns['plant'],
                $request->input('plant')
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Select Only Required Fields
        |--------------------------------------------------------------------------
        */

        $selectColumns = [
            'id',
        ];

        $selectColumns[] = $columns['created_at']
            ? $columns['created_at'] . ' as report_created_at'
            : DB::raw('NULL as report_created_at');

        $selectColumns[] = $columns['received_at']
            ? $columns['received_at'] . ' as report_received_at'
            : DB::raw('NULL as report_received_at');

        $selectColumns[] = $columns['validated_at']
            ? $columns['validated_at'] . ' as report_validated_at'
            : DB::raw('NULL as report_validated_at');

        $selectColumns[] = $columns['returned_at']
            ? $columns['returned_at'] . ' as report_returned_at'
            : DB::raw('NULL as report_returned_at');

        $selectColumns[] = $columns['paid_at']
            ? $columns['paid_at'] . ' as report_paid_at'
            : DB::raw('NULL as report_paid_at');

        $selectColumns[] = $columns['value']
            ? $columns['value'] . ' as report_value'
            : DB::raw('0 as report_value');

        $selectColumns[] = $columns['mode']
            ? $columns['mode'] . ' as report_mode'
            : DB::raw('NULL as report_mode');

        $query->select(
            $selectColumns
        );


        /*
        |--------------------------------------------------------------------------
        | Summary Totals
        |--------------------------------------------------------------------------
        */

        $totalCount = 0;
        $totalValue = 0;

        $modeCount = 0;
        $modeValue = 0;


        /*
        |--------------------------------------------------------------------------
        | Process Records
        |--------------------------------------------------------------------------
        |
        | cursor() keeps memory usage low when the table contains many rows.
        |
        */

        foreach (
            $query->orderBy('id', 'asc')->cursor()
            as $row
        ) {
            $amount = $this->cleanAmount(
                $row->report_value ?? 0
            );

            $totalCount++;

            $totalValue += $amount;


            /*
            |--------------------------------------------------------------------------
            | Mode Summary
            |--------------------------------------------------------------------------
            */

            $mode = trim(
                (string) ($row->report_mode ?? '')
            );

            if ($mode !== '') {
                $modeCount++;

                $modeValue += $amount;
            }


            /*
            |--------------------------------------------------------------------------
            | Workflow Dates
            |--------------------------------------------------------------------------
            */

            $createdAt = $row->report_created_at ?? null;

            $receivedAt = $row->report_received_at ?? null;

            $validatedAt = $row->report_validated_at ?? null;

            $returnedAt = $row->report_returned_at ?? null;

            $paidAt = $row->report_paid_at ?? null;


            /*
            |--------------------------------------------------------------------------
            | Pending Invoice
            |--------------------------------------------------------------------------
            |
            | A shipment is pending only while freight invoice information has
            | not been submitted.
            |
            | Pending ageing starts from created_at.
            |
            */

            if (
                empty($receivedAt) &&
                !empty($createdAt)
            ) {
                $this->addToAgeingMatrix(
                    'pending',
                    $createdAt,
                    $amount,
                    $countMatrix,
                    $valueMatrix,
                    $request
                );
            }


            /*
            |--------------------------------------------------------------------------
            | Invoice Received
            |--------------------------------------------------------------------------
            |
            | Invoice receiving/submission date:
            |
            | freight_info_updated_at
            |
            */

            if (!empty($receivedAt)) {
                $this->addToAgeingMatrix(
                    'received',
                    $receivedAt,
                    $amount,
                    $countMatrix,
                    $valueMatrix,
                    $request
                );
            }


            /*
            |--------------------------------------------------------------------------
            | Invoice Validated
            |--------------------------------------------------------------------------
            |
            | Validation ageing starts from validated_at.
            |
            */

            if (!empty($validatedAt)) {
                $this->addToAgeingMatrix(
                    'validated',
                    $validatedAt,
                    $amount,
                    $countMatrix,
                    $valueMatrix,
                    $request
                );
            }


            /*
            |--------------------------------------------------------------------------
            | Invoice Returned
            |--------------------------------------------------------------------------
            |
            | Return ageing starts from returned_at.
            |
            */

            if (!empty($returnedAt)) {
                $this->addToAgeingMatrix(
                    'returned',
                    $returnedAt,
                    $amount,
                    $countMatrix,
                    $valueMatrix,
                    $request
                );
            }


            /*
            |--------------------------------------------------------------------------
            | Invoice Paid
            |--------------------------------------------------------------------------
            |
            | This is counted only when a paid date field exists and contains
            | a value.
            |
            */

            if (!empty($paidAt)) {
                $this->addToAgeingMatrix(
                    'paid',
                    $paidAt,
                    $amount,
                    $countMatrix,
                    $valueMatrix,
                    $request
                );
            }
        }


        return [
            'buckets'      => $buckets,
            'count_matrix' => $countMatrix,
            'value_matrix' => $valueMatrix,

            'total_count'  => $totalCount,
            'total_value'  => $totalValue,

            'mode_count'   => $modeCount,
            'mode_value'   => $modeValue,
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | Add One Record to Ageing Matrix
    |--------------------------------------------------------------------------
    */

    private function addToAgeingMatrix(
        string $status,
        mixed $activityDate,
        float $amount,
        array &$countMatrix,
        array &$valueMatrix,
        Request $request
    ): void {
        if (empty($activityDate)) {
            return;
        }

        try {
            $eventDate = Carbon::parse(
                $activityDate
            )->startOfDay();
        } catch (Throwable $exception) {
            return;
        }


        /*
        |--------------------------------------------------------------------------
        | Apply From Date Filter
        |--------------------------------------------------------------------------
        |
        | The filter is applied against each status's actual activity date.
        |
        */

        if ($request->filled('from_date')) {
            $fromDate = Carbon::parse(
                $request->input('from_date')
            )->startOfDay();

            if ($eventDate->lt($fromDate)) {
                return;
            }
        }


        /*
        |--------------------------------------------------------------------------
        | Apply To Date Filter
        |--------------------------------------------------------------------------
        */

        if ($request->filled('to_date')) {
            $toDate = Carbon::parse(
                $request->input('to_date')
            )->endOfDay();

            if ($eventDate->gt($toDate)) {
                return;
            }
        }


        /*
        |--------------------------------------------------------------------------
        | Calculate Age in Days
        |--------------------------------------------------------------------------
        */

        $today = now()->startOfDay();

        $ageInDays = $eventDate->diffInDays(
            $today,
            false
        );

        /*
         * Future dates are treated as zero days old.
         */

        if ($ageInDays < 0) {
            $ageInDays = 0;
        }

        $bucketKey = $this->getAgeBucketKey(
            $ageInDays
        );

        /*
         * Records older than 180 days are excluded from the matrix.
         */

        if ($bucketKey === null) {
            return;
        }

        $countMatrix[$status][$bucketKey]++;

        $valueMatrix[$status][$bucketKey] += $amount;
    }


    /*
    |--------------------------------------------------------------------------
    | Ageing Buckets
    |--------------------------------------------------------------------------
    */

    private function ageingBuckets(): array
    {
        return [
            '0_15'    => '0-15 Days',
            '16_30'   => '16-30 Days',
            '31_45'   => '31-45 Days',
            '46_60'   => '46-60 Days',
            '61_90'   => '61-90 Days',
            '91_120'  => '91-120 Days',
            '121_150' => '121-150 Days',
            '151_180' => '151-180 Days',
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | Report Status Labels
    |--------------------------------------------------------------------------
    */

    private function statusLabels(): array
    {
        return [
            'received'  => 'Invoice Received',
            'validated' => 'Invoice Validated',
            'returned'  => 'Invoice Returned',
            'pending'   => 'Invoices Pending',
            'paid'      => 'Invoices Paid',
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | Return Ageing Bucket Key
    |--------------------------------------------------------------------------
    */

    private function getAgeBucketKey(
        int $ageInDays
    ): ?string {
        if ($ageInDays <= 15) {
            return '0_15';
        }

        if ($ageInDays <= 30) {
            return '16_30';
        }

        if ($ageInDays <= 45) {
            return '31_45';
        }

        if ($ageInDays <= 60) {
            return '46_60';
        }

        if ($ageInDays <= 90) {
            return '61_90';
        }

        if ($ageInDays <= 120) {
            return '91_120';
        }

        if ($ageInDays <= 150) {
            return '121_150';
        }

        if ($ageInDays <= 180) {
            return '151_180';
        }

        return null;
    }


    /*
    |--------------------------------------------------------------------------
    | Resolve Actual Database Columns
    |--------------------------------------------------------------------------
    */

    private function resolveColumns(): array
    {
        return [
            /*
            |--------------------------------------------------------------------------
            | Workflow Dates
            |--------------------------------------------------------------------------
            */

            'created_at' => $this->firstExistingColumn([
                'created_at',
                'created_date',
            ]),

            'received_at' => $this->firstExistingColumn([
                'freight_info_updated_at',
            ]),

            'validated_at' => $this->firstExistingColumn([
                'validated_at',
            ]),

            'returned_at' => $this->firstExistingColumn([
                'returned_at',
                'return_at',
            ]),

            'paid_at' => $this->firstExistingColumn([
                'paid_at',
                'payment_date',
                'payment_verified_at',
            ]),


            /*
            |--------------------------------------------------------------------------
            | Report Value
            |--------------------------------------------------------------------------
            */

            'value' => $this->firstExistingColumn([
                'freight_amount',
            ]),


            /*
            |--------------------------------------------------------------------------
            | Filters
            |--------------------------------------------------------------------------
            */

            'mode' => $this->firstExistingColumn([
                'freight_type',
                'shipment_mode',
                'mode',
                'transport_mode',
            ]),

            'vendor_code' => $this->firstExistingColumn([
                'vendor_code',
            ]),

            'vendor_name' => $this->firstExistingColumn([
                'vendor_name',
            ]),

            'plant' => $this->firstExistingColumn([
                'consignee_code',
                'plant_code',
                'storage_plant_code',
            ]),
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | Return First Existing Column
    |--------------------------------------------------------------------------
    */

    private function firstExistingColumn(
        array $columnNames
    ): ?string {
        foreach ($columnNames as $columnName) {
            if (
                Schema::hasColumn(
                    $this->tableName,
                    $columnName
                )
            ) {
                return $columnName;
            }
        }

        return null;
    }


    /*
    |--------------------------------------------------------------------------
    | Get Logged-In Admin User
    |--------------------------------------------------------------------------
    */

    private function getLoggedInUser()
    {
        return Auth::guard('admin')->user();
    }


    /*
    |--------------------------------------------------------------------------
    | Check Whether User Can View All Vendors
    |--------------------------------------------------------------------------
    */

    private function canViewAllVendors(): bool
    {
        $user = $this->getLoggedInUser();

        if (!$user) {
            return false;
        }

        return in_array(
            (int) $user->role_id,
            $this->allVendorAccessRoleIds,
            true
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Get Vendor Code from Admin Login Session
    |--------------------------------------------------------------------------
    */

    private function getLoggedInVendorCode(): ?string
    {
        $user = $this->getLoggedInUser();

        if (!$user) {
            return null;
        }

        $vendorCode = trim(
            (string) ($user->vendor_code ?? '')
        );

        if ($vendorCode === '') {
            return null;
        }

        return $vendorCode;
    }


    /*
    |--------------------------------------------------------------------------
    | Apply Logged-In Vendor Restriction
    |--------------------------------------------------------------------------
    |
    | For vendor login:
    |
    | admins.vendor_code = bill_data_upload.vendor_code
    |
    */

    private function applyLoggedInVendorRestriction(
        Builder $query,
        array $columns
    ): void {
        /*
         * Admin and Accounts users may view all vendors.
         */

        if ($this->canViewAllVendors()) {
            return;
        }

        $loggedInVendorCode = $this->getLoggedInVendorCode();

        if (!$loggedInVendorCode) {
            abort(
                403,
                'Vendor code is not mapped with the logged-in account.'
            );
        }

        if (!$columns['vendor_code']) {
            abort(
                500,
                'vendor_code column was not found in bill_data_upload.'
            );
        }

        $query->where(
            $columns['vendor_code'],
            $loggedInVendorCode
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Get Secured Mode or Plant Options
    |--------------------------------------------------------------------------
    */

    private function getSecuredFilterOptions(
        ?string $columnName,
        array $columns
    ): Collection {
        if (!$columnName) {
            return collect();
        }

        $query = DB::table(
            $this->tableName
        )
            ->whereNotNull($columnName)
            ->where($columnName, '<>', '');

        /*
         * Vendor sees options from only his own records.
         */

        $this->applyLoggedInVendorRestriction(
            $query,
            $columns
        );

        return $query
            ->select($columnName)
            ->distinct()
            ->orderBy($columnName)
            ->pluck($columnName);
    }


    /* Get Vendor Dropdown Options| This method is used only for Admin and Accounts users.
        */

    private function getVendorFilterOptions(
        array $columns
    ): Collection {
        if (!$columns['vendor_code']) {
            return collect();
        }

        $query = DB::table(
            $this->tableName
        )
            ->whereNotNull(
                $columns['vendor_code']
            )
            ->where(
                $columns['vendor_code'],
                '<>',
                ''
            );

        if ($columns['vendor_name']) {
            return $query
                ->select([
                    $columns['vendor_code']
                        . ' as vendor_code',

                    $columns['vendor_name']
                        . ' as vendor_name',
                ])
                ->distinct()
                ->orderBy(
                    $columns['vendor_name']
                )
                ->get();
        }

        return $query
            ->select([
                $columns['vendor_code']
                    . ' as vendor_code',

                DB::raw(
                    $columns['vendor_code']
                    . ' as vendor_name'
                ),
            ])
            ->distinct()
            ->orderBy(
                $columns['vendor_code']
            )
            ->get();
    }


    /* Get Logged-In Vendor Name*/

    private function getLoggedInVendorName(
        array $columns
    ): ?string {
        if ($this->canViewAllVendors()) {
            return null;
        }

        $vendorCode = $this->getLoggedInVendorCode();

        if (
            !$vendorCode ||
            !$columns['vendor_code']
        ) {
            return null;
        }

        if (!$columns['vendor_name']) {
            return $vendorCode;
        }

        $vendorName = DB::table(
            $this->tableName
        )
            ->where(
                $columns['vendor_code'],
                $vendorCode
            )
            ->whereNotNull(
                $columns['vendor_name']
            )
            ->where(
                $columns['vendor_name'],
                '<>',
                ''
            )
            ->value(
                $columns['vendor_name']
            );

        return $vendorName ?: $vendorCode;
    }


    /*
    |--------------------------------------------------------------------------
    | Convert Amount to Numeric Value
    |--------------------------------------------------------------------------
    */

    private function cleanAmount(
        mixed $amount
    ): float {
        if (
            $amount === null ||
            trim((string) $amount) === ''
        ) {
            return 0;
        }

        $cleanedAmount = preg_replace(
            '/[^0-9.\-]/',
            '',
            (string) $amount
        );

        if (!is_numeric($cleanedAmount)) {
            return 0;
        }

        return (float) $cleanedAmount;
    }
}