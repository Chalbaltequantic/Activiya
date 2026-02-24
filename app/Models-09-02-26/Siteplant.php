<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Siteplant extends Model
{
    use HasFactory;
	protected $table = 'site_plants';

	protected $fillable = [
							'plant_site_code',
							'plant_site_location_name',
							'site_code',
							'site_status',
							'plant_site_name',
							'street_house_number',
							'street1',
							'street2',
							'city',
							'post_code',
							'state_code',
							'state_name',
							'pan_no',
							'food_license_no',
							'food_license_expiry' ,
							'gstin_number',
							'site_executive_name',
							'site_executive_contact_no',
							'site_executive_mail_id',
							'site_incharge_name',
							'site_incharge_contact_no',
							'site_incharge_mail_id',
							'site_manager_name',
							'site_manager_contact_no',
							'site_manager_mail_id',
							'region',
							'company_code',
							'company_type',
							's5_d5_short_name',
							'created_at',
							'created_by',
							'status', 
							'updated_at',
							'updated_by'
			
						  ];
}
