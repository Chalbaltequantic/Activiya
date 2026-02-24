<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoadPlacementStatusLog extends Model
{
	protected $table = 'load_placement_status_logs';
    public $timestamps = false;

    protected $fillable = [
        'load_summary_id', 'reference_no', 'source_type',
        'vendor_code',
        'placement_status',
        'lr_no',
        'remarks',
        'created_by',
        'created_by_role'
    ];

    public function loadplacement()
    {
        return $this->belongsTo(LoadSummary::class, 'load_summary_id');
    }
	
	public function admin()
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }
	
	
}
