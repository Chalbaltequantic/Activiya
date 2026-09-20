<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    protected $fillable = [
        'po_import_id',
        'po_template_id',
        'created_by',
        'po_no',
        'po_date',
        'delivery_date',
        'expiry_date',
        'vendor_code',
        'vendor_name',
        'buyer_name',
        'bill_to',
        'ship_to',
        'buyer_gstin',
        'vendor_gstin',
        'basic_amount',
        'tax_amount',
        'total_amount',
        'currency',
        'original_filename',
        'file_path',
        'raw_text',
        'extracted_json',
        'processing_status',
        'processing_remark',
    ];

    protected $casts = [
        'po_date' => 'date',
        'delivery_date' => 'date',
        'expiry_date' => 'date',
        'extracted_json' => 'array',
        'basic_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    public function template()
    {
        return $this->belongsTo(PoTemplate::class, 'po_template_id');
    }

    public function import()
    {
        return $this->belongsTo(PoImport::class, 'po_import_id');
    }

    public function items()
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }
}