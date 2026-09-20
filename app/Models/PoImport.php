<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PoImport extends Model
{
    protected $fillable = [
        'created_by',
        'total_files',
        'processed_files',
        'failed_files',
        'status',
    ];

    public function purchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class);
    }
}