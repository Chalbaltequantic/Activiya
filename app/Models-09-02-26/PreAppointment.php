<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PreAppointment extends Model
{
    use HasFactory;
	protected $table = 'preappointments';

	protected $fillable = [
							'inv_number', 
							'inv_doc_date', 
							'po_no', 
							'po_date',
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
							'remarks',
							'driver_name',
							'driver_no',
							'arrival_date',
							'delivery_remarks',
							'shipment_inv_value', 
							'delivery_gross_weight',	
							'company_code',
							'no_of_cases_sale',
							'status',
							'created_by',
							'created_at',
							'truck_detail_updated_at',
							'truck_detail_updated_by',
							'appointment_date',
							'appointment_status',
							'appointment_date_updated_at',
							'appointment_date_updated_at'
							
						  ];
						  
		public function histories()
		{
			return $this->hasMany(PreappointmentDeliveryStatusHistory::class, 'appointment_id','id');
		}

		public function latestDeliveryStatus()
		{
			return $this->hasOne(PreappointmentDeliveryStatusHistory::class, 'appointment_id','id')->latestOfMany();
		}	

		public function podFiles()
		{
			return $this->hasMany(PreappointmentPodFile::class, 'appointment_id','id');
		}

		public function podFront()
		{
			return $this->hasOne(PreappointmentPodFile::class, 'appointment_id','id')->where('file_type', 'podfront')->latest();
		}

		public function podBack()
		{
			return $this->hasOne(PreappointmentPodFile::class, 'appointment_id','id')->where('file_type', 'podback')->latest();
		}	
}
