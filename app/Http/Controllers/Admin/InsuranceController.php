<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Insurance;
use App\Models\InsurancePhotograph;
use App\Models\Siteplant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class InsuranceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index()
    {
        return view('admin.insurance.index');
    }

    public function import(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:xls,xlsx'
        ]);

        try {
            $spreadsheet = IOFactory::load($request->file('excel_file')->getPathname());
            $rows = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
            $createdBy = Auth::guard('admin')->id();
            $inserted = 0;

            DB::beginTransaction();

            foreach ($rows as $index => $row) {
                if ($index == 1) {
                    continue;
                }

                $hasData = count(array_filter($row, function ($value) {
                    return trim((string)$value) !== '';
                })) > 0;

                if (!$hasData) {
                    continue;
                }

                $invoiceNo = trim((string)($row['G'] ?? ''));
                $fromCode = trim((string)($row['C'] ?? ''));
                $fromLocation = trim((string)($row['D'] ?? ''));
                $toCode = trim((string)($row['E'] ?? ''));
                $toLocation = trim((string)($row['F'] ?? ''));
                $transporter = trim((string)($row['I'] ?? ''));
                $truckNumber = '';
                $lrNo = trim((string)($row['J'] ?? ''));
                $lrDate = $this->excelDate($row['K'] ?? null);

                if ($invoiceNo != '') {
                    $preloading = DB::table('digiwim_preloading_operations')
                        ->where('invoice_challan_no', $invoiceNo)
                        ->orderBy('id')
                        ->first();

                    if ($preloading) {
                        if ($fromCode == '' && !empty($preloading->consignor_code)) {
                            $fromCode = $preloading->consignor_code;
                        }

                        if ($fromLocation == '' && !empty($preloading->consignor_location)) {
                            $fromLocation = $preloading->consignor_location;
                        }

                        if ($toCode == '' && !empty($preloading->consignee_code)) {
                            $toCode = $preloading->consignee_code;
                        }

                        if ($toLocation == '' && !empty($preloading->consignee_location)) {
                            $toLocation = $preloading->consignee_location;
                        }

                        if ($transporter == '' && !empty($preloading->transporter_name)) {
                            $transporter = $preloading->transporter_name;
                        }
						
						
                        if ($truckNumber == '' && !empty($preloading->truck_number)) {
                            $truckNumber = $preloading->truck_number;
                        }

                        if ($lrNo == '' && !empty($preloading->lr_no)) {
                            $lrNo = $preloading->lr_no;
                        }

                        if (!$lrDate && !empty($preloading->lr_date)) {
                            $lrDate = $preloading->lr_date;
                        }
                    }
                }

                $fromLocation = $this->fillLocation($fromCode, $fromLocation);
                $toLocation = $this->fillLocation($toCode, $toLocation);

                $damage = (float)str_replace(',', '', $row['L'] ?? 0);
                $shortage = (float)str_replace(',', '', $row['M'] ?? 0);

                $insurance = Insurance::create([
                    'loss_date' => $this->excelDate($row['A'] ?? null),
                    'nature_of_claim' => trim((string)($row['B'] ?? '')),
                    'from_location_code' => $fromCode,
                    'from_location' => $fromLocation,
                    'to_location_code' => $toCode,
                    'to_location' => $toLocation,
                    'invoice_no' => $invoiceNo,
                    'invoice_date' => $this->excelDate($row['H'] ?? null),
                    'transporter_name' => $transporter,
                    'truck_no' => $truckNumber,
                    'lr_no' => $lrNo,
                    'lr_date' => $lrDate,
                    'damage_value' => $damage,
                    'shortage_value' => $shortage,
                    'total_value' => $damage + $shortage,
                    'created_by' => $createdBy
                ]);
				
				$insurance->claim_no = $this->generateClaimNo($insurance->id);
				$insurance->save();

                $inserted++;
            }

            DB::commit();

            return redirect()
                ->route('admin.insurance.datalist')
                ->with('success', $inserted . ' Insurance record(s) imported successfully.');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function manualupload(Request $request)
    {
        $ids = session('insurance_manual_ids', []);
        $savedInsurance = collect();

        if (!empty($ids)) {
            $savedInsurance = Insurance::with('photographs')
                ->whereIn('id', $ids)
                ->orderByRaw('FIELD(id,' . implode(',', array_map('intval', $ids)) . ')')
                ->get();
        }

        $activeTab = $request->get('tab', 'details');

        return view('admin.insurance.manualupload', compact(
            'savedInsurance',
            'activeTab'
        ));
    }
	
	private function generateClaimNo($insuranceId)
	{
		$year = date('Y');
		$month = date('n');

		if ($month >= 4) {
			$startYear = $year;
			$endYear = $year + 1;
		} else {
			$startYear = $year - 1;
			$endYear = $year;
		}

		$financialYear = substr($startYear, -2) . '-' . substr($endYear, -2);

		$claimNumber = 1000 + $insuranceId;

		return 'ZWPL/' . $financialYear . '/' . $claimNumber;
	}
	
	
    public function saveManualData(Request $request)
    {
        $createdBy = Auth::guard('admin')->id();
        $insertedIds = [];

        DB::beginTransaction();

        try {
            for ($i = 0; $i < 20; $i++) {
                $lossDate = $request->input("loss_date.$i");
                $invoiceNo = trim((string)$request->input("invoice_no.$i"));
                $fromCode = trim((string)$request->input("from_location_code.$i"));
                $toCode = trim((string)$request->input("to_location_code.$i"));

                if (!$lossDate && $invoiceNo == '' && $fromCode == '' && $toCode == '') {
                    continue;
                }

                $fromLocation = trim((string)$request->input("from_location.$i"));
                $toLocation = trim((string)$request->input("to_location.$i"));
                $transporter = trim((string)$request->input("transporter_name.$i"));
                $truckNumber = trim((string)$request->input("truck_no.$i"));
                $lrNo = trim((string)$request->input("lr_no.$i"));
                $lrDate = $request->input("lr_date.$i");

                if ($invoiceNo != '') {
                    $preloading = DB::table('digiwim_preloading_operations')
                        ->where('invoice_challan_no', $invoiceNo)
                        ->orderBy('id')
                        ->first();

                    if ($preloading) {
                        if ($fromCode == '' && !empty($preloading->consignor_code)) {
                            $fromCode = $preloading->consignor_code;
                        }

                        if ($fromLocation == '' && !empty($preloading->consignor_location)) {
                            $fromLocation = $preloading->consignor_location;
                        }

                        if ($toCode == '' && !empty($preloading->consignee_code)) {
                            $toCode = $preloading->consignee_code;
                        }

                        if ($toLocation == '' && !empty($preloading->consignee_location)) {
                            $toLocation = $preloading->consignee_location;
                        }

                        if ($transporter == '' && !empty($preloading->transporter_name)) {
                            $transporter = $preloading->transporter_name;
                        }
						if ($truckNumber == '' && !empty($preloading->truck_number)) {
                            $truckNumber = $preloading->truck_number;
                        }

                        if ($lrNo == '' && !empty($preloading->lr_no)) {
                            $lrNo = $preloading->lr_no;
                        }

                        if (!$lrDate && !empty($preloading->lr_date)) {
                            $lrDate = $preloading->lr_date;
                        }
                    }
                }

                $fromLocation = $this->fillLocation($fromCode, $fromLocation);
                $toLocation = $this->fillLocation($toCode, $toLocation);

                $insurance = Insurance::create([
                    'loss_date' => $lossDate ?: null,
                    'nature_of_claim' => $request->input("nature_of_claim.$i"),
                    'from_location_code' => $fromCode,
                    'from_location' => $fromLocation,
                    'to_location_code' => $toCode,
                    'to_location' => $toLocation,
                    'invoice_no' => $invoiceNo,
                    'invoice_date' => $request->input("invoice_date.$i") ?: null,
                    'transporter_name' => $transporter,
                    'truck_no' => $truckNumber,
                    'lr_no' => $lrNo,
                    'lr_date' => $lrDate ?: null,
                    'created_by' => $createdBy
                ]);
				
				$insurance->claim_no = $this->generateClaimNo($insurance->id);
				$insurance->save();


                $insertedIds[] = $insurance->id;
            }

            if (empty($insertedIds)) {
                DB::rollBack();

                return back()->withInput()->with('error', 'Please enter at least one Insurance record.');
            }

            DB::commit();

            session([
                'insurance_manual_ids' => $insertedIds
            ]);

            return redirect()
                ->route('admin.insurance.manual-upload', ['tab' => 'files'])
                ->with('success', count($insertedIds) . ' Insurance record(s) saved successfully. Please upload files.');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function fetchLocation(Request $request)
    {
        $code = trim((string)$request->plant_code);

        if ($code == '') {
            return response()->json([
                'success' => false,
                'message' => 'Location code required.'
            ]);
        }

        $plant = Siteplant::where('plant_site_code', $code)->first();

        if (!$plant) {
            return response()->json([
                'success' => false,
                'message' => 'Location not found.'
            ]);
        }

        return response()->json([
            'success' => true,
            'location_name' => $plant->city ?? $plant->plant_site_name ?? ''
        ]);
    }

    public function fetchInvoice(Request $request)
    {
        $invoiceNo = trim((string)$request->invoice_no);

        if ($invoiceNo == '') {
            return response()->json([
                'success' => false,
                'message' => 'Invoice No. required.'
            ]);
        }

        $row = DB::table('digiwim_preloading_operations')
            ->where('invoice_challan_no', $invoiceNo)
            ->orderBy('id')
            ->first();

        if (!$row) {
            return response()->json([
                'success' => false,
                'message' => 'Invoice not found. Enter details manually.'
            ]);
        }

        return response()->json([
            'success' => true,
            'from_location_code' => $row->consignor_code ?? '',
            'from_location' => $row->consignor_location ?? '',
            'to_location_code' => $row->consignee_code ?? '',
            'to_location' => $row->consignee_location ?? '',
            'transporter_name' => $row->transporter_name ?? '',
            'truck_no' => $row->truck_number ?? '',
            'lr_no' => $row->lr_no ?? '',
            'lr_date' => $row->lr_date ?? ''
        ]);
    }

    public function uploadInvoice(Request $request, $id)
    {
        $request->validate([
            'file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240'
        ]);

        $insurance = Insurance::findOrFail($id);

        $this->createDirectories();

        if ($insurance->invoice_file) {
            $this->deletePhysicalFile($insurance->invoice_file);
        }

        $file = $request->file('file');

        $fileName = 'invoice_' . $insurance->id . '_' . time() . '_' . Str::random(6) . '.' . strtolower($file->getClientOriginalExtension());

        $file->move(
            public_path('uploads/insurance/invoice'),
            $fileName
        );

        $insurance->invoice_file = 'uploads/insurance/invoice/' . $fileName;
        $insurance->save();

        return response()->json([
            'success' => true,
            'message' => 'Invoice uploaded successfully.',
            'url' => asset($insurance->invoice_file)
        ]);
    }

    public function deleteInvoice($id)
    {
        $insurance = Insurance::findOrFail($id);

        if ($insurance->invoice_file) {
            $this->deletePhysicalFile($insurance->invoice_file);
        }

        $insurance->invoice_file = null;
        $insurance->save();

        return response()->json([
            'success' => true,
            'message' => 'Invoice deleted successfully.'
        ]);
    }

    public function uploadPod(Request $request, $id)
    {
        $request->validate([
            'file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240'
        ]);

        $insurance = Insurance::findOrFail($id);

        $this->createDirectories();

        if ($insurance->pod_lr_copy) {
            $this->deletePhysicalFile($insurance->pod_lr_copy);
        }

        $file = $request->file('file');

        $fileName = 'pod_' . $insurance->id . '_' . time() . '_' . Str::random(6) . '.' . strtolower($file->getClientOriginalExtension());

        $file->move(
            public_path('uploads/insurance/lr'),
            $fileName
        );

        $insurance->pod_lr_copy = 'uploads/insurance/lr/' . $fileName;
        $insurance->save();

        return response()->json([
            'success' => true,
            'message' => 'POD/LR Copy uploaded successfully.',
            'url' => asset($insurance->pod_lr_copy)
        ]);
    }

    public function deletePod($id)
    {
        $insurance = Insurance::findOrFail($id);

        if ($insurance->pod_lr_copy) {
            $this->deletePhysicalFile($insurance->pod_lr_copy);
        }

        $insurance->pod_lr_copy = null;
        $insurance->save();

        return response()->json([
            'success' => true,
            'message' => 'POD/LR Copy deleted successfully.'
        ]);
    }

    public function photographList($id)
    {
        $insurance = Insurance::with('photographs')->findOrFail($id);

        return response()->json([
            'success' => true,
            'insurance_id' => $insurance->id,
            'invoice_no' => $insurance->invoice_no,
            'lr_no' => $insurance->lr_no,
            'photo_count' => $insurance->photographs->count(),
            'remaining' => max(0, 10 - $insurance->photographs->count()),
            'photographs' => $insurance->photographs->map(function ($photo) {
                return [
                    'id' => $photo->id,
                    'url' => asset($photo->photo_path),
                    'photo_path' => $photo->photo_path,
                    'original_name' => $photo->original_name
                ];
            })->values()
        ]);
    }

    public function uploadPhotographs(Request $request, $id)
    {
        $request->validate([
            'photographs' => 'required|array|min:1|max:10',
            'photographs.*' => 'required|image|mimes:jpg,jpeg,png|max:1024'
        ], [
            'photographs.max' => 'Maximum 10 photographs are allowed.',
            'photographs.*.max' => 'Each photograph must be 1 MB or less.'
        ]);

        $insurance = Insurance::findOrFail($id);
        $currentCount = $insurance->photographs()->count();
        $files = $request->file('photographs', []);

        if ($currentCount + count($files) > 10) {
            return response()->json([
                'success' => false,
                'message' => 'Only ' . (10 - $currentCount) . ' more photograph(s) can be uploaded.'
            ], 422);
        }

        $this->createDirectories();
        $createdBy = Auth::guard('admin')->id();

        foreach ($files as $photo) {
            $fileName = 'photo_' . $insurance->id . '_' . time() . '_' . Str::random(8) . '.' . strtolower($photo->getClientOriginalExtension());

            $photo->move(
                public_path('uploads/insurance/photos'),
                $fileName
            );

            InsurancePhotograph::create([
                'insurance_id' => $insurance->id,
                'photo_path' => 'uploads/insurance/photos/' . $fileName,
                'original_name' => $photo->getClientOriginalName(),
                'uploaded_by' => $createdBy
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Photograph(s) uploaded successfully.'
        ]);
    }

    public function deletePhotograph($photoId)
    {
        $photo = InsurancePhotograph::findOrFail($photoId);

        $this->deletePhysicalFile($photo->photo_path);

        $photo->delete();

        return response()->json([
            'success' => true,
            'message' => 'Photograph deleted successfully.'
        ]);
    }

    public function tVendor(Request $request)
    {
        $perPage = (int)$request->get('per_page', 25);

        if (!in_array($perPage, [10, 25, 50, 100])) {
            $perPage = 25;
        }

        $query = Insurance::with('photographs');

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('invoice_no', 'like', '%' . $search . '%')
                    ->orWhere('lr_no', 'like', '%' . $search . '%')
                    ->orWhere('transporter_name', 'like', '%' . $search . '%')
                    ->orWhere('from_location_code', 'like', '%' . $search . '%')
                    ->orWhere('from_location', 'like', '%' . $search . '%')
                    ->orWhere('to_location_code', 'like', '%' . $search . '%')
                    ->orWhere('to_location', 'like', '%' . $search . '%');
            });
        }

        $datalist = $query
            ->orderBy('id', 'desc')
            ->paginate($perPage);

        $datalist->appends($request->query());

        return view('admin.insurance.tvendor', compact(
            'datalist',
            'perPage'
        ));
    }


	public function uploadTVendorDocument(Request $request, $id)
	{
		$request->validate([
			'document_type' => 'required|string',
			'file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240'
		]);

		$insurance = Insurance::findOrFail($id);

		if ($request->document_type == 'cof') {
			$field = 'cof_file';
			$folder = 'cof';
			$label = 'COF';
		} elseif ($request->document_type == 'fir_police_report') {
			$field = 'fir_police_report_file';
			$folder = 'fir-police-report';
			$label = 'FIR / Police Report';
		} elseif ($request->document_type == 'fire_report') {
			$field = 'fire_report_file';
			$folder = 'fire-report';
			$label = 'Fire Report';
		} elseif ($request->document_type == 'fir_closure_report') {
			$field = 'fir_closure_report_file';
			$folder = 'fir-closure-report';
			$label = 'FIR Closure Report';
		} else {
			return response()->json([
				'success' => false,
				'message' => 'Invalid document type.'
			], 422);
		}

		$file = $request->file('file');
		$oldFile = $insurance->{$field};

		$fileName = $request->document_type . '_' .
			$insurance->id . '_' .
			time() . '_' .
			Str::random(6) . '.' .
			strtolower($file->getClientOriginalExtension());

		$file->move(
			public_path('uploads/insurance/' . $folder),
			$fileName
		);

		$relativePath = 'uploads/insurance/' . $folder . '/' . $fileName;

		$insurance->{$field} = $relativePath;
		$insurance->save();

		DB::table('insurance_file_histories')->insert([
			'insurance_id' => $insurance->id,
			'document_type' => $request->document_type,
			'file_path' => $relativePath,
			'original_name' => $file->getClientOriginalName(),
			'action' => $oldFile ? 'Replaced' : 'Uploaded',
			'uploaded_by' => Auth::guard('admin')->id(),
			'created_at' => now(),
			'updated_at' => now()
		]);

		return response()->json([
			'success' => true,
			'message' => $label . ' uploaded successfully.',
			'url' => asset($relativePath)
		]);
	}

	public function deleteTVendorDocument(Request $request, $id)
	{
		$request->validate([
			'document_type' => 'required|string'
		]);

		$insurance = Insurance::findOrFail($id);

		if ($request->document_type == 'cof') {
			$field = 'cof_file';
			$label = 'COF';
		} elseif ($request->document_type == 'fir_police_report') {
			$field = 'fir_police_report_file';
			$label = 'FIR / Police Report';
		} elseif ($request->document_type == 'fire_report') {
			$field = 'fire_report_file';
			$label = 'Fire Report';
		} elseif ($request->document_type == 'fir_closure_report') {
			$field = 'fir_closure_report_file';
			$label = 'FIR Closure Report';
		} else {
			return response()->json([
				'success' => false,
				'message' => 'Invalid document type.'
			], 422);
		}

		$currentFile = $insurance->{$field};

		if (!$currentFile) {
			return response()->json([
				'success' => false,
				'message' => 'No file found.'
			], 404);
		}

		$lastUpload = DB::table('insurance_file_histories')
			->where('insurance_id', $insurance->id)
			->where('document_type', $request->document_type)
			->where('file_path', $currentFile)
			->orderBy('id', 'desc')
			->first();

		DB::table('insurance_file_histories')->insert([
			'insurance_id' => $insurance->id,
			'document_type' => $request->document_type,
			'file_path' => $currentFile,
			'original_name' => $lastUpload->original_name ?? basename($currentFile),
			'action' => 'Deleted',
			'uploaded_by' => Auth::guard('admin')->id(),
			'created_at' => now(),
			'updated_at' => now()
		]);

		$insurance->{$field} = null;
		$insurance->save();

		return response()->json([
			'success' => true,
			'message' => $label . ' removed successfully.'
		]);
	}

   public function tVendorHistory($id, $documentType)
	{
		$insurance = Insurance::findOrFail($id);

		if ($documentType == 'cof') {
			$label = 'COF';
		} elseif ($documentType == 'fir_police_report') {
			$label = 'FIR / Police Report';
		} elseif ($documentType == 'fire_report') {
			$label = 'Fire Report';
		} elseif ($documentType == 'fir_closure_report') {
			$label = 'FIR Closure Report';
		} else {
			return response()->json([
				'success' => false,
				'message' => 'Invalid document type.'
			], 422);
		}

		$history = DB::table('insurance_file_histories')
			->where('insurance_id', $insurance->id)
			->where('document_type', $documentType)
			->orderBy('id', 'desc')
			->get()
			->map(function ($row) {
				return [
					'id' => $row->id,
					'file_name' => $row->original_name ?: basename($row->file_path),
					'file_url' => $row->file_path ? asset($row->file_path) : null,
					'action' => $row->action,
					'uploaded_by' => $row->uploaded_by,
					'created_at' => date('d-m-Y h:i A', strtotime($row->created_at))
				];
			});

		return response()->json([
			'success' => true,
			'insurance_id' => $insurance->id,
			'invoice_no' => $insurance->invoice_no,
			'document_type' => $documentType,
			'document_label' => $label,
			'history' => $history
		]);
	}

    public function datalist(Request $request)
    {
        $perPage = (int)$request->get('per_page', 25);

        if (!in_array($perPage, [10, 25, 50, 100])) {
            $perPage = 25;
        }

        $allowedSort = [
            'loss_date',
            'nature_of_claim',
            'from_location_code',
            'from_location',
            'to_location_code',
            'to_location',
            'invoice_no',
            'invoice_date',
            'transporter_name',
            'truck_no',
            'lr_no',
            'lr_date',
            'created_at'
        ];

        $sortBy = $request->get('sort_by', 'created_at');
        $sortDirection = strtolower($request->get('sort_direction', 'desc'));

        if (!in_array($sortBy, $allowedSort)) {
            $sortBy = 'created_at';
        }

        if (!in_array($sortDirection, ['asc', 'desc'])) {
            $sortDirection = 'desc';
        }

        $query = $this->filteredQuery($request);

        $datalist = $query
            ->with('photographs')
            ->orderBy($sortBy, $sortDirection)
            ->paginate($perPage);

        $datalist->appends($request->query());

        return view('admin.insurance.datalist', compact(
            'datalist',
            'perPage',
            'sortBy',
            'sortDirection'
        ));
    }

    public function export(Request $request)
    {
        $rows = $this->filteredQuery($request)
            ->orderBy('created_at', 'desc')
            ->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $headings = [
            'Loss Date',
            'Nature of Claim',
            'From Code',
            'From Location',
            'To Code',
            'To Location',
            'Invoice No.',
            'Invoice Date',
            'Transporter Name',
            'Truck Number',
            'LR No.',
            'LR Date'
        ];

        foreach ($headings as $index => $heading) {
            $sheet->setCellValue([$index + 1, 1], $heading);
        }

        $rowNo = 2;

        foreach ($rows as $row) {
            $sheet->setCellValue([1, $rowNo], $row->loss_date ? $row->loss_date->format('Y-m-d') : '');
            $sheet->setCellValue([2, $rowNo], $row->nature_of_claim);
            $sheet->setCellValueExplicit([3, $rowNo], (string)$row->from_location_code, DataType::TYPE_STRING);
            $sheet->setCellValue([4, $rowNo], $row->from_location);
            $sheet->setCellValueExplicit([5, $rowNo], (string)$row->to_location_code, DataType::TYPE_STRING);
            $sheet->setCellValue([6, $rowNo], $row->to_location);
            $sheet->setCellValueExplicit([7, $rowNo], (string)$row->invoice_no, DataType::TYPE_STRING);
            $sheet->setCellValue([8, $rowNo], $row->invoice_date ? $row->invoice_date->format('Y-m-d') : '');
            $sheet->setCellValue([9, $rowNo], $row->transporter_name);
            $sheet->setCellValue([9, $rowNo], $row->truck_no);
            $sheet->setCellValueExplicit([10, $rowNo], (string)$row->lr_no, DataType::TYPE_STRING);
            $sheet->setCellValue([11, $rowNo], $row->lr_date ? $row->lr_date->format('Y-m-d') : '');
           
            $rowNo++;
        }

        $sheet->getStyle('A1:N1')->getFont()->setBold(true);
        $sheet->freezePane('A2');
        $sheet->setAutoFilter('A1:N1');

        $writer = new Xlsx($spreadsheet);
        $fileName = 'Insurance_' . date('Y-m-d_H-i-s') . '.xlsx';

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $fileName);
    }

    private function filteredQuery(Request $request)
    {
        $query = Insurance::query();

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('nature_of_claim', 'like', '%' . $search . '%')
                    ->orWhere('from_location_code', 'like', '%' . $search . '%')
                    ->orWhere('from_location', 'like', '%' . $search . '%')
                    ->orWhere('to_location_code', 'like', '%' . $search . '%')
                    ->orWhere('to_location', 'like', '%' . $search . '%')
                    ->orWhere('invoice_no', 'like', '%' . $search . '%')
                    ->orWhere('transporter_name', 'like', '%' . $search . '%')
                    ->orWhere('lr_no', 'like', '%' . $search . '%');
            });
        }

        return $query;
    }

    private function fillLocation($code, $location)
    {
        if ($code == '' || $location != '') {
            return $location;
        }

        $plant = Siteplant::where('plant_site_code', $code)->first();

        if (!$plant) {
            return $location;
        }

        return $plant->plant_site_location_name ?? $plant->plant_site_name ?? $location;
    }

    private function createDirectories()
    {
        $directories = [
            public_path('uploads/insurance/invoice'),
            public_path('uploads/insurance/lr'),
            public_path('uploads/insurance/photos')
        ];

        foreach ($directories as $directory) {
            if (!File::exists($directory)) {
                File::makeDirectory($directory, 0755, true);
            }
        }
    }

    private function deletePhysicalFile($relativePath)
    {
        if (!$relativePath) {
            return;
        }

        $fullPath = public_path($relativePath);

        if (File::exists($fullPath)) {
            File::delete($fullPath);
        }
    }

    private function excelDate($value)
    {
        if (!$value) {
            return null;
        }

        try {
            if (is_numeric($value)) {
                return Date::excelToDateTimeObject($value)->format('Y-m-d');
            }

            $timestamp = strtotime($value);

            return $timestamp ? date('Y-m-d', $timestamp) : null;
        } catch (\Exception $e) {
            return null;
        }
    }

    
}