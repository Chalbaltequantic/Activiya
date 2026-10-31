<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoadUnloadMaster extends Model
{
    protected $table = 'load_unload_master';

    protected $fillable = [
        'from_code',
        'from_name',
        'origin_region',
        'to_name',
        'to_code',
        'destination_region',
        'vendor_code',
        'vendor_name',
        'mode',
        'truck_code',
        'truck_description',
        'charge_type',
        'rate_type',
        'rate',
        'load_unload_limit',
        'created_by',
    ];

    protected $casts = [
        'rate' => 'decimal:4',
        'load_unload_limit' => 'decimal:4',
    ];
}
