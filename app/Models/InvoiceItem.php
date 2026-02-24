<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class InvoiceItem extends Model
{
	   protected $fillable = [
		'invoice_id','description','taxable','gst_percent',
		'cgst','sgst','igst','total',
		'lr_no','lr_date','vehicle_dispatch_date',
		'from_location','to_location','po_no'
	];
}