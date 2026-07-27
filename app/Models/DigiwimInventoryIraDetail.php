<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class DigiwimInventoryIraDetail extends Model
{
    use SoftDeletes;

    protected $table = 'digiwim_inventory_ira_details';

    protected $fillable = [
        'digiwim_inventory_ira_id','qty_unit','qty_case','bin_no','remarks','activity_by','activity_at',
    ];

    protected $casts = [
        'qty_unit' => 'decimal:3',
        'qty_case' => 'decimal:3',
        'activity_at' => 'datetime',
    ];

    public function ira(): BelongsTo
    {
        return $this->belongsTo(DigiwimInventoryIra::class, 'digiwim_inventory_ira_id');
    }

    public function activityByAdmin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'activity_by');
    }
}
