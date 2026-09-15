<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Insurance;
use Illuminate\Http\Request;

class InsuranceClaimController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index(Request $request)
    {
        $query = Insurance::with('products')
            ->withCount('products');

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('claim_no', 'like', '%' . $search . '%')
                    ->orWhere('invoice_no', 'like', '%' . $search . '%')
                    ->orWhere('lr_no', 'like', '%' . $search . '%')
                    ->orWhere('transporter_name', 'like', '%' . $search . '%')
                    ->orWhere('from_location', 'like', '%' . $search . '%')
                    ->orWhere('to_location', 'like', '%' . $search . '%');
            });
        }

        $claims = $query
            ->orderBy('id', 'desc')
            ->paginate(20)
            ->withQueryString();

        return view('admin.insurance.claim-bills', compact('claims'));
    }

    public function show($id)
    {
        $insurance = Insurance::with([
            'products' => function ($query) {
                $query->orderBy('id', 'asc');
            }
        ])->findOrFail($id);

        /*
         * Policy information.
         *
         * Currently Insurance table does not contain Policy No.,
         * Policy From and Policy To.
         *
         * These can later be fetched from Policy Master/Settings.
         */
        $policyNo = '';
        $policyFrom = '';
        $policyTo = '';

        /*
         * Product Claim Amount
         */
        $basicClaimAmount = $insurance->products->sum(function ($product) {
            return (float)$product->total_value;
        });

        /*
         * GST 5%
         */
        $gstPercent = 5;

        $gstAmount = round(
            $basicClaimAmount * $gstPercent / 100,
            2
        );

        /*
         * Amount after GST
         */
        $amountAfterGst = round(
            $basicClaimAmount + $gstAmount,
            2
        );

        /*
         * Additional 10%
         */
        $additionalPercent = 10;

        $additionalAmount = round(
            $amountAfterGst * $additionalPercent / 100,
            2
        );

        /*
         * Final Claim Amount
         */
        $finalClaimAmount = round(
            $amountAfterGst + $additionalAmount,
            2
        );

        return view('admin.insurance.claim-bill-print', compact(
            'insurance',
            'policyNo',
            'policyFrom',
            'policyTo',
            'basicClaimAmount',
            'gstPercent',
            'gstAmount',
            'amountAfterGst',
            'additionalPercent',
            'additionalAmount',
            'finalClaimAmount'
        ));
    }
}