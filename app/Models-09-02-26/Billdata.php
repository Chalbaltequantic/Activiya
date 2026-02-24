<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Billdata extends Model
{
    use HasFactory;
	protected $table = 'bill_data_upload';

	protected $fillable = [
							'consignor_name', 
							'consignor_code',
							'consignor_location',
							's5_consignor_short_name_and_location',
							'consignee_name',
							'consignee_code',
							'consignee_location',
							'd5_consignor_short_name_and_location',
							'ref1',
							'vendor_code',
							'vendor_name',
							't_code',
							'truck_type',
							'lr_no',
							'lr_cn_date',
							'a_amount',
							'ref2',
							'ref3',
							'freight_type',
							'ap_status',
							'custom',
							'status',
							'created_by',
							'freight_invoice_no',
							'freight_invoice_date',
							'freight_amount',
							'freight_invoice_file',
							'pod_file',
							'validated_status',
							'submit',
							'f_return',
							'remark',
							'validated_by',
							'validated_at',
							
						  ];
}
