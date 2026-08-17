<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrackingStatusHistory extends Model
{
    use HasFactory;

    protected $table = 'tracking_status_histories';

    protected $fillable = [

        'tracking_id',
        'indent_no',
        'shipment_status',
        'transit_status',
        'distance_covered',
        'distance_to_cover',
        'current_location',
        'tracking_link',
        'driver_number',
        'updated_by',
        'updated_by_type',
        'status_updated_at',
    ];

    public function tracking()
    {
        return $this->belongsTo(
            Tracking::class,
            'tracking_id'
        );
    }
}