<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\LoadApprovalHistory;
use App\Models\LoadSendHistory;
use App\Models\LoadSummary;
use App\Models\ManualLoadSummary;
use App\Models\LoadStatusLog;
use Auth;
class VendorAllocationController extends Controller
{
	public function __construct()
    {
		$this->middleware('auth:admin'); 
    }

	
	public function send(Request $request)
	{
		//$load = LoadSummary::findOrFail($request->load_id);
		
		if ($request->source_type === 'AUTO') {
				$load = LoadSummary::lockForUpdate()->findOrFail($request->load_id);
			} else {
				$load = ManualLoadSummary::lockForUpdate()->findOrFail($request->load_id);
			}

		$vendorRank = $request->vendor_rank; // pass from UI
		$allocationSource = $request->allocation_source; // auto allocation / manual edit allocation

		/**
		 * AUTO allocation OR Rank 1 - Direct send to vendor
		 */
		if ($allocationSource === 'Auto Allocation' || $vendorRank == 1) {

					
			$send = LoadSendHistory::create([
					'load_summary_id'  => $load->id,
					'vendor_code'      => $load->vendor_code,
					'vendor_rank'      => $vendorRank,
					'reference_no'		=> $load->reference_no,
					'source_type'		=> $request->source_type,
					'allocation_source'=> 'Auto Allocation',
					'remarks'          => $request->remarks,
					'sent_by'          => Auth::user()->id,
					'sent_at'          => now(),
					'status'           => 'pending'
				]);

			$load->sent_to_vendor = 1;
			$load->sent_status    = 'sent_to_vendor';
			$load->sent_remarks   = $request->remarks;
			$load->sent_by        = Auth::user()->id;
			$load->sent_at        = now();
			$load->save();
			
			LoadStatusLog::create([
			'load_summary_id' => $load->id,
			'reference_no'		=> $load->reference_no,
			'source_type'		=> $request->source_type,
			'reference_type'  => 'send',
			'reference_id'    => $send->id,
			'old_status'      => null,
			'new_status'      => 'pending',
			'changed_by_id'   => Auth::id(),
			'changed_by_role' => 'admin'
		]);

			return response()->json(['status' => 'sent_to_vendor']);
		}

		/**
		 * MANUAL + Rank ≥ 2  Approver flow
		 */
		if ($source === 'Manual Edit' && $vendorRank >= 2) {

				$load->sent_status = 'approval_required';
				$load->save();
				
				
				$send = LoadSendHistory::create([
					'load_summary_id'  => $load->id,
					'vendor_code'      => $load->vendor_code,
					'reference_no'		=> $load->reference_no,
					'source_type'		=> $request->source_type,
					'vendor_rank'      => $vendorRank,
					'allocation_source'=> 'Manual Edit',
					'remarks'          => $request->remarks,
					'sent_by'          => Auth::user()->id,
					'sent_at'          => now(),
					'status'           => 'approval_required'
				]);
				
				
				$approval =  LoadApprovalHistory::create([
					'load_summary_id' => $load->id,
					'vendor_code' => $request->vendor_code,
					'approver_id' => null,
					'status' => 'pending'
				]);
				
				LoadStatusLog::insert([
				[
					'load_summary_id' => $load->id,
					'reference_no'		=> $load->reference_no,
					'source_type'		=> $request->source_type,
					'reference_type'  => 'send',
					'reference_id'    => $send->id,
					'old_status'      => null,
					'new_status'      => 'pending',
					'changed_by_id'   => Auth::id(),
					'changed_by_role' => 'admin',
					'created_at'      => now()
				]
			]);

				return response()->json(['status' => 'sent_to_approver']);
			} 
		
	}

}