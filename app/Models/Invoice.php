<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = [
        'vendor_id','site_plant_id','invoice_no','bill_date',
        'gst_type','registered_address_id',
        'billing_address_id','branch_address_id',
        'total_taxable','total_tax','grand_total',
        'digital_signature_path'
    ];

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function sitePlant()
    {
        return $this->belongsTo(Siteplant::class);
    }

    /* Invoice Number Auto Generator */
    public static function generateInvoiceNumber()
    {
        $year = date('Y');
        $last = self::whereYear('created_at',$year)->latest()->first();

        $number = $last
            ? intval(substr($last->invoice_no,-5)) + 1
            : 1;
			
		$currentMonth = date('m');

		$financialYearStartMonth = 4; // April

		if ($currentMonth >= $financialYearStartMonth) {
			
			$assessmentYear = $year . '-' . ($year + 1);
		} else {
			
			$assessmentYear = ($year - 1) . '-' . $year;
		}

        return 'INV-'.$assessmentYear.'-'.str_pad($number,5,'0',STR_PAD_LEFT);
    }
	
	public function annexures()
	{
		return $this->hasMany(InvoiceAnnexure::class);
	}
	
	public function registeredAddress()
	{
		return $this->belongsTo(VendorAddress::class, 'registered_address_id');
	}

	public function billingAddress()
	{
		return $this->belongsTo(VendorAddress::class, 'billing_address_id');
	}

	public function branchAddress()
	{
		return $this->belongsTo(VendorAddress::class, 'branch_address_id');
	}
	
	public function vendor()
	{
		return $this->belongsTo(Vendor::class);
	}
}