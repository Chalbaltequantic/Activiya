<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GrnPoMatch extends Model
{
    protected $guarded = [];

    protected $casts = ['summary' => 'array'];

    public function grn()
    {
        return $this->belongsTo(Grn::class);
    }

    public function lines()
    {
        return $this->hasMany(GrnReconciliationItem::class, 'match_id');
    }
}
