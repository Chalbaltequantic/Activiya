<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class DigiwimInventoryIra extends Model
{
    use SoftDeletes;

    protected $table = 'digiwim_inventory_ira';

    protected $fillable = [
        'digiwim_preloading_id','inventory_key','material_code','material_description','division','brand',
        'sub_brand','uom','piece_per_box','mrp','weight','volume','storage_plant_code','storage_plant_name',
        'storage_plant_location','batch_no','mfg_date','expiry_date','inventory_qty','status','started_by',
        'started_at','ended_by','ended_at',
    ];

    protected $casts = [
        'piece_per_box' => 'decimal:3',
        'mrp' => 'decimal:2',
        'weight' => 'decimal:3',
        'volume' => 'decimal:3',
        'inventory_qty' => 'decimal:3',
        'mfg_date' => 'date',
        'expiry_date' => 'date',
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
    ];

    public function activities(): HasMany
    {
        return $this->hasMany(DigiwimInventoryIraDetail::class, 'digiwim_inventory_ira_id')
            ->orderBy('activity_at');
    }

    public function startedByAdmin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'started_by');
    }

    public function endedByAdmin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'ended_by');
    }
}
