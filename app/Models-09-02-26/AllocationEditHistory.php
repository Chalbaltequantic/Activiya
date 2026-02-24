<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AllocationEditHistory extends Model
{
    protected $table = 'allocation_edit_history';
    public $timestamps = false;

    protected $fillable = [
        'load_summary_id',
        'old_vendor_code',
        'old_vendor_name',
        'old_vendor_code_source',
        'new_vendor_code',
        'new_vendor_name',
        'remarks',
        'edited_by',
        'edited_at'
    ];
}