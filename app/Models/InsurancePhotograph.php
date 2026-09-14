<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InsurancePhotograph extends Model
{
    use HasFactory;

    protected $table = 'insurance_photographs';

    protected $fillable = [
        'insurance_id',
        'photo_path',
        'original_name',
        'uploaded_by'
    ];

    public function insurance()
    {
        return $this->belongsTo(Insurance::class, 'insurance_id');
    }
}