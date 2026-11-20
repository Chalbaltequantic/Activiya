<?php

namespace App\Jobs;

use App\Models\Billdata;
use App\Models\FreightOcrResult;
use App\Services\FreightOcrValidator;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ProcessFreightOcr implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    // Maximum number of attempts if the job fails.
    public $tries = 3;

    // Maximum time allowed for one attempt (seconds).
    public $timeout = 180;

    // Wait 15 seconds before retrying a failed attempt.
    public $backoff = 15;

    // Store the OCR result ID.
    public $resultId;

    /**
     * Create the OCR job.
     */
    public function __construct(int $resultId)
    {
        $this->resultId = $resultId;

        // Use a separate queue for freight OCR jobs.
        $this->onQueue('freight-ocr');
    }

    /**
     * Process and validate the uploaded freight invoice.
     */
    public function handle(FreightOcrValidator $validator): void
    {
        // Step 1: Find the OCR result.
        $result = FreightOcrResult::findOrFail($this->resultId);

        // Do not process an invoice that is already verified.
        if (
            $result->status == 'valid' ||
            $result->status == 'invalid' ||
            $result->status == 'review_required'
        ) {
            return;
        }

        // Mark the invoice as being processed.
        $result->update([
            'status' => 'processing'
        ]);

        // Step 2: Find the related freight bill.
        $bill = Billdata::findOrFail($result->billdata_id);

        // Do not process submitted or returned freight bills.
        if ($bill->submit || $bill->f_return) {

            $message = 'Freight bill is already submitted or returned.';

            $result->update([
                'status'  => 'review_required',
                'remarks' => $message,
                'reasons' => [$message]
            ]);

            return;
        }

        // Step 3: Check for duplicate freight invoices.
        $duplicateMessage = $validator->duplicate($bill);

        if ($duplicateMessage) {

            $result->update([
                'status'  => 'invalid',
                'remarks' => $duplicateMessage,
                'reasons' => [$duplicateMessage]
            ]);

            return;
        }

        // Step 4: Find the uploaded invoice file.
        $invoicePath = $this->resolveFile($bill->freight_invoice_file);

        if (!$invoicePath) {

            $message = 'Invoice file is missing or cannot be accessed.';

            $result->update([
                'status'  => 'review_required',
                'remarks' => $message,
                'reasons' => [$message]
            ]);

            return;
        }

        // Step 5: Get OCR API configuration.
        $apiUrl = config('services.freight_ocr.url');
        $apiKey = config('services.freight_ocr.api_key');

        if (empty($apiUrl) || empty($apiKey)) {
            throw new \RuntimeException(
                'Freight OCR service is not configured.'
            );
        }

        // Prepare OCR API endpoint.
        $apiUrl = rtrim($apiUrl, '/') . '/api/freight/extract';

        // Step 6: Open the invoice document.
        $invoiceFile = fopen($invoicePath, 'rb');

        if ($invoiceFile === false) {
            throw new \RuntimeException(
                'Unable to open the uploaded invoice file.'
            );
        }

        // Step 7: Send the invoice document to OCR API.
        try {

            $response = Http::withHeaders([
                'X-API-Key' => $apiKey
            ])
            ->timeout(150)
            ->connectTimeout(10)
            ->attach(
                'invoice',
                $invoiceFile,
                basename($invoicePath)
            )
            ->post($apiUrl);

        } finally {

            // Always close the file after sending the request.
            fclose($invoiceFile);
        }

        // Step 8: Check OCR API response.
        if (!$response->successful()) {

            throw new \RuntimeException(
                'OCR API request failed. HTTP status: ' .
                $response->status()
            );
        }

        // Convert the OCR response into an array.
        $extractedData = $response->json();

        if (!is_array($extractedData)) {
            throw new \RuntimeException(
                'Invalid response received from OCR service.'
            );
        }

        // Step 9: Validate extracted invoice details.
        // The validator compares extracted data with freight bill data.
        list($status, $reasons) = $validator->check(
            $bill,
            $extractedData
        );

        // Step 10: Save the final OCR verification result.
        $result->update([
            'status'    => $status,
            'reasons'   => $reasons,
            'remarks'   => implode('; ', $reasons),
            'extracted' => $extractedData
        ]);
    }

    /**
     * Find the uploaded invoice file.
     *
     * Invoice documents are stored inside:
     * public/uploads/invoice/
     */
    private function resolveFile($filename): ?string
    {
        // Return if the invoice filename is empty.
        if (empty($filename)) {
            return null;
        }

        // Convert Windows-style paths into normal paths.
        $relativePath = str_replace('\\', '/', $filename);

        // Remove any slash at the beginning.
        $relativePath = ltrim($relativePath, '/');

        // Prevent access to files outside the invoice folder.
        if (str_contains($relativePath, '..')) {
            return null;
        }

        // Only allow files from the invoice upload folder.
        if (!str_starts_with($relativePath, 'uploads/invoice/')) {
            return null;
        }

        // Build the complete file path.
        $fullPath = public_path($relativePath);

        // Check whether the file exists.
        if (!is_file($fullPath)) {
            return null;
        }

        // Check whether the file is readable.
        if (!is_readable($fullPath)) {
            return null;
        }

        return $fullPath;
    }

    /**
     * Handle a job that has failed after all retry attempts.
     */
    public function failed(\Throwable $exception): void
    {
        // Record the actual error in Laravel logs.
        Log::error('Freight OCR processing failed', [
            'result_id' => $this->resultId,
            'error'     => $exception->getMessage()
        ]);

        // Update the OCR result so users know verification failed.
        FreightOcrResult::where('id', $this->resultId)
            ->update([
                'status'  => 'failed',
                'remarks' => 'Invoice verification failed. Please retry.',
                'reasons' => [
                    'Invoice verification failed. Please retry.'
                ]
            ]);
    }
}
