<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorAddress extends Model
{
    use HasFactory;
	protected $fillable = [
								'vendor_id ',
								'address_line1',
								'address_line2',
								'city',
								'state',
								'state_code',
								'country',
								'zip_code',
								'from_date',
								'to_date',
								'is_current',
								'created_at',
								'updated_at',
								'status'
								
							];
	
	public function vendor()
	{
		return $this->belongsTo(Vendor::class);
	}
}
