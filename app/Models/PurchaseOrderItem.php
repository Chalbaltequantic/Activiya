<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrderItem extends Model
{
    protected $fillable = [
        'purchase_order_id',
        'line_no',
        'item_code',
        'description',
        'hsn_code',
        'ean',
        'quantity',
        'uom',
        'mrp',
        'unit_cost',
        'cgst_percent',
        'sgst_percent',
        'igst_percent',
        'cess_percent',
        'total_amount',
        'raw_data',
    ];

    protected $casts = [
        'raw_data' => 'array',
        'quantity' => 'decimal:3',
        'mrp' => 'decimal:2',
        'unit_cost' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }
}