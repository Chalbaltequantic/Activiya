<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GrnReconciliationItem extends Model
{
    protected $guarded = [];

    protected $casts = ['details' => 'array'];

    public function match()
    {
        return $this->belongsTo(GrnPoMatch::class, 'match_id');
    }
}
