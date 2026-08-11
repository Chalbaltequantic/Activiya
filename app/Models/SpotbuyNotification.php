<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SpotbuyNotification extends Model
{
    protected $table = 'spotbuy_notifications';

    protected $fillable = [

        'buyer_id',

        'supplier_id',

        'spotby_id',

        'round_no',

        'notification_type',

        'title',

        'message',

        'action_url',

        'is_read',

        'read_at',
    ];


    protected $casts = [

        'is_read' => 'boolean',

        'read_at' => 'datetime',

        'created_at' => 'datetime',

        'updated_at' => 'datetime',
    ];


    /* Buyer */

    public function buyer()
    {
        return $this->belongsTo(
            Admin::class,
            'buyer_id'
        );
    }


    /* Supplier / Vendor */

    public function supplier()
    {
        return $this->belongsTo(
            Vendor::class,
            'supplier_id'
        );
    }


    /* Spot Buy */

    public function spotby()
    {
        return $this->belongsTo(
            Spotby::class,
            'spotby_id'
        );
    }
}