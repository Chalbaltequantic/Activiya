<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tracking extends Model
{
    use HasFactory;
	protected $table = 'tracking';

	protected $fillable = [
							'indent_no',
							'customer_po_no',
							'origin',
							'destination',
							'vendor_name',
							'vehicle_type',
							'lr_no',
							'cases',
							'truck_no',
							'driver_number',
							'dispatch_date',
							'dispatch_time',
							'lead_time',
							'distance',
							'delivery_due_date',
							'shipment_status',
							'transit_status',
							'distance_covered',
							'current_location',
							'distance_to_cover',
							'tracking_link',
							'reporting_date',
							'reporting_time',
							'release_date',
							'release_time',
							'detention_days',
							'created_at',
							'created_by',
							'updated_by',
							'updatedby_vendor_consigon',
							'updatedby_vendor_consigon_at',
							'updatedby_vendor',
							'updatedby_vendor_at'
							
						  ];						  
	
}
