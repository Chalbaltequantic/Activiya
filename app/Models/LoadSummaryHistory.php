<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoadSummaryHistory extends Model
{
        protected $fillable = [
        'load_summary_id',
        'reference_no',
        'old_weight',
        'old_volume',
        'old_weight_util',
        'old_volume_util',
        'new_weight',
        'new_volume',
        'new_weight_util',
        'new_volume_util',
        'edited_by'
    ];

    public $timestamps = false;
}
