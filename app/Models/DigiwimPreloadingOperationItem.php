<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DigiwimPreloadingOperationItem extends Model
{
    protected $table = 'digiwim_preloading_operation_items';

    protected $fillable = [
        'operation_id',
        'digi_wim_preloading_id',
        'invoice_challan_no',
        'material_code',
        'material_description',
        'batch_no',
        'mfg_date',
        'expiry_date',
        'qty',
        'bin_no',
        'goods_status',
        'remarks',
        'created_by',
        'status',
    ];
}