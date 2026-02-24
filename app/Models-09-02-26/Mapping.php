<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mapping extends Model
{
    use HasFactory;
	protected $table = 'vendor_subvendor_mapping';

	protected $fillable = [
							'operation_type', 
							'company_code',
							'consignor_code',
							'consignee_code',
							'vendor_code',
							'subvendor_code',
							'status',
							'created_by',
							'created_at'
							
						  ];
}
