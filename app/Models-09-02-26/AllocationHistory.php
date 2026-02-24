<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AllocationHistory extends Model
{
    protected $fillable = [
        'load_summary_id',
        'vendor_code',
        'vendor_name',
        'vendor_rank',
        'origin_code',
        'destination_code',
        'truck_type',
        'allocated_by',
		'cycle_total'
    ];
}