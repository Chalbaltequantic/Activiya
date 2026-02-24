<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;
	protected $table = 'appointments';

	protected $fillable = [
							'inv_number', 
							'inv_doc_date', 
							'lr_no', 
							'lr_date',
							'consignor_code', 
							'consignor_name', 
							'consignor_location', 
							'consignor_short_location',
							'consignee_code', 
							'consignee_name', 
							'consignee_location', 
							'consignee_short_location',
							'v_code',
							'vendor_name', 
							'cases_sale', 
							't_code',
							'truck_type', 
							'vehicle_no',
							'driver_name',
							'driver_no',
							'arrival_date',
							'delivery_remarks',
							'shipment_inv_value', 
							'delivery_gross_weight',	
							'company_code',
							'status',
							'created_by',
							'created_at',
							'truck_detail_updated_at',
							'truck_detail_updated_by'							
						  ];
						  
		public function histories()
		{
			return $this->hasMany(DeliveryStatusHistory::class);
		}

		public function latestDeliveryStatus()
		{
			return $this->hasOne(DeliveryStatusHistory::class)->latestOfMany();
		}	

		public function podFiles()
		{
			return $this->hasMany(PodFile::class);
		}

		public function podFront()
		{
			return $this->hasOne(PodFile::class)->where('file_type', 'podfront')->latest();
		}

		public function podBack()
		{
			return $this->hasOne(PodFile::class)->where('file_type', 'podback')->latest();
		}	
}
