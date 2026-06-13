<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DigiwimEgp extends Model
{
    protected $table = 'digiwim_egp';

    protected $fillable = [

        'purpose_of_entry',

        'outward_date',
        'outward_time',

        'customer_name',
        'customer_location',

        'invoice_challan_no',
        'invoice_challan_date',

        'vendor_name',

        'truck_no',
        'lr_cn_no',

        'driver_mobile_no',

        'custom',

        'added_by',

        'updated_by',
    ];
}