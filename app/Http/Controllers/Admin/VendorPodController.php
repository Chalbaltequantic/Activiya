<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VendorPod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\QueryException;

class VendorPodController extends Controller
{
	public function __construct()
    {
        $this->middleware('auth:admin');     
    }

   private function isAdmin(): bool
    {
		 $user = Auth::user();
		if (!$user) {
			return false;
		}
		return (int) $user->role_id === 1;
    }

    private function vendorCode(): ?string
	{
		$user = Auth::guard('admin')->user();
		
		if (!$user) {
			abort(401, 'Please login first.');
		}

		// Admin does not require a vendor code.
		if ((int) $user->role_id === 1) {
			return null;
		}

		$code = trim((string) ($user->vendor_code ?? ''));

		if ($code === '') {
			abort(403, 'Vendor code is not mapped to your login.');
		}

		return $code;
	}

    private function allowedQuery()
    {
        return $this->isAdmin()
            ? VendorPod::query()
            : VendorPod::where('vendor_code', $this->vendorCode());
    }

    private function normalizedLr(string $lr): string
    {
        return mb_strtoupper(trim($lr), 'UTF-8');
    }

  	
	public function index()
	{
		$title = 'Vendor POD Upload';
		$pagetitle = 'Vendor POD Upload';

		$isAdmin = $this->isAdmin();

		$query = VendorPod::query();

		if (!$isAdmin) {
			$vendorCode = $this->vendorCode();

			$query->where('vendor_code', $vendorCode);
		}

		$entries = $query->latest()->paginate(20);

		return view('admin.vendor_pod.index', compact(
			'title',
			'pagetitle',
			'entries',
			'isAdmin'
		));
	}

    public function store(Request $request)
    {
        abort_if($this->isAdmin(), 403, 'Only vendor accounts can upload new PODs.');
        $vendorCode = $this->vendorCode();

        $request->validate([
            'data' => ['required', 'array', 'max:20'],
        ]);

        $rows = $request->input('data', []);
        $inserted = 0;
        $skipped = [];
        $seenLrs = [];

        // Each row is independent. An invalid row does not roll back successful rows.
        for ($i = 0; $i < 20; $i++) {
            $row = is_array($rows[$i] ?? null) ? $rows[$i] : [];
            $invoice = trim((string) ($row['invoice_no'] ?? ''));
            $lr = trim((string) ($row['lr_no'] ?? ''));
            $front = $request->file("data.{$i}.pod_front");
            $back = $request->file("data.{$i}.pod_back");
            $other = $request->file("data.{$i}.other_document");

            // Completely empty rows are ignored silently.
            if ($invoice === '' && $lr === '' && !$front && !$back && !$other) {
                continue;
            }

            $validator = Validator::make([
                'invoice_no' => $invoice,
                'lr_no' => $lr,
                'pod_front' => $front,
                'pod_back' => $back,
                'other_document' => $other,
            ], [
                'invoice_no' => ['required', 'string', 'max:100'],
                'lr_no' => ['required', 'string', 'max:100'],
                'pod_front' => ['required', 'file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:2048'],
                'pod_back' => ['required', 'file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:2048'],
                'other_document' => ['required', 'file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:2048'],
            ]);

            if ($validator->fails()) {
                $skipped[] = 'Row ' . ($i + 1) . ': ' . implode('; ', $validator->errors()->all());
                continue;
            }

            $lrKey = $this->normalizedLr($lr);
            if (isset($seenLrs[$lrKey]) || VendorPod::withTrashed()->where('lr_no_key', $lrKey)->exists()) {
                $skipped[] = 'Row ' . ($i + 1) . ': LR/CN number already uploaded or repeated in this submission.';
                continue;
            }
            $seenLrs[$lrKey] = true;

            $paths = [];
            try {
               
				//$paths['pod_front_path'] = $front->store('vendor-pods/front', 'local');
                //$paths['pod_back_path'] = $back->store('vendor-pods/back', 'local');
                //$paths['other_document_path'] = $other->store('vendor-pods/other', 'local');
				
				$paths['pod_front_path'] = $front->store('vendor-pods/front', 'vendor_pods');
				$paths['pod_back_path'] = $back->store('vendor-pods/back', 'vendor_pods');
				$paths['other_document_path'] = $other->store('vendor-pods/other', 'vendor_pods');

                VendorPod::create(array_merge([
                    'vendor_code' => $vendorCode,
                    'invoice_no' => $invoice,
                    'lr_no' => $lr,
                    'lr_no_key' => $lrKey,
                    'uploaded_by' => Auth::id(),
                ], $paths));
                $inserted++;
            } catch (\Throwable $e) {
                foreach ($paths as $path) {
                    if ($path) Storage::disk('local')->delete($path);
                }
                unset($seenLrs[$lrKey]);
                report($e);
                $skipped[] = 'Row ' . ($i + 1) . ': Upload could not be saved. Check for duplicate LR or contact administrator.';
            }
        }

        return redirect()->route('admin.vendor-pods.index', ['tab' => 'upload'])
            ->with('success', $inserted . ' POD record(s) uploaded successfully.')
            ->with('rowErrors', $skipped);
    }

   /* public function file(int $id, string $type)
    {
        $pod = $this->allowedQuery()->findOrFail($id);
        $columns = [
            'front' => 'pod_front_path',
            'back' => 'pod_back_path',
            'other' => 'other_document_path',
        ];
        abort_unless(isset($columns[$type]), 404);
        $path = $pod->{$columns[$type]};
        abort_unless($path && Storage::disk('local')->exists($path), 404);
        return Storage::disk('local')->response($path, basename($path), [
            'Content-Disposition' => 'inline; filename="' . basename($path) . '"',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }*/
	
	
	public function file(int $id, string $type)
	{
		$pod = $this->allowedQuery()->findOrFail($id);
		$columns = [
			'front' => 'pod_front_path',
			'back'  => 'pod_back_path',
			'other' => 'other_document_path',
		];
		abort_unless(isset($columns[$type]), 404);
		$path = $pod->{$columns[$type]};

		abort_unless($path && Storage::disk('vendor_pods')->exists($path), 404, 'File not found.');

		return Storage::disk('vendor_pods')->response($path,
			basename($path),
			[
				'Content-Disposition' => 'inline; filename="' . basename($path) . '"',
				'X-Content-Type-Options' => 'nosniff',
			]
		);
	}

	
	
	
	

    public function update(Request $request, int $id)
    {
        abort_unless($this->isAdmin(), 403);
        $pod = VendorPod::findOrFail($id);
        $request->validate([
            'invoice_no' => ['required', 'string', 'max:100'],
            'pod_front' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:2048'],
            'pod_back' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:2048'],
            'other_document' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:2048'],
        ]);

        $changes = ['invoice_no' => trim((string) $request->invoice_no), 'updated_by' => Auth::id()];
        $newPaths = [];
        $oldPaths = [];
        try {
            foreach (['pod_front' => 'front', 'pod_back' => 'back', 'other_document' => 'other'] as $field => $folder) {
                if (!$request->hasFile($field)) continue;
                $column = $field . '_path';
                $newPaths[$column] = $request->file($field)->store('vendor-pods/' . $folder, 'local');
                $oldPaths[$column] = $pod->{$column};
                $changes[$column] = $newPaths[$column];
            }
            $pod->update($changes);
        } catch (\Throwable $e) {
            foreach ($newPaths as $path) if ($path) Storage::disk('local')->delete($path);
            throw $e;
        }
        foreach ($oldPaths as $path) if ($path) Storage::disk('local')->delete($path);
        return back()->with('success', 'POD updated successfully.');
    }

    public function destroy(int $id)
    {
        abort_unless($this->isAdmin(), 403);
        $pod = VendorPod::findOrFail($id);
        $pod->deleted_by = Auth::id();
        $pod->save();
        $pod->delete(); // Soft delete; LR number remains permanently reserved.
        return back()->with('success', 'POD deleted from listing. LR number cannot be uploaded again.');
    }
}
