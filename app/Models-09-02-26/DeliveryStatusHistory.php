<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryStatusHistory extends Model
{
    use HasFactory;
	protected $table = 'delivery_status_histories';

	protected $fillable = ['appointment_id', 'delivery_status','inv_no', 'created_by','created_at', 'remarks'];

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }	  
}
