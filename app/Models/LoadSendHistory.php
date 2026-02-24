<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoadSendHistory extends Model
{
    protected $table = 'load_send_history';
	 public $timestamps = false; // to not be added update_at, created_at in create stmt query

    protected $fillable = [
        'load_summary_id',
        'vendor_code',
        'vendor_rank',
		'vehicle_number',
        'driver_name',
        'driver_mobile',
		'rejection_reason',
		'responded_at',
        'remarks',
        'sent_by',
        'sent_at',
		'status',
		'allocation_source', 'reference_no', 'source_type'
    ];
}
