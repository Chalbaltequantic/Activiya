<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DigiwimEgr extends Model
{
    protected $table = 'digiwim_egr';

    protected $fillable = [
        'inward_date',
        'inward_time',
        'purpose_of_entry',
        'supplier_name',
        'supplier_location',
        'invoice_challan_no',
        'invoice_challan_date',
        'vendor_name',
        'truck_no',
        'lr_cn_no',
        'driver_mobile_no',
        'custom',
    ];
}