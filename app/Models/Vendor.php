<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    use HasFactory;
	
	protected $fillable = [
								'parent_id',
								'vendor_code',
								'vendor_type',
								'company_code',
								'vendor_name',
								'vendor_short_name',
								'authorized_person_name',
								'authorized_person_phone',
								'authorized_person_mail',
								'withholding_tax_type',
								'tds_section_1',
								'receipt_type_1',
								'receipt_name',
								'withholding_tax_type_2',
								'tds_section_2',
								'pan_no',
								'email',
								'gstin_number',
								'pan_gstin_check',
								'terms_of_payment_key',
								'account_group',
								'posting_block_overall',
								'purchase_block_overall',
								'service_block',
								'purchase_shipment_block',
								'payment_block',
								'status',
								'created_by',
								'updated_by',
								'created_at',
								'updated_at',
								'logo', 
								'caution', 
								'notice', 
								'msme_no', 
								'fssai_no' ,
								'cin' 
								
							];

    public function addresses()
    {
        return $this->hasMany(VendorAddress::class);
    }

    public function currentAddress()
    {
        return $this->hasOne(VendorAddress::class)->where('is_current', true);
    }

    public function bankAccounts() {
    return $this->hasMany(VendorBankAccount::class);
	}
	
	 public function spotbys()
    {
        return $this->belongsToMany(Spotby::class, 'spotby_vendors')
            ->withTimestamps();
    }

    public function quotes()
    {
        return $this->hasMany(SpotbyVendorQuote::class, 'vendor_id');
    }
	
	public function spotbies()
	{
		return $this->belongsToMany(Spotby::class, 'spotby_vendors', 'vendor_id', 'spotby_id');
	}
	
	
	
}