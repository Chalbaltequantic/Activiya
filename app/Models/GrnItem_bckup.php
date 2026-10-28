<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GrnItem extends Model
{
    protected $guarded = [];

    protected $casts = ['extra' => 'array'];

    public function grn()
    {
        return $this->belongsTo(Grn::class);
    }
}
