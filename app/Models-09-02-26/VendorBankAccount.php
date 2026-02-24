<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorBankAccount extends Model
{
    use HasFactory;
	protected $table = 'vendor_bank_details';
	protected $fillable = [
							'vendor_id ',
							'bank_name',
							'branch_name',
							'account_holder_name',
							'account_number',
							'ifsc_code',
							'account_type',
							'created_at',
							'updated_at',
							'status'
								
							];
	
	public function vendor() 
	{
		return $this->belongsTo(Vendor::class);
	}
}
