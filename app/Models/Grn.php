<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Grn extends Model
{
    protected $guarded = [];

    protected $casts = [
        'grn_date' => 'date',
        'raw_result' => 'array',
    ];

    public function items()
    {
        return $this->hasMany(GrnItem::class);
    }

    public function matches()
    {
        return $this->hasMany(GrnPoMatch::class);
    }
}
