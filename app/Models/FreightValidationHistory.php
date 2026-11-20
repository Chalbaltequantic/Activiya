
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FreightValidationHistory extends Model
{
    use HasFactory;

    /**
     * Database table name.
     */
    protected $table = 'freight_validation_histories';

    /**
     * Primary key.
     */
    protected $primaryKey = 'id';

    /**
     * Enable timestamps.
     */
    public $timestamps = true;

    /**
     * Mass assignable attributes.
     *
     * These must match the actual migration columns
     * before history records can be saved.
     */
    protected $guarded = [];

    /**
     * Attribute casting.
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relationship with freight bill data.
     *
     * Assumes the history table uses billdata_id.
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
