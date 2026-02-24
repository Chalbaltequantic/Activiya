<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PodFile extends Model
{
    use HasFactory;
	protected $table = 'pod_files';

	protected $fillable = ['appointment_id', 'file_type', 'file_path', 'created_at', 'created_by'];

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    } 
}
