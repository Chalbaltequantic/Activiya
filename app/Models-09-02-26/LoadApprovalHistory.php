<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoadApprovalHistory extends Model
{
    protected $table = 'load_approval_history';
    public $timestamps = false;

    protected $fillable = [
        'load_summary_id',
        'vendor_code',
        'approver_id',
        'status',
        'remarks',
        'action_at'
    ];	
	
	public function loadsummary()
    {
        return $this->belongsTo(LoadSummary::class, 'load_summary_id');
    }
}