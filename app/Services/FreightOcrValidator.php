<?php

namespace App\Services;

use App\Models\Billdata;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class FreightOcrValidator
{
    private function value($value): string
    {
        return trim((string) ($value ?? ''));
    }

    private function normalized($value): string
    {
        return strtoupper(preg_replace('/\s+/', '', $this->value($value)));
    }

    private function lrNumber($value): string
    {
        $value = $this->normalized($value);
        return ctype_digit($value) ? (ltrim($value, '0') ?: '0') : $value;
    }

    private function amount($value): ?int
    {
        $value = preg_replace('/[^0-9.\-]/', '', $this->value($value));
        return is_numeric($value) ? (int) round(((float) $value) * 100) : null;
    }

    private function date($value): ?string
    {
        try {
            return $this->value($value) !== '' ? Carbon::parse($value)->format('Y-m-d') : null;
        } catch (\Throwable $e) {
            return null;
        }
    }

	public function duplicate($entry): ?string
	{
		$vendorCode = trim((string) $entry->vendor_code);
		$invoiceNo = trim((string) $entry->freight_invoice_no);

		if ($vendorCode === '' || $invoiceNo === '') {
			return null;
		}

		$duplicate = \Illuminate\Support\Facades\DB::table('bill_data_upload')
			->where('vendor_code', $vendorCode)
			->where('freight_invoice_no', $invoiceNo)
			->where('id', '!=', $entry->id)
			->first();

		if (!$duplicate) {
			return null;
		}

		return "Duplicate invoice {$invoiceNo} already exists for vendor {$vendorCode} under Bill ID {$duplicate->id}.";
	}
   /* public function duplicate(Billdata $bill, ?string $invoiceNumber = null): ?string
    {
        $number = $this->value($invoiceNumber ?? $bill->freight_invoice_no);
        $vendor = $this->value($bill->vendor_code);
        if ($number === '' || $vendor === '') {
            return null;
        }

        $other = Billdata::query()
            ->where('vendor_code', $vendor)
            ->whereRaw('UPPER(TRIM(freight_invoice_no)) = ?', [strtoupper($number)])
            ->where('id', '<>', $bill->id)
            ->orderBy('id')
            ->first(['id']);

        return $other
            ? "Duplicate invoice: vendor {$vendor}, invoice {$number} already exists under Bill ID {$other->id}"
            : null;
    }*/

    private function gstinRemark(string $label, $expected, $actual): ?string
    {
        $expected = $this->normalized($expected);
        $actual = $this->normalized($actual);
        if ($expected === '') {
            return "Advisory: {$label} GSTIN missing in master";
        }
        if ($actual === '') {
            return "Advisory: {$label} GSTIN not extracted from invoice";
        }
        return $expected !== $actual
            ? "Advisory: {$label} GSTIN mismatch: master {$expected}, OCR {$actual}"
            : null;
    }

    public function check(Billdata $bill, array $data): array
    {
        $errors = [];
        $review = [];
        $advisory = [];
        $invoice = is_array($data['invoice'] ?? null) ? $data['invoice'] : [];
        $lrMatch = is_array($data['lr_number_match'] ?? null) ? $data['lr_number_match'] : [];
        $lr = is_array($data['lr'] ?? null) ? $data['lr'] : [];

        // Duplicate invoices are blocking; check the saved invoice number and OCR number.
        foreach (array_unique(array_filter([
            $this->value($bill->freight_invoice_no),
            $this->value($invoice['invoice_number'] ?? null),
        ])) as $number) {
            $duplicate = $this->duplicate($bill, $number);
            if ($duplicate) {
                $errors[] = $duplicate;
            }
        }

        if (empty($data['success'])) {
            $review[] = 'Invoice text could not be read';
        }

        $checks = [
            ['Invoice number', $bill->freight_invoice_no, $invoice['invoice_number'] ?? null, 'text'],
            ['LR number', $bill->lr_no, $invoice['lr_number'] ?? null, 'lr'],
            ['Freight PO', $bill->ref2, $invoice['po_number'] ?? null, 'text'],
            ['Invoice date', $bill->freight_invoice_date, $invoice['invoice_date'] ?? null, 'date'],
        ];

        foreach ($checks as [$label, $expected, $actual, $type]) {
            if ($this->value($expected) === '') {
                continue;
            }
            if ($this->value($actual) === '') {
                $review[] = "{$label} not extracted";
                continue;
            }
            $left = $type === 'date' ? $this->date($expected) : ($type === 'lr' ? $this->lrNumber($expected) : $this->normalized($expected));
            $right = $type === 'date' ? $this->date($actual) : ($type === 'lr' ? $this->lrNumber($actual) : $this->normalized($actual));
            if ($left === null || $right === null) {
                $review[] = "{$label} date could not be parsed";
            } elseif ($left !== $right) {
                $errors[] = "{$label} mismatch: database {$this->value($expected)}, OCR {$this->value($actual)}";
            }
        }

        $enteredAmount = $this->amount($bill->freight_amount);
        $ocrAmount = $this->amount($invoice['amount'] ?? null);
        if ($enteredAmount === null) {
            $review[] = 'Entered freight amount missing';
        } elseif ($ocrAmount === null) {
            $review[] = 'Invoice amount not extracted';
        } elseif ($enteredAmount !== $ocrAmount) {
            $errors[] = 'Amount mismatch: entered '.number_format($enteredAmount / 100, 2).' vs OCR '.number_format($ocrAmount / 100, 2);
        }

        $billAmount = $this->amount($bill->a_amount);
        if ($billAmount !== null && $enteredAmount !== null && $billAmount !== $enteredAmount) {
            $errors[] = 'Freight amount does not match bill amount';
        }

        $invoiceLr = $invoice['lr_number'] ?? $lrMatch['invoice_lr_number'] ?? null;
        $scannedLr = $lr['serial_number'] ?? $lr['lr_number'] ?? $lr['lr_serial_number'] ?? $lrMatch['page_2_serial_number'] ?? null;
        if ($this->value($invoiceLr) !== '' && $this->value($scannedLr) !== '') {
            if ($this->lrNumber($invoiceLr) !== $this->lrNumber($scannedLr)) {
                $errors[] = "Invoice LR {$invoiceLr} differs from scanned LR {$scannedLr}";
            }
        } else {
            $review[] = 'Scanned LR serial number not independently verified';
        }

        // ADHOC approval is intentionally NOT checked by OCR.
        if (!$bill->pod_file) {
            $review[] = 'POD file missing';
        }
        if (($data['invoice_digital_signature']['cryptographic_status'] ?? 'not_verified') !== 'verified') {
            $review[] = 'Invoice digital signature not cryptographically verified';
        }

        // GSTIN checks are advisory only: never add them to errors or review.
        $vendorGstin = DB::table('vendors')->where('vendor_code', $bill->vendor_code)->value('gstin_number');
        $vendorOcr = $invoice['vendor_gstin'] ?? $invoice['vendor_gstin_number'] ?? null;
        $note = $this->gstinRemark('Vendor', $vendorGstin, $vendorOcr);
        if ($note) {
            $advisory[] = $note;
        }

        // Customer/site mapping default: consignee_code -> site_plant.plant_site_code.
        // If the invoice's billed customer is the consignor, change the field below.
        $siteCode = $bill->consignee_code;
        $customerGstin = DB::table('site_plants')->where('plant_site_code', $siteCode)->value('gstin_number');
        $customerOcr = $invoice['customer_gstin'] ?? $invoice['customer_gstin_number'] ?? null;
        $note = $this->gstinRemark('Customer (site '.$siteCode.')', $customerGstin, $customerOcr);
        if ($note) {
            $advisory[] = $note;
        }

        $status = $errors ? 'invalid' : ($review ? 'review_required' : 'valid');
        $reasons = array_merge($errors, $review, $advisory);
        return [$status, $reasons ?: ['All mandatory automated checks passed']];
    }
}
