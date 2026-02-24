<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LoadApprovalHistory;
use App\Models\LoadSendHistory;
use App\Models\LoadSummary;
use App\Models\ManualLoadSummary;
use App\Models\LoadStatusLog;
use App\Models\LoadPlacementStatusLog;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;


use Auth;

class VendorLoadController extends Controller
{
	public function __construct()
    {
		$this->middleware('auth:admin'); 
    }
	
	
	public function accept(Request $request, $id)
	{
		try {

			DB::beginTransaction();

			$source_type = $request->source_type;
			$reference_no = $request->reference_no;
			// load data
			if($source_type==='AUTO')
			{
				$load = LoadSummary::lockForUpdate()->find($id);
			}
			else{
				$load = ManualLoadSummary::lockForUpdate($id);
			}
			if (!$load) {
				throw new \Exception('Load not found');
			}

			// Vendor can accept ONLY if sent_to_vendor
			if ($load->sent_status !== 'sent_to_vendor') {
				throw new \Exception('Load not eligible for acceptance. Current status: ' . $load->sent_status);
			}

			$oldStatus = $load->sent_status;

			// Update load summary
			$load->sent_status   = 'accepted';
			$load->accepted_at   = now();
			$load->accepted_by   = Auth::user()->id;
			$load->save();

			//Update send history 
			$sendHistory = LoadSendHistory::where('load_summary_id', $load->id)
			->where('reference_no',$reference_no)
			->where('source_type',$source_type)		
			//->where('vendor_code', Auth::user()->vendor_code ?? null)
			->latest()
			->first();

			if (!$sendHistory) {
				throw new \Exception('LoadSendHistory record not found for vendor');
			}

			$sendHistory->status        = 'accepted';
			$sendHistory->responded_at  = now();
			$sendHistory->save();

			// Status audit log
			LoadStatusLog::create([
				'load_summary_id' => $load->id,
				'reference_type'  => 'vendor_accept', // must match ENUM
				'reference_id'    => $sendHistory->id,
				'old_status'      => $oldStatus,
				'new_status'      => 'accepted',
				'changed_by_id'   => Auth::user()->id,
				'changed_by_role' => 'vendor',
				'reference_no' => $reference_no,
				'source_type' => $source_type,
				'changed_by_role' => 'vendor',
				
				
			]);

			DB::commit();

			return response()->json([
				'status'  => true,
				'message' => 'Load accepted successfully'
			]);

		} catch (\Throwable $e) {

			DB::rollBack();

			// Log full error
			Log::error('Vendor Load Accept Failed', [
				'load_id' => $id,
				'user_id' => Auth::user()->id,
				'error'   => $e->getMessage(),
				'trace'   => $e->getTraceAsString()
			]);

			return response()->json([
				'status'  => false,
				'message' => $e->getMessage() // send real error to frontend
			], 500);
		}
	}
	
	
	public function deploy(Request $request)
	{
		$request->validate([
			'id'             => 'required|exists:load_summary,id', // load_summary_id
			'vehicle_number' => 'required|string|max:50',
			'driver_name'    => 'required|string|max:100',
			'driver_mobile'  => 'required|string|max:20'
		]);

		try {
			DB::beginTransaction();

			$loadSummaryId = $request->id;
			$vendorCode    = Auth::user()->vendor_code;
			
			$source_type = $request->source_type;
			$reference_no = $request->reference_no;

			// Fetch latest send history for this load + vendor
			if(!empty($vendorCode))
			{
				$send = LoadSendHistory::lockForUpdate()
					->where('load_summary_id', $loadSummaryId)
					->where('vendor_code', $vendorCode)
					->where('reference_no',$reference_no)
					->where('source_type',$source_type)	
					->orderByDesc('id')
					->first();
			}
			else {
					$send = LoadSendHistory::lockForUpdate()
					->where('load_summary_id', $loadSummaryId)
					->where('reference_no',$reference_no)
					->where('source_type',$source_type)	
					->orderByDesc('id')
					->first();
			}	

			if (!$send) {
				throw new \Exception('No active load assignment found for this vendor');
			}

			// Update truck details
			$send->update([
				'status'  => 'deployed',
				'vehicle_number' => $request->vehicle_number,
				'driver_name'    => $request->driver_name,
				'driver_mobile'  => $request->driver_mobile,
				'responded_at'   => now()
			]);

			// Status audit log
			LoadStatusLog::create([
				'load_summary_id' => $loadSummaryId,
				'reference_type'  => 'vendor_deploy',
				'reference_id'    => $send->id,
				'old_status'      => 'accepted',
				'new_status'      => 'deployed',
				'changed_by_id'   => Auth::id(),
				'reference_no' => $reference_no,
				'source_type' => $source_type,
				'changed_by_role' => 'vendor'
			]);

			DB::commit();

			return response()->json([
				'status'  => true,
				'message' => 'Vehicle deployed successfully'
			]);

		} catch (\Throwable $e) {

			DB::rollBack();

			Log::error('Deploy failed', [
				'load_summary_id' => $request->id,
				'vendor_code'     => Auth::user()->vendor_code,
				'error'           => $e->getMessage()
			]);

			return response()->json([
				'status'  => false,
				'message' => $e->getMessage()
			], 500);
		}
	}

    //  REJECT LOAD
  
	public function reject(Request $request)
	{
		$request->validate([
			'id' => 'required|exists:load_summary,id',
			'remarks' => 'required|string|max:500'
		]);

		try {
			DB::beginTransaction();
			
			$source_type = $request->source_type;
			$reference_no = $request->reference_no;
			if($source_type==='AUTO')
			{
				$load = LoadSummary::lockForUpdate()->find($request->id);
			}
			else
			{
				$load = ManualLoadSummary::lockForUpdate()->find($request->id);
			}
			
			if (!$load) {
				throw new \Exception('Load not found');
			}

			// Vendor can accept ONLY if sent_to_vendor
			if ($load->sent_status !== 'sent_to_vendor') {
				throw new \Exception('Load not eligible for acceptance. Current status: ' . $load->sent_status);
			}

			$oldStatus = $load->sent_status;

			// Update load summary
			$load->sent_status   = 'rejected';
			$load->accepted_at   = now();
			$load->accepted_by   = Auth::user()->id;
			$load->save();


			$vendorCode = Auth::user()->vendor_code;
			if(!empty($vendorCode))
			{
			$send = LoadSendHistory::lockForUpdate()
				->where('load_summary_id', $request->id)
				->where('vendor_code', $vendorCode)
				->where('reference_no',$reference_no)
				->where('source_type',$source_type)	
				->orderByDesc('id')
				->first();
			}
			else{
				$send = LoadSendHistory::lockForUpdate()
				->where('load_summary_id', $request->id)
				->where('reference_no',$reference_no)
				->where('source_type',$source_type)	
				->orderByDesc('id')
				->first();
			}
			
			
			if (!$send) {
				throw new \Exception('No active assignment found');
			}

			if ($send->status === 'rejected') {
				throw new \Exception('Already rejected');
			}

			$send->update([
				'status' => 'rejected',
				'rejection_reason' => $request->remarks,
				'responded_at'  => now()
			]);

			LoadStatusLog::create([
				'load_summary_id' => $send->load_summary_id,
				'reference_type'  => 'vendor_reject',
				'reference_id'    => $send->id,
				'old_status'      => $oldStatus,
				'new_status'      => 'rejected',
				'reference_no' => $reference_no,
				'source_type' => $source_type,
				'changed_by_id'   => Auth::id(),
				'changed_by_role' => 'vendor'
			]);

			DB::commit();

			return response()->json([
				'status' => true,
				'message' => 'Load rejected. Penalty may apply.'
			]);

		} catch (\Throwable $e) {
			DB::rollBack();

			return response()->json([
				'status' => false,
				'message' => $e->getMessage()
			], 500);
		}
	}
	
	//placementStatus
	public function submitPlacementStatus(Request $request)
	{
		$request->validate([
			'load_id'          => 'required|exists:load_summary,id',
			'placement_status' => 'required|string',
			'lr_no'            => 'nullable|string|max:50',
			'remarks'          => 'nullable|string|max:255'
		]);

		try {
			DB::transaction(function () use ($request) {

				$source_type = $request->source_type;
				$reference_no = $request->reference_no;
				
				if($source_type==='AUTO')
				{
					$load = LoadSummary::lockForUpdate()->findOrFail($request->load_id);
				}
				else{
					$load = ManualLoadSummary::lockForUpdate()->findOrFail($request->load_id);
				}
				// Prevent update if not accepted
				if ($load->sent_status !== 'accepted') {
					abort(403, 'Load not eligible for placement update');
				}

				LoadPlacementStatusLog::create([
					'load_summary_id' => $load->id,
					'reference_no'    => $request->reference_no,
					'source_type'     => $request->source_type,
					'vendor_code'     => Auth::user()->vendor_code,
					'placement_status'=> $request->placement_status,
					'lr_no'           => $request->lr_no,
					'remarks'         => $request->remarks,					
					'remarks'         => $request->remarks,
					'created_by'      => Auth::id(),
					'created_by_role' => 'vendor'
				]);
			});

			return response()->json([
				'status'  => true,
				'message' => 'Placement status updated'
			]);

		} catch (\Throwable $e) {
			return response()->json([
				'status'  => false,
				'message' => $e->getMessage()
			], 500);
		}
	}

}