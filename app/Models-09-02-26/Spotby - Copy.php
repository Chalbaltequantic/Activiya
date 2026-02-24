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
							
						  ];
						  
	public function vendors()
    {
        return $this->hasMany(SpotbyVendor::class, 'spotby_id');
    }	
	/*public function vendors()
    {
        return $this->belongsToMany(Vendor::class, 'spotby_vendors')
            ->withTimestamps();
    }
	*/
    public function quotes()
    {
        return $this->hasMany(SpotbyVendorQuote::class, 'spotby_id');
    }
	
}
