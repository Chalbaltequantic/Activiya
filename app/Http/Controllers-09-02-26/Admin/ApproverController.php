<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LoadApprovalHistory;
use App\Models\LoadSendHistory;
use App\Models\LoadSummary;
use App\Models\LoadStatusLog;
use Illuminate\Http\Request;
use Auth;

class ApproverController extends Controller
{
     public function __construct()
    {
       
		$this->middleware('auth:admin'); 
    }

	public function index()
    {
        $loads = LoadApprovalHistory::with('loadsummary')
            ->where('status', 'pending')
            ->orderByDesc('id')
            ->get();

         return view('admin.loadoptimizer.approver_list', compact('loads'));
    }
	
    /*public function action(Request $request)
    {
        $approval = LoadApprovalHistory::findOrFail($request->id);

        $approval->update([
            'status' => $request->status,
            'remarks' => $request->remarks,
            'approver_id' => auth()->id(),
            'action_at' => now()
        ]);

        if ($request->status === 'approved') {

            LoadSendHistory::create([
                'load_summary_id' => $approval->load_summary_id,
                'vendor_code' => $approval->vendor_code,
                'remarks' => 'Approved by approver',
                'sent_by' => auth()->id(),
                'sent_at' => now(),
                'allocation_source' => 'manual'
            ]);

            LoadSummary::where('id',$approval->load_summary_id)
                ->update(['vendor_code'=>$approval->vendor_code]);
        }

        return back()->with('success','Action completed');
    }*/
	
	
	 /**
     *  Approve / Reject
     */
   public function action(Request $request)
    {
        $request->validate([
            'approval_id' => 'required|exists:load_approval_history,id',
            'status'      => 'required|in:approved,rejected',
            'remarks'     => 'nullable|string|max:500'
        ]);

        DB::transaction(function () use ($request) {

             $approval = LoadApprovalHistory::lockForUpdate()
            ->findOrFail($request->approval_id);

			$load = LoadSummary::lockForUpdate()
				->findOrFail($approval->load_summary_id);

			
            // update approval history
            $approval->status      = $request->status;
            $approval->remarks     = $request->remarks;
            $approval->approver_id = Auth::user()->id();
            $approval->action_at   = now();
            $approval->save();
          
            
			$oldLoadStatus = $load->sent_status; // usually 'approval_required'

			//  Update load summary
			if ($request->status === 'approved') {
				$load->sent_status = 'approved';
			} else {
				$load->sent_status = 'rejected';
			}

			$load->vendor_approval_status  = $request->status;
			$load->vendor_approval_remarks = $request->remarks;
			$load->vendor_approved_by      = Auth::user()->id();
			$load->vendor_approved_at      = now();
			$load->save();
			
			LoadStatusLog::create([
				'load_summary_id' => $load->id,
				'reference_type'  => 'approval',
				'reference_id'    => $approval->id,
				'old_status'      => $oldLoadStatus,
				'new_status'      => $load->sent_status,
				'changed_by_id'   => Auth::user()->id(),
				'changed_by_role' => 'approver',
				'created_at'      => now()
			]);
			
			
			
        });
		
        return back()->with('success', 'Approval decision recorded');
    }
}