<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Billdata;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Throwable;

class NonFtlBulkInvoiceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    private function nonFtlQuery()
    {
        // Change the submit value here if your application stores a different submitted status.
         return Billdata::query()
        ->where('submit', 1)
        ->whereNotNull('custom')
        ->whereRaw("LOWER(TRIM(custom)) != ?", ['ftl']);
    }

    public function index()
    {
        $invoices = $this->nonFtlQuery()
            ->whereNotNull('freight_invoice_no')
            ->where('freight_invoice_no', '<>', '')
            ->orderByDesc('id')
            ->paginate(30);

        return view('admin.billdata.nonftl_bulk_invoices', compact('invoices'));
    }

    public function upload(Request $request)
    {
        $request->validate([
            'invoice_files' => 'required|array|min:1|max:100',
            'invoice_files.*' => 'required|file|mimes:pdf|max:10240',
        ]);

        $success = [];
        $errors = [];
        $directory = public_path('uploads/invoice');

        if (!File::isDirectory($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        foreach ($request->file('invoice_files') as $file) {
            $originalName = $file->getClientOriginalName();
            $invoiceNo = trim(pathinfo($originalName, PATHINFO_FILENAME));

            if ($invoiceNo === '' || !preg_match('/^[A-Za-z0-9._\/-]+$/', $invoiceNo)) {
                $errors[] = $originalName . ': invalid invoice number in filename.';
                continue;
            }

            // All rows for the same invoice share one PDF.
            $bills = $this->nonFtlQuery()
                ->where('freight_invoice_no', $invoiceNo)
                ->get();

            if ($bills->isEmpty()) {
                $errors[] = $originalName . ': no submitted Non-FTL invoice matched.';
                continue;
            }

            if ($bills->contains(function ($bill) {
                return !empty($bill->freight_invoice_file);
            })) {
                $errors[] = $originalName . ': invoice document already attached; skipped.';
                continue;
            }

            $savedName = Str::uuid()->toString() . '.pdf';
            $relativePath = 'uploads/invoice/' . $savedName;

            try {
                $file->move($directory, $savedName);

                DB::transaction(function () use ($bills, $relativePath) {
                    foreach ($bills as $bill) {
                        // Use a direct update so the existing Billdata $fillable is unchanged.
                        Billdata::whereKey($bill->getKey())
                            ->where(function ($query) {
                                $query->whereNull('freight_invoice_file')
                                    ->orWhere('freight_invoice_file', '');
                            })
                            ->update(['freight_invoice_file' => $relativePath]);
                    }
                });

                $success[] = $originalName . ' (' . $bills->count() . ' bill row(s))';
            } catch (Throwable $e) {
                File::delete(public_path($relativePath));
                report($e);
                $errors[] = $originalName . ': upload failed.';
            }
        }

        return redirect()->route('admin.nonftl.bulk-invoices.index')->with([
            'success' => count($success) . ' invoice file(s) uploaded successfully.',
            'uploadedInvoices' => $success,
            'errorRows' => array_map(function ($message) {
                return ['reason' => $message];
            }, $errors),
        ]);
    }

    public function view(Billdata $bill)
    {
        $allowed = $this->nonFtlQuery()->whereKey($bill->getKey())->exists();
        abort_unless($allowed, 404);

        $path = $bill->freight_invoice_file;
        abort_if(empty($path), 404);

        // Restrict file access to the dedicated invoice upload directory.
        $fileName = basename(str_replace('\\', '/', $path));
        $absolutePath = public_path('uploads/invoice/' . $fileName);
        abort_unless(File::isFile($absolutePath), 404);

        return response()->file($absolutePath, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $fileName . '"',
        ]);
    }
}
