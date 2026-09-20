<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PoTemplate extends Model
{
    protected $fillable = [
        'code',
        'name',
        'parser_code',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function purchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class);
    }
}