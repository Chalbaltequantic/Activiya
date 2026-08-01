<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xls;

class FreightBillProcessingReportController extends Controller
{
    private string $table = 'bill_data_upload';

    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    /*
    |--------------------------------------------------------------------------
    | Freight Bill Processing Dashboard
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $title = 'Freight Bill Processing Dashboard';
        $pagetitle = $title;

        $columns = $this->resolveReportColumns();

        if ($columns['date'] === null) {
            return back()->with(
                'error',
                'Invoice receiving date column was not found in bill_data_upload.'
            );
        }

        $report = $this->prepareReport($request, $columns);

        $modes = $this->getFilterOptions($columns['mode']);
        $vendors = $this->getFilterOptions($columns['vendor']);
        $plants = $this->getFilterOptions($columns['plant']);

        return view(
            'admin.freight_bill_processing.dashboard',
            compact(
                'title',
                'pagetitle',
                'report',
                'modes',
                'vendors',
                'plants',
                'columns'
            )
        );
    }

    /* Download Dashboard as XLS  */

    public function exportXls(Request $request)
    {
        $columns = $this->resolveReportColumns();

        if ($columns['date'] === null) {
            return back()->with(
                'error',
                'Invoice receiving date column was not found in bill_data_upload.'
            );
        }

        $report = $this->prepareReport($request, $columns);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setTitle('Freight Dashboard');

        $sheet->setCellValue('A1', 'Freight Bill Processing Dashboard');
        $sheet->mergeCells('A1:I1');

        $sheet->setCellValue('A3', 'Total Shipments');
        $sheet->setCellValue('B3', 'Count');
        $sheet->setCellValue('C3', 'Value');
        $sheet->setCellValue('B4', $report['total_count']);
        $sheet->setCellValue('C4', $report['total_value']);

        $sheet->setCellValue('A5', 'Shipments Mode');
        $sheet->setCellValue('B5', 'Count');
        $sheet->setCellValue('C5', 'Value');
        $sheet->setCellValue('B6', $report['mode_count']);
        $sheet->setCellValue('C6', $report['mode_value']);

        $sheet->setCellValue('A8', 'Selected Mode');
        $sheet->setCellValue('B8', $request->mode ?: 'All');
        $sheet->setCellValue('A9', 'Selected Vendor');
        $sheet->setCellValue('B9', $request->vendor ?: 'All');
        $sheet->setCellValue('A10', 'Selected Plant');
        $sheet->setCellValue('B10', $request->plant ?: 'All');
        $sheet->setCellValue('A11', 'From Date');
        $sheet->setCellValue('B11', $request->from_date ?: '');
        $sheet->setCellValue('A12', 'To Date');
        $sheet->setCellValue('B12', $request->to_date ?: '');

        $headers = [
            'A14' => 'Count',
            'B14' => '0-15 Days',
            'C14' => '16-30 Days',
            'D14' => '31-45 Days',
            'E14' => '46-60 Days',
            'F14' => '61-90 Days',
            'G14' => '91-120 Days',
            'H14' => '121-150 Days',
            'I14' => '151-180 Days',
        ];

        foreach ($headers as $cell => $value) {
            $sheet->setCellValue($cell, $value);
        }

        $statusRows = [
            'received' => 'Invoice Received',
            'validated' => 'Invoice Validated',
            'returned' => 'Invoice Returned',
            'pending' => 'Invoices Pending',
            'paid' => 'Invoices Paid',
        ];

        $bucketKeys = array_keys($report['buckets']);

        $rowNumber = 15;

        foreach ($statusRows as $statusKey => $statusLabel) {
            $sheet->setCellValue('A' . $rowNumber, $statusLabel);

            $columnLetter = 'B';

            foreach ($bucketKeys as $bucketKey) {
                $sheet->setCellValue(
                    $columnLetter . $rowNumber,
                    $report['count_matrix'][$statusKey][$bucketKey]
                );

                $columnLetter++;
            }

            $rowNumber++;
        }

        $valueHeaderRow = 22;

        $sheet->setCellValue('A' . $valueHeaderRow, 'Value');

        $columnLetter = 'B';

        foreach ($report['buckets'] as $bucketLabel) {
            $sheet->setCellValue(
                $columnLetter . $valueHeaderRow,
                $bucketLabel
            );

            $columnLetter++;
        }

        $rowNumber = 23;

        foreach ($statusRows as $statusKey => $statusLabel) {
            $sheet->setCellValue('A' . $rowNumber, $statusLabel);

            $columnLetter = 'B';

            foreach ($bucketKeys as $bucketKey) {
                $sheet->setCellValue(
                    $columnLetter . $rowNumber,
                    $report['value_matrix'][$statusKey][$bucketKey]
                );

                $columnLetter++;
            }

            $rowNumber++;
        }

        $sheet->setCellValue(
            'A30',
            'Base date for counting is Invoice Receiving Date'
        );
        $sheet->mergeCells('A30:I30');

        $sheet->getStyle('A1:I1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A14:I14')->getFont()->setBold(true);
        $sheet->getStyle('A22:I22')->getFont()->setBold(true);

        $sheet->getStyle('A1:I30')->getBorders()->getAllBorders()->setBorderStyle(
            Border::BORDER_THIN
        );

        $sheet->getStyle('A1:I1')->getAlignment()->setHorizontal(
            Alignment::HORIZONTAL_CENTER
        );

        $sheet->getStyle('A14:I14')->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()
            ->setARGB('D9EAF7');

        $sheet->getStyle('A22:I22')->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()
            ->setARGB('D9EAF7');

        $sheet->getStyle('C4:C6')->getNumberFormat()->setFormatCode(
            '#,##0.00'
        );

        $sheet->getStyle('B23:I27')->getNumberFormat()->setFormatCode(
            '#,##0.00'
        );

        foreach (range('A', 'I') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        $filename =
            'freight-bill-processing-dashboard-' .
            now()->format('Y-m-d-His') .
            '.xls';

        $tempFile = storage_path('app/' . $filename);

        $writer = new Xls($spreadsheet);
        $writer->save($tempFile);

        return response()
            ->download($tempFile, $filename)
            ->deleteFileAfterSend(true);
    }

    /* Prepare Dashboard Report  */

    private function prepareReport(Request $request, array $columns): array
    {
        $buckets = [
            '0_15' => '0-15 Days',
            '16_30' => '16-30 Days',
            '31_45' => '31-45 Days',
            '46_60' => '46-60 Days',
            '61_90' => '61-90 Days',
            '91_120' => '91-120 Days',
            '121_150' => '121-150 Days',
            '151_180' => '151-180 Days',
        ];

        $statuses = [
            'received',
            'validated',
            'returned',
            'pending',
            'paid',
        ];

        $countMatrix = [];
        $valueMatrix = [];

        foreach ($statuses as $status) {
            foreach ($buckets as $bucketKey => $bucketLabel) {
                $countMatrix[$status][$bucketKey] = 0;
                $valueMatrix[$status][$bucketKey] = 0;
            }
        }

        $query = DB::table($this->table);

        $selectColumns = [
            $columns['date'] . ' as report_date',
        ];

        if ($columns['value']) {
            $selectColumns[] = $columns['value'] . ' as report_value';
        } else {
            $selectColumns[] = DB::raw('0 as report_value');
        }

        if ($columns['status']) {
            $selectColumns[] = $columns['status'] . ' as report_status';
        } else {
            $selectColumns[] = DB::raw("'pending' as report_status");
        }

        if ($columns['payment_status']) {
            $selectColumns[] =
                $columns['payment_status'] . ' as report_payment_status';
        } else {
            $selectColumns[] =
                DB::raw("NULL as report_payment_status");
        }

        if ($columns['validation_status']) {
            $selectColumns[] =
                $columns['validation_status'] . ' as report_validation_status';
        } else {
            $selectColumns[] =
                DB::raw("NULL as report_validation_status");
        }

        if ($columns['return_status']) {
            $selectColumns[] =
                $columns['return_status'] . ' as report_return_status';
        } else {
            $selectColumns[] =
                DB::raw("NULL as report_return_status");
        }

        if ($columns['mode']) {
            $selectColumns[] = $columns['mode'] . ' as report_mode';
        } else {
            $selectColumns[] = DB::raw("NULL as report_mode");
        }

        $query->select($selectColumns);

        if ($request->filled('mode') && $columns['mode']) {
            $query->where($columns['mode'], $request->mode);
        }

        if ($request->filled('vendor') && $columns['vendor']) {
            $query->where($columns['vendor'], $request->vendor);
        }

        if ($request->filled('plant') && $columns['plant']) {
            $query->where($columns['plant'], $request->plant);
        }

        if ($request->filled('from_date')) {
            $query->whereDate(
                $columns['date'],
                '>=',
                $request->from_date
            );
        }

        if ($request->filled('to_date')) {
            $query->whereDate(
                $columns['date'],
                '<=',
                $request->to_date
            );
        }

        $totalCount = 0;
        $totalValue = 0;

        $modeCount = 0;
        $modeValue = 0;

        foreach ($query->orderBy($columns['date'])->cursor() as $row) {
            if (empty($row->report_date)) {
                continue;
            }

            try {
                $receivedDate = Carbon::parse($row->report_date)->startOfDay();
            } catch (\Exception $exception) {
                continue;
            }

            $ageDays = $receivedDate->diffInDays(
                now()->startOfDay(),
                false
            );

            if ($ageDays < 0) {
                $ageDays = 0;
            }

            $amount = $this->cleanAmount($row->report_value ?? 0);

            $totalCount++;
            $totalValue += $amount;

            if (
                !$request->filled('mode') ||
                (string) ($row->report_mode ?? '') ===
                    (string) $request->mode
            ) {
                $modeCount++;
                $modeValue += $amount;
            }

            $bucketKey = $this->getAgeBucket($ageDays);

            if ($bucketKey === null) {
                continue;
            }

            $status = $this->resolveInvoiceStatus($row);

            $countMatrix[$status][$bucketKey]++;

            $valueMatrix[$status][$bucketKey] += $amount;
        }

        return [
            'buckets' => $buckets,
            'count_matrix' => $countMatrix,
            'value_matrix' => $valueMatrix,
            'total_count' => $totalCount,
            'total_value' => $totalValue,
            'mode_count' => $modeCount,
            'mode_value' => $modeValue,
        ];
    }

    /* Resolve Current Invoice Status
    | Priority:
    | Paid > Returned > Validated > Received > Pending
    
    */

    private function resolveInvoiceStatus(object $row): string
    {
        $paymentStatus = strtolower(
            trim((string) ($row->report_payment_status ?? ''))
        );

        $validationStatus = strtolower(
            trim((string) ($row->report_validation_status ?? ''))
        );

        $returnStatus = strtolower(
            trim((string) ($row->report_return_status ?? ''))
        );

        $status = strtolower(
            trim((string) ($row->report_status ?? ''))
        );

        $combined = implode(
            ' ',
            [
                $paymentStatus,
                $validationStatus,
                $returnStatus,
                $status,
            ]
        );

        if (
            str_contains($combined, 'paid') ||
            str_contains($combined, 'payment complete') ||
            str_contains($combined, 'payment_done')
        ) {
            return 'paid';
        }

        if (
            str_contains($combined, 'return') ||
            str_contains($combined, 'reject') ||
            str_contains($combined, 'sent back')
        ) {
            return 'returned';
        }

        if (
            str_contains($combined, 'validat') ||
            str_contains($combined, 'approv') ||
            str_contains($combined, 'verif')
        ) {
            return 'validated';
        }

        if (
            str_contains($combined, 'receiv') ||
            str_contains($combined, 'submit') ||
            str_contains($combined, 'upload')
        ) {
            return 'received';
        }

        return 'pending';
    }

    /* Ageing Bucket  */

    private function getAgeBucket(int $days): ?string
    {
        if ($days <= 15) {
            return '0_15';
        }

        if ($days <= 30) {
            return '16_30';
        }

        if ($days <= 45) {
            return '31_45';
        }

        if ($days <= 60) {
            return '46_60';
        }

        if ($days <= 90) {
            return '61_90';
        }

        if ($days <= 120) {
            return '91_120';
        }

        if ($days <= 150) {
            return '121_150';
        }

        if ($days <= 180) {
            return '151_180';
        }

        return null;
    }

    /*
    |--------------------------------------------------------------------------
    | Resolve bill_data_upload Column Names
    |--------------------------------------------------------------------------
    |
    | The method supports common column names already used in freight modules.
    | Put your exact column first in each candidate list when required.
    |
    */

    private function resolveReportColumns(): array
    {
        return [
            'date' => $this->firstExistingColumn([
                'invoice_receiving_date',
                'invoice_received_date',
                'invoice_receive_date',
                'received_date',
                'invoice_date',
                'inv_date',
                'created_at',
            ]),

            'value' => $this->firstExistingColumn([
                'invoice_amount',
                'invoice_value',
                'freight_amount',
                'bill_amount',
                'amount',
                'shipment_inv_value',
                'total_amount',
            ]),

            'status' => $this->firstExistingColumn([
                'invoice_status',
                'bill_status',
                'processing_status',
                'status',
            ]),

            'payment_status' => $this->firstExistingColumn([
                'payment_status',
                'payment_verified_status',
                'paid_status',
            ]),

            'validation_status' => $this->firstExistingColumn([
                'validation_status',
                'invoice_validation_status',
                'account_status',
                'verification_status',
            ]),

            'return_status' => $this->firstExistingColumn([
                'return_status',
                'invoice_return_status',
                'rejection_status',
            ]),

            'mode' => $this->firstExistingColumn([
                'shipment_mode',
                'mode',
                'transport_mode',
                'freight_mode',
            ]),

            'vendor' => $this->firstExistingColumn([
                'vendor_code',
                'vendor_name',
                'transporter_code',
                'transporter_name',
            ]),

            'plant' => $this->firstExistingColumn([
                'plant_code',
                'plant',
                'storage_plant_code',
                'consignee_code',
                'site_code',
            ]),
        ];
    }

    private function firstExistingColumn(array $columns): ?string
    {
        foreach ($columns as $column) {
            if (Schema::hasColumn($this->table, $column)) {
                return $column;
            }
        }

        return null;
    }

    /*
    |--------------------------------------------------------------------------
    | Dropdown Options
    |--------------------------------------------------------------------------
    */

    private function getFilterOptions(?string $column)
    {
        if ($column === null) {
            return collect();
        }

        return DB::table($this->table)
            ->whereNotNull($column)
            ->where($column, '!=', '')
            ->select($column)
            ->distinct()
            ->orderBy($column)
            ->pluck($column);
    }

    /*
    |--------------------------------------------------------------------------
    | Clean Amount
    |--------------------------------------------------------------------------
    */

    private function cleanAmount($value): float
    {
        if ($value === null || trim((string) $value) === '') {
            return 0;
        }

        $value = preg_replace(
            '/[^0-9.\-]/',
            '',
            (string) $value
        );

        return is_numeric($value)
            ? (float) $value
            : 0;
    }
}
