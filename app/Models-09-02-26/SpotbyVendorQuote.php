<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpotbyVendorQuote extends Model
{
     protected $table = 'spotby_vendor_quotes';
    protected $fillable = [
        'spotby_id', 'vendor_id', 'round', 'price',
        'transit_time', 'client_revised_price', 'client_revised_transit_time'
    ];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }

    public function spotby()
    {
        return $this->belongsTo(Spotby::class, 'spotby_id');
    }
	
	
	
}

