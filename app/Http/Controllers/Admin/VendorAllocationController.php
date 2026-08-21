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

	
	/*public function send(Request $request)
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
		/*if ($allocationSource === 'Auto Allocation' || $vendorRank == 1) {

					
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
		/*if ($source === 'Manual Edit' && $vendorRank >= 2) {

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
		
	}*/
	
public function send(Request $request)
{
    try {

        if ($request->source_type === 'AUTO') {

            $load = LoadSummary::findOrFail($request->load_id);

        } else {

            $load = ManualLoadSummary::findOrFail($request->load_id);
        }


        /* Get vendor allocation information
		Use saved database values because these are the final values after
         Edit Vendor operation.
        */

        $vendorRank = (int) $load->vendor_rank;
        $allocationSource = trim(
            (string) $load->vendor_code_source
        );


        /* CASE 1:
         Auto Allocation OR Rank 1
		 Rank 1 does not require approval.
         Send directly to vendor.
        
        */

        if (
            $allocationSource === 'Auto Allocation'
            || $vendorRank == 1
        ) {

            $send = LoadSendHistory::create([

                'load_summary_id' => $load->id,

                'vendor_code' => $load->vendor_code,

                'vendor_rank' => $vendorRank,

                'reference_no' => $load->reference_no,

                'source_type' => $request->source_type,

                'allocation_source' => $allocationSource,

                'remarks' => $request->remarks,

                'sent_by' => Auth::user()->id,

                'sent_at' => now(),

                'status' => 'pending'
            ]);


            /*
             * Update Load
             */

            $load->sent_to_vendor = 1;

            $load->sent_status = 'sent_to_vendor';

            $load->sent_remarks = $request->remarks;

            $load->sent_by = Auth::user()->id;

            $load->sent_at = now();

            $load->save();


            /*
             * Status Log
             */

            LoadStatusLog::create([

                'load_summary_id' => $load->id,

                'reference_no' => $load->reference_no,

                'source_type' => $request->source_type,

                'reference_type' => 'send',

                'reference_id' => $send->id,

                'old_status' => null,

                'new_status' => 'pending',

                'changed_by_id' => Auth::id(),

                'changed_by_role' => 'admin'
            ]);


            return response()->json([
                'status' => 'sent_to_vendor',
                'message' => 'Load sent directly to vendor.'
            ]);
        }


        /* CASE 2:
         Manual Edit + Rank 2 or Higher
         Approval is required.
        
        */

        if (
            $allocationSource === 'Manual Edit'
            && $vendorRank >= 2
        ) {

            /*
             * Save previous status for history
             */

            $oldStatus = $load->sent_status;


            /*
             * Update Load
             */

            $load->sent_status = 'approval_required';

            $load->sent_remarks = $request->remarks;

            $load->sent_by = Auth::user()->id;

            $load->sent_at = now();

            $load->save();


            /* Create Send History */

            $send = LoadSendHistory::create([

                'load_summary_id' => $load->id,

                'vendor_code' => $load->vendor_code,

                'reference_no' => $load->reference_no,

                'source_type' => $request->source_type,

                'vendor_rank' => $vendorRank,

                'allocation_source' => 'Manual Edit',

                'remarks' => $request->remarks,

                'sent_by' => Auth::user()->id,

                'sent_at' => now(),

                'status' => 'approval_required'
            ]);


            /* Create Approval History
             These columns exactly match your table structure.
            */

            $approval = LoadApprovalHistory::create([

                'load_summary_id' => $load->id,

                'reference_no' => $load->reference_no,

                'source_type' => $request->source_type,

                'vendor_code' => $load->vendor_code,

                'approver_id' => null,

                'status' => 'pending',

                'remarks' => $request->remarks,

                'action_at' => null
            ]);


            /* Create Status Log*/

            LoadStatusLog::create([

                'load_summary_id' => $load->id,

                'reference_no' => $load->reference_no,

                'source_type' => $request->source_type,

                'reference_type' => 'approval',

                'reference_id' => $approval->id,

                'old_status' => $oldStatus,

                'new_status' => 'approval_required',

                'changed_by_id' => Auth::id(),

                'changed_by_role' => 'admin'
            ]);


            return response()->json([

                'status' => 'sent_to_approver',

                'message' => 'Load sent to approver successfully.',

                'approval_id' => $approval->id
            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Neither condition matched
        |--------------------------------------------------------------------------
        |
        | This response is VERY IMPORTANT for debugging.
        |
        */

        return response()->json([

            'status' => 'error',

            'message' => 'Vendor allocation condition did not match.',

            'debug' => [

                'load_id' => $load->id,

                'reference_no' => $load->reference_no,

                'vendor_code' => $load->vendor_code,

                'vendor_rank' => $vendorRank,

                'vendor_code_source' => $allocationSource,

                'source_type' => $request->source_type
            ]

        ], 422);


    } catch (\Throwable $e) {

        \Log::error(
            'Vendor allocation send failed',
            [
                'load_id' => $request->load_id,

                'source_type' => $request->source_type,

                'error' => $e->getMessage(),

                'line' => $e->getLine(),

                'file' => $e->getFile()
            ]
        );


        return response()->json([

            'status' => 'error',

            'message' => $e->getMessage()

        ], 500);
    }
}
}