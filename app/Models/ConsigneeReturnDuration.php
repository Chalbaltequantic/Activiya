<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConsigneeReturnDuration extends Model
{
    protected $table = 'consignee_return_durations';
	protected $fillable = ['consignee_code', 'consignee_name','return_time_minutes','end_date', 'status'];

    public function siteplant()
	{
		return $this->belongsTo(Siteplant::class, 'consignee_code', 'consignee_code');
	}
}