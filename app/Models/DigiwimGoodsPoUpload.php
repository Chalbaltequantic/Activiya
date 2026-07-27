<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DigiwimGoodsPoUpload extends Model
{
    use HasFactory;

    protected $table = 'digiwim_goods_po_uploads';

    protected $primaryKey = 'id';

    public $timestamps = true;

    protected $fillable = [

        'buyer_code',
        'buyer_name',
        'buyer_location',

        'bill_to_code',
        'bill_to_name',
        'bill_to_location',

        'ship_to_code',
        'ship_to_name',
        'ship_to_location',

        'supplier_code',
        'supplier_name',
        'supplier_location',

        'po_no',
        'po_date',

        'material_code',
        'material_description',

        'qty_units',
        'total_cs',

        'rate_per_unit',
        'tax',
        'conversion',
        'discount',

        'inco_terms',

        'freight',

        'custom',
        'custom_1',
        'custom_2',
        'custom_3',
        'custom_4',

        'added_by',
        'updated_by'
    ];

    protected $casts = [

        'po_date' => 'date',

        'qty_units' => 'decimal:3',
        'total_cs' => 'decimal:3',

        'rate_per_unit' => 'decimal:2',
        'tax' => 'decimal:2',
        'conversion' => 'decimal:3',
        'discount' => 'decimal:2',
        'freight' => 'decimal:2',
    ];

}