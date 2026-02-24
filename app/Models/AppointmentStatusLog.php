<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppointmentStatusLog extends Model
{
    protected $table = 'appointment_status_logs';
	protected $fillable = [
        'appointment_id','consignee_id', 'status', 'reason', 'reschedule_date', 'reschedule_time'
    ];

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }

}
