<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class InvoiceAnnexure extends Model
{
	   protected $fillable = [
        'invoice_id',
        'customer_ref_no','obd_no','arrival_date','delivery_date','transit_days',
        'vehicle_no','vehicle_size','actual_weight','no_of_packages',
        'freight','charge_weight','loading_charge','unloading_charge',
        'loading_pt_det_charge','unloading_pt_det_charge',
        'incentive_charge','other_charge','two_point_delivery',
        'toll_tax','green_tax'
	];
}