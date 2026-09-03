<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DigiwimBinMaster extends Model
{
    use HasFactory;

    protected $table = 'digiwim_bin_master';

    protected $fillable = [
        'plant_code',
        'plant_name',
        'bin_no',
        'bin_type',
        'bin_status',
        'storage_location',
        'storage_section',
        'bin_location',
        'bin_length',
        'bin_width',
        'bin_height',
        'bin_volume_cft_cap',
        'bin_volume_cft_cap_2',
        'bin_weight_kg_cap',
        'bin_weight_kg_cap_2',
        'custom1',
        'custom2',
        'custom3',
        'custom4',
        'custom5',
        'created_by',
    ];
}