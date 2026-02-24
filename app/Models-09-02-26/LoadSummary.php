<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoadSummary extends Model
{
	protected $table="load_summary";
   protected $fillable = [
        'reference_no','origin_name_code','destination_name_code',
        't_mode','truck_id', 'truck_code', 'total_weight','total_volume',
        'zw_util','zv_util','gross_util','is_qualified','parent_reference_no','optimization_score',
		'approval_status','approval_remark','approval_sent_by', 'approval_sent_at',
        'status','created_by','updated_by', 'approved_by', 'approved_at', 'vendor_code', 
		'vendor_name','vendor_code_source', 'vendor_code_updated_at',
		'vendor_approval_status','vendor_approval_remarks','vendor_approved_by','vendor_approved_at',
		'accepted_by', 'accepted_at'
    ];
	
	public function truck()
	{
		return $this->belongsTo(TruckMaster::class, 'truck_id');
	}
	public function items()
	{
		return $this->hasMany(LoadSummaryItem::class);
	}
	public function sendHistory()
	{
		return $this->hasMany(LoadSendHistory::class, 'load_summary_id');
	}
	//placement status 
	
	public function placementLogs()
	{
		return $this->hasMany(
			LoadPlacementStatusLog::class,
			'load_summary_id'
		);
	}

	public function latestPlacement()
	{
		return $this->hasOne(
			LoadPlacementStatusLog::class,
			'load_summary_id'
		)->latest('id');
	}
	 public function truckType()
    {
        return $this->belongsTo(
            RateMaster::class,
            'truck_code',   // FK in load_summary
            't_code'      // PK / unique key in rate_master
        );
    }
	
	public function latestSendHistory()
	{
		return $this->hasOne(LoadSendHistory::class)
			->latestOfMany(); // Laravel 8+
	}
	
	
}
