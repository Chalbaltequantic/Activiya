<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\LoadApprovalHistory;
use App\Models\LoadSendHistory;

public function send(Request $request)
{
    $load = LoadSummary::findOrFail($request->load_id);

    $vendorRank = $request->vendor_rank; // pass from UI
    $allocationSource = $request->allocation_source; // auto/manual

    /**
     * AUTO allocation OR Rank 1 - Direct send to vendor
     */
    if ($allocationSource === 'auto' || $vendorRank == 1) {

        LoadSendHistory::create([
            'load_summary_id' => $load->id,
            'vendor_code' => $request->vendor_code,
            'remarks' => $request->remarks,
            'sent_by' => auth()->id(),
            'sent_at' => now(),
            'allocation_source' => $allocationSource
        ]);

        $load->update(['vendor_code' => $request->vendor_code]);

        return response()->json(['status' => 'sent_to_vendor']);
    }

    /**
     * MANUAL + Rank ≥ 2  Approver flow
     */
    LoadApprovalHistory::create([
        'load_summary_id' => $load->id,
        'vendor_code' => $request->vendor_code,
        'approver_id' => null,
        'status' => 'pending'
    ]);

    return response()->json(['status' => 'sent_to_approver']);
}