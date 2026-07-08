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
        'image_width',
        'image_height',
        'image_size_kb',
        'box_count',
        'ai_box_count',
        'manual_box_count',
        'confidence_score',
        'processing_time_ms',
        'image_status',
        'review_status',
        'remarks',
        'created_by',
        'updated_by',
    ];
}