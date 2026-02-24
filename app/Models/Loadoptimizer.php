<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Loadoptimizer extends Model
{
    use HasFactory;
	use SoftDeletes;
	 
	protected $table = 'load_optimizer';

	protected $fillable = [
							'reference_no',
							'origin_name_code',
							'destination_name_code',
							'origin_name',
							'destination_city',
							'sku_code',
							'sku_description',
							'priority',
							'sku_class',
							't_mode',
							'required_delivery_date',
							'qty',
							'z_total_weight',
							'z_total_volume',							
							'status',
							'updated_by',
							'created_by',
							'approval_sent_by',
							'approval_sent_at',
							'approved_at',
							'approved_by'
							
						  ];
	protected $dates = ['deleted_at'];						
							
	public function summaryItems()
	{
		return $this->hasMany(LoadSummaryItem::class);
	}						
	
}
