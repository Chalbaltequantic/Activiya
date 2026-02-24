<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoadStatusLog extends Model
{
	
  protected $fillable = [
        'load_summary_id',
        'reference_type',
        'reference_id',
        'old_status',
        'new_status',
        'changed_by_id',
        'changed_by_role',
		'updated_at'
    ];
	
	

	
}
