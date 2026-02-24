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
        'status','created_by','updated_by', 'approved_by', 'approved_at'
    ];
	
	public function truck()
	{
		return $this->belongsTo(TruckMaster::class, 'truck_id');
	}
	public function items()
	{
		return $this->hasMany(LoadSummaryItem::class);
	}
}
