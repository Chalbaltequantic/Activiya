<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Spotby extends Model
{
    use HasFactory;
	protected $table = 'spotby';

	protected $fillable = [
							'from',
							'to' ,
							'vehicle_type',
							'valid_from',
							'valid_upto',
							'no_of_vehicles',
							'goods_qty',
							'uom',
							'loading_charges',
							'unloading_charges',
							'special_instruction',
							'rfq_start_date_time',
							'rfq_end_date_time',
							'created_at',
							'created_by',
							'updated_by',
							'freeze_vendor_name',
							'final_rate',
							'approve_status',
							'freeze_date',
							'freeze_by',
							'approve_by',
							'approve_date',
							
						  ];
						  
	/*public function vendors()
    {
        return $this->hasMany(SpotbyVendor::class, 'spotby_id');
    }*/


	public function vendors()
	{
		return $this->belongsToMany(
        Vendor::class,
        'spotby_vendors',   // pivot table
        'spotby_id',        // FK on pivot to Spotby
        'vendor_id'         // FK on pivot to Vendor
		);
	}
    public function quotes()
    {
        return $this->hasMany(SpotbyVendorQuote::class, 'spotby_id');
    }
	
	public function spotbyVendors()
	{
		return $this->hasMany(SpotbyVendor::class, 'spotby_id');
	}

	// Average price accessor (optional)
	public function getAveragePriceAttribute()
	{
		return $this->quotes()->avg('price');
	}
	
	public function freezeByUser()
	{
		return $this->belongsTo(Admin::class, 'freeze_by');
	}

	public function approvedByUser()
	{
		return $this->belongsTo(Admin::class, 'approve_by');
	}

	
}
