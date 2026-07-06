<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoadBoxCount extends Model
{
    protected $table = 'load_box_counts';

    protected $fillable = [
        'load_summary_id',
        'source_type',
        'reference_no',
        'placement_status',
        'image_path',
        'box_count',
        'manual_box_count',
        'remarks',
        'created_by',
    ];
}