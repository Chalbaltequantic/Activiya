<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class InvoiceAnnexure extends Model
{
	   //'vehicle_no','vehicle_size','actual_weight','no_of_packages',
	   
	   protected $fillable = [
        'invoice_id',
        'customer_ref_no','obd_no','arrival_date','dispatch_date','loading_detention_days', 'loading_detention_charge', 'loading_charge', 'loading_pt_det_charge', 'reporting_date', 
		'unloading_date', 'unloading_detention_days', 'transit_days', 'unloading_detention_charge',
		'unloading_charge', 'unloading_pt_det_charge', 
        'freight','gr_charges', 'fix_rental', 'toll_tax', 'green_tax'
        
	];
}