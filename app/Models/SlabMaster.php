<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SlabMaster extends Model
{
    protected $table = 'slab_master';

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
        'slab_1',
        'slab_2',
        'slab_3',
        'slab_4',
        'slab_5',
        'slab_6',
        'slab_7',
        'slab_8',
        'slab_9',
        'slab_10',
        'slab_1_rate',
        'slab_2_rate',
        'slab_3_rate',
        'slab_4_rate',
        'slab_5_rate',
        'slab_6_rate',
        'slab_7_rate',
        'slab_8_rate',
        'slab_9_rate',
        'slab_10_rate',
        'created_by',
    ];
}
