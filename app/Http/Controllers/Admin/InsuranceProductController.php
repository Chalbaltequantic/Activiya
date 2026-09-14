<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Insurance;
use App\Models\InsuranceProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class InsuranceProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index($insuranceId)
    {
        $insurance = Insurance::findOrFail($insuranceId);

        $products = InsuranceProduct::where('insurance_id', $insurance->id)
            ->orderBy('id', 'desc')
            ->get();

        return view('admin.insurance.products', compact(
            'insurance',
            'products'
        ));
    }

    public function lookupProduct(Request $request, $insuranceId)
    {
        $request->validate([
            'product_code' => 'required|string|max:100'
        ]);

        $insurance = Insurance::findOrFail($insuranceId);

        $productCode = trim($request->product_code);
        $invoiceNo = trim((string)$request->invoice_no);

        $damageRow = null;
        $shortageRow = null;

        if (Schema::hasTable('digiwim_preloading_operation_items')) {
            $query = DB::table('digiwim_preloading_operation_items')
                ->where('material_code', $productCode);

            if ($invoiceNo != '') {
                if (Schema::hasColumn('digiwim_preloading_operation_items', 'invoice_no')) {
                    $query->where('invoice_no', $invoiceNo);
                } elseif (Schema::hasColumn('digiwim_preloading_operation_items', 'invoice_challan_no')) {
                    $query->where('invoice_challan_no', $invoiceNo);
                } elseif (Schema::hasColumn('digiwim_preloading_operation_items', 'challan_no')) {
                    $query->where('challan_no', $invoiceNo);
                }
            }

            $rows = $query
                ->whereIn('good_status', ['Damage', 'Short'])
                ->get();

            $damageRow = $rows->first(function ($row) {
                return strtolower(trim((string)$row->good_status)) == 'damage';
            });

            $shortageRow = $rows->first(function ($row) {
                return strtolower(trim((string)$row->good_status)) == 'short';
            });
        }

        $description = '';
        $batchNo = '';
        $damageQty = 0;
        $shortageQty = 0;

        $sourceRow = $damageRow ?: $shortageRow;

        if ($sourceRow) {
            if (isset($sourceRow->material_description)) {
                $description = $sourceRow->material_description;
            }

            if (isset($sourceRow->batch_no)) {
                $batchNo = $sourceRow->batch_no;
            }
        }

        if ($damageRow) {
            if (isset($damageRow->qty)) {
                $damageQty = (float)$damageRow->qty;
            } elseif (isset($damageRow->quantity)) {
                $damageQty = (float)$damageRow->quantity;
            }
        }

        if ($shortageRow) {
            if (isset($shortageRow->qty)) {
                $shortageQty = (float)$shortageRow->qty;
            } elseif (isset($shortageRow->quantity)) {
                $shortageQty = (float)$shortageRow->quantity;
            }
        }

        $recoveryMrp = 0;

        if (Schema::hasTable('materials')) {
            $material = DB::table('materials')
                ->where('material_code', $productCode)
                ->first();

            if ($material) {
                if (isset($material->recovery_mrp)) {
                    $recoveryMrp = (float)$material->recovery_mrp;
                }

                if ($description == '' && isset($material->material_description)) {
                    $description = $material->material_description;
                }
            }
        }

        $damageValue = $damageQty * $recoveryMrp;
        $shortageValue = $shortageQty * $recoveryMrp;

        return response()->json([
            'success' => true,
            'found_operation_item' => $sourceRow ? true : false,
            'found_material' => $recoveryMrp > 0 ? true : false,
            'product_description' => $description,
            'batch_no' => $batchNo,
            'damage_quantity' => $damageQty,
            'shortage_quantity' => $shortageQty,
            'recovery_mrp' => $recoveryMrp,
            'damage_value' => round($damageValue, 2),
            'shortage_value' => round($shortageValue, 2),
            'total_value' => round($damageValue + $shortageValue, 2),
            'invoice_no' => $invoiceNo ?: $insurance->invoice_no,
            'lr_no' => $insurance->lr_no
        ]);
    }

    public function store(Request $request, $insuranceId)
    {
        $insurance = Insurance::findOrFail($insuranceId);

        $productCodes = $request->input('product_code', []);

        if (count($productCodes) > 100) {
            return back()->with('error', 'Maximum 100 product rows are allowed.');
        }

        $createdBy = Auth::guard('admin')->id();
        $savedCount = 0;

        DB::beginTransaction();

        try {
            for ($i = 0; $i < count($productCodes); $i++) {
                $productCode = trim((string)$request->input("product_code.$i"));

                $description = trim((string)$request->input("product_description.$i"));

                $batchNo = trim((string)$request->input("batch_no.$i"));

                $damageQty = (float)str_replace(',', '', $request->input("damage_quantity.$i", 0));

                $shortageQty = (float)str_replace(',', '', $request->input("shortage_quantity.$i", 0));

                $recoveryMrp = (float)str_replace(',', '', $request->input("recovery_mrp.$i", 0));

                $damageValue = (float)str_replace(',', '', $request->input("damage_value.$i", 0));

                $shortageValue = (float)str_replace(',', '', $request->input("shortage_value.$i", 0));

                if (
                    $productCode == '' &&
                    $description == '' &&
                    $batchNo == '' &&
                    $damageQty == 0 &&
                    $shortageQty == 0
                ) {
                    continue;
                }

                if ($recoveryMrp > 0) {
                    $damageValue = $damageQty * $recoveryMrp;
                    $shortageValue = $shortageQty * $recoveryMrp;
                }

                $totalValue = $damageValue + $shortageValue;

                InsuranceProduct::create([
                    'insurance_id' => $insurance->id,
                    'lr_no' => $request->input("lr_no.$i") ?: $insurance->lr_no,
                    'invoice_no' => $request->input("invoice_no.$i") ?: $insurance->invoice_no,
                    'product_code' => $productCode,
                    'product_description' => $description,
                    'batch_no' => $batchNo,
                    'damage_quantity' => $damageQty,
                    'shortage_quantity' => $shortageQty,
                    'recovery_mrp' => $recoveryMrp,
                    'damage_value' => round($damageValue, 2),
                    'shortage_value' => round($shortageValue, 2),
                    'total_value' => round($totalValue, 2),
                    'created_by' => $createdBy
                ]);

                $savedCount++;
            }

            if ($savedCount == 0) {
                DB::rollBack();

                return back()->with('error', 'Please enter at least one Product.');
            }

            DB::commit();

            return redirect()
                ->route('admin.insurance.products', $insurance->id)
                ->with('success', $savedCount . ' product(s) saved successfully.');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', $e->getMessage());
        }
    }
}