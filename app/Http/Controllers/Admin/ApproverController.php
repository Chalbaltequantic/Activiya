<?php
namespace App\Http\Controllers\Admin;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\LoadApprovalHistory;
use App\Models\LoadSendHistory;
use App\Models\LoadSummary;
use App\Models\ManualLoadSummary;
use App\Models\LoadStatusLog;
use Illuminate\Http\Request;
use Auth;

class ApproverController extends Controller
{
     public function __construct()
    {
       
		$this->middleware('auth:admin'); 
    }

	/*public function index()
    {
        $loads = LoadApprovalHistory::with('loadsummary')
            ->where('status', 'pending')
            ->orderByDesc('id')
            ->get();

         return view('admin.loadoptimizer.approver_list', compact('loads'));
    }*/
	
	public function index()
	{
		/**
		 * ======================================
		 * AUTO LOADS (load_summary)
		 * ======================================
		 */
		$autoLoads = DB::table('load_approval_history as lah')
			->join('load_summary as ls', function ($join) {
				$join->on('ls.id', '=', 'lah.load_summary_id')
					 ->where('lah.source_type', 'AUTO');
			})
			->leftJoin('truck_master as tm', 'tm.id', '=', 'ls.truck_id')
			->where('lah.status', 'pending')
			->select([
				'lah.id',
				'lah.reference_no',
				'ls.origin_name_code',
				'ls.origin_name',
				'ls.destination_name_code',
				'ls.destination_name',
				'ls.t_mode',
				'tm.description as truck_description',
				'ls.vendor_name',
				'ls.sent_remarks',
				'ls.sent_at',
				DB::raw("'AUTO' as source_type")
			]);

		/**
		 * ======================================
		 * MANUAL LOADS (manual_load_summary)
		 * ======================================
		 */
		$manualLoads = DB::table('load_approval_history as lah')
			->join('manual_load_summary as mls', function ($join) {
				$join->on('mls.id', '=', 'lah.load_summary_id')
					 ->where('lah.source_type', 'MANUAL');
			})
			//->leftJoin('truck_master as tm', 'tm.code', '=', 'mls.truck_code')
			->where('lah.status', 'pending')
			->select([
				'lah.id',
				'lah.reference_no',
				'mls.origin_name_code',
				'mls.origin_name',
				'mls.destination_name_code',
				'mls.destination_name',
				'mls.t_mode',
				'mls.truck_name as truck_description',
				'mls.vendor_name',
				'mls.sent_remarks',
				'mls.sent_at',
				DB::raw("'MANUAL' as source_type")
			]);

		/**
		 * ======================================
		 * MERGE + SORT
		 * ======================================
		 */
		$loads = $autoLoads
			->unionAll($manualLoads)
			->orderByDesc('sent_at')
			->get();

		return view('admin.loadoptimizer.approver_list', compact('loads'));
	}

	
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

            $reference_no = $request->reference_no;
            $source_type = $request->source_type;

			$approval = LoadApprovalHistory::lockForUpdate()
            ->findOrFail($request->approval_id);

			if($source_type==='AUTO')
			{
			$load = LoadSummary::lockForUpdate()
				->findOrFail($approval->load_summary_id);
			}
			else{
				$load = ManualLoadSummary::lockForUpdate()
				->findOrFail($approval->load_summary_id);
			}
			
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
				'reference_no'      => $reference_no,
				'source_type'      => $source_type,
				'changed_by_id'   => Auth::user()->id(),
				'changed_by_role' => 'approver',
				'created_at'      => now()
			]);
			
			
			
        });
		
        return back()->with('success', 'Approval decision recorded');
    }
}