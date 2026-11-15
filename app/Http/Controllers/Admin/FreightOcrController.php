<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Jobs\ProcessFreightOcr;
use App\Models\Billdata;
use App\Models\FreightOcrBatch;
use App\Models\FreightOcrResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class FreightOcrController extends Controller 
{
	public function __construct(){$this->middleware('auth:admin');}
	public function start(Request $request){
	$data=$request->validate(['ids'=>'required|array|min:1|max:1000','ids.*'=>'required|integer|distinct']);
	$ids=$data['ids'];
	$bills=Billdata::whereIn('id',$ids)->where(function($q){$q->whereNull('submit')->orWhere('submit',0);})->where(function($q){$q->whereNull('f_return')->orWhere('f_return',0);})->get(['id']);
	if($bills->count()!==count($ids))return response()->json(['message'=>'Some selected bills are missing, submitted or returned'],422);
	$batch=DB::transaction(function()use($bills){
	$batch=FreightOcrBatch::create(['created_by'=>auth('admin')->id(),'total'=>$bills->count(),'status'=>'processing']);
	foreach($bills as $bill){$result=FreightOcrResult::create(['batch_id'=>$batch->id,'billdata_id'=>$bill->id,'status'=>'queued']);ProcessFreightOcr::dispatch($result->id)->afterCommit();}
	return $batch;
	});
	return response()->json(['batch_id'=>$batch->id,'total'=>$batch->total,'status'=>'processing'],202);
	}
	public function status(FreightOcrBatch $batch,Request $request)
	{
		if((int)$batch->created_by!==(int)auth('admin')->id())abort(403);
		
		$counts=$batch->results()->select('status',DB::raw('count(*) as total'))->groupBy('status')->pluck('total','status');
		$done=(int)($counts['valid']??0)+(int)($counts['invalid']??0)+(int)($counts['review_required']??0)+(int)($counts['failed']??0);
		
		$finished=$done >= $batch->total;
		
		if($finished && $batch->status!=='completed')$batch->update(['status'=>'completed']);
		$since=(int)$request->query('after',0);
		
		$results=$batch->results()->where('id','>',$since)->orderBy('id')->limit(1000)->get(['id','billdata_id','status','remarks','reasons','updated_at']);
		// Every poll also returns current state of all rows; avoids missing status transitions.
		$all=$batch->results()->get(['id','billdata_id','status','remarks','reasons','updated_at']);
		return response()->json(['batch_id'=>$batch->id,'total'=>$batch->total,'completed'=>$done,'finished'=>$finished,'counts'=>$counts,'results'=>$all]);
	}
	
	public function retry(FreightOcrBatch $batch)
	{
		if((int)$batch->created_by!==(int)auth('admin')->id())abort(403);
		$failed=$batch->results()->where('status','failed')->get();
		foreach($failed as $result)
		{
			$result->update(['status'=>'queued','remarks'=>null,'reasons'=>null]);ProcessFreightOcr::dispatch($result->id);
		}
			if($failed->isNotEmpty())$batch->update(['status'=>'processing']);
			return response()->json(['retried'=>$failed->count()]);
	}
}
