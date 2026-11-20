<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FreightValidationHistory extends Model
{
    use HasFactory;

    /**
     * Database table
     */
    protected $table = 'freight_validation_histories';

    /**
     * Primary key
     */
    protected $primaryKey = 'id';

    /**
     * Enable timestamps
     */
    public $timestamps = true;

    /**
     * Allow history attributes to be saved
     */
    protected $guarded = [];

    /**
     * Automatically convert PHP arrays to JSON
     * when saving, and JSON to arrays when reading.
     */
    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'old_data' => 'array',
        'new_data' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relationship with freight bill
     */
    public function billdata()
    {
        return $this->belongsTo(
            Billdata::class,
            'billdata_id',
            'id'
        );
    }
}
