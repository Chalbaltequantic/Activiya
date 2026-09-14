<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InsuranceProduct extends Model
{
    use HasFactory;

    protected $table = 'insurance_product';

    protected $fillable = [
        'insurance_id',
        'lr_no',
        'invoice_no',
        'product_code',
        'product_description',
        'batch_no',
        'damage_quantity',
        'shortage_quantity',
        'recovery_mrp',
        'damage_value',
        'shortage_value',
        'total_value',
        'created_by'
    ];

    protected $casts = [
        'damage_quantity' => 'decimal:3',
        'shortage_quantity' => 'decimal:3',
        'recovery_mrp' => 'decimal:2',
        'damage_value' => 'decimal:2',
        'shortage_value' => 'decimal:2',
        'total_value' => 'decimal:2'
    ];

    public function insurance()
    {
        return $this->belongsTo(Insurance::class, 'insurance_id');
    }
}