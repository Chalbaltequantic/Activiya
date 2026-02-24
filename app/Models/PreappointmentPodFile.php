<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PreappointmentPodFile extends Model
{
    use HasFactory;
	protected $table = 'preappointment_pod_files';

	protected $fillable = ['appointment_id', 'file_type', 'file_path', 'created_at', 'created_by'];

    public function appointment()
    {
        return $this->belongsTo(PreAppointment::class);
    } 
}
