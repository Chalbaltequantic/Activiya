<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TruckMaster extends Model
{
    protected $table = 'truck_master';

    protected $fillable = [
        'code',
        'description',
        'short_name',
        'length',
        'width',
        'height',
        'weight_capacity',
        'max_volume_capacity',
        'min_capacity',
        't_body',
        'utilities',
        'status',
		'created_at',
        'created_by',
        'updated_by'
    ];
}