<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ratedata extends Model
{
    use HasFactory;
	protected $table = 'rate_master';

	protected $fillable = [
							'consignor_name', 
							'consignor_code',
							'consignor_location',
							's5_consignor_short_name_and_location',
							'consignee_name',
							'consignee_code',
							'consignee_location',
							'd5_consignor_short_name_and_location',
							'mode',
							'logic',
							'vendor_code',
							'vendor_name',
							't_code',
							'truck_type',
							'validity_start',
							'validity_end',
							'a_amount',
							'tat',
							'rank',
							'distance',
							'custom1',
							'custom2',
							'custom3',
							'custom4',
							'custom5',
							'status',
							'created_by',
							'updated_by',
							'created_at',
							'updated_at'
							
						  ];
						  
}
