<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DigiWim extends Model
{
    use HasFactory;

    protected $table = 'digi_wim';

    protected $fillable = [
        'indent_id',

        'supplier_code',
        'supplier_name',
        'supplier_location',

        'po_no',
        'invoice_challan_no',
        'invoice_challan_date',

        'consignee_code',
        'consignee_name',
        'consignee_location',

        'm_code',
        'material_descriptions',

        'batch_no',
        'mfg_date',
        'expiry_date',

        'qty_units',
        'total_cs',

        'transporter_code',
        'transporter_name',

        'truck_no',
        'lr_no',
        'lr_date',

        'ewaybill_no',

        'truck_code',
        'vehicle_type',

        'custom',
        'custom_1',
        'custom_2',
        'custom_3',
        'custom_4',
    ];

    protected $casts = [
        'invoice_challan_date' => 'date',
        'mfg_date' => 'date',
        'expiry_date' => 'date',
        'lr_date' => 'date',

        'qty_units' => 'decimal:2',
        'total_cs' => 'decimal:2',
    ];
}