<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lr extends Model
{
	protected $table="lrs";
    protected $fillable = [
        'vendor_id','site_plant_id','invoice_no','bill_date',
       'registered_address_id',
        'billing_address_id','branch_address_id', 'invoice_no',
		'lr_no', 'bill_date', 'vehicle_no', 'insurance', 'fssai_no', 'gstin','msme',
		'consignor', 'consignee', 'consignor_id', 'consignee_id','origin', 'destination', 'packages', 'description',
		'actual_weight', 'charged', 'rate', 'amount', 'invoice_value', 'surcharge',
		'hamali', 'risk_charge', 'b_charge', 'other_charge', 'total_amount', 'caution', 'notice'
      
    ];

    public function sitePlant()
    {
        return $this->belongsTo(Siteplant::class);
    }
	
	public function consignor()
	{
		return $this->belongsTo(Siteplant::class, 'consignor_id', 'id');
	}

	public function consignee()
	{
		return $this->belongsTo(Siteplant::class, 'consignee_id', 'id');
	}

    /* Invoice Number Auto Generator */
    public static function generateLrinvoiceNumber()
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

        return 'LR-'.$assessmentYear.'-'.str_pad($number,5,'0',STR_PAD_LEFT);
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