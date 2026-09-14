<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Insurance extends Model
{
    use HasFactory;

    protected $table = 'insurance';

    protected $fillable = [
        'loss_date',
        'nature_of_claim',
        'from_location_code',
        'from_location',
        'to_location_code',
        'to_location',
        'invoice_no',
        'invoice_date',
        'transporter_name',
        'lr_no',
        'lr_date',
        'damage_value',
        'shortage_value',
        'total_value',
        'invoice_file',
        'pod_lr_copy',
        'cof_file',
        'fir_police_report_file',
        'fire_report_file',
        'fir_closure_report_file',
        'created_by'
    ];

    protected $casts = [
        'loss_date' => 'date',
        'invoice_date' => 'date',
        'lr_date' => 'date',
        'damage_value' => 'decimal:2',
        'shortage_value' => 'decimal:2',
        'total_value' => 'decimal:2'
    ];

    public function photographs()
    {
        return $this->hasMany(InsurancePhotograph::class, 'insurance_id');
    }
	
	public function products()
	{
		return $this->hasMany(InsuranceProduct::class, 'insurance_id');
	}
}