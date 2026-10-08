<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FreightBillApprovalHistory extends Model
{
    use HasFactory;

    protected $table = 'freight_bill_approval_histories';

    protected $fillable = [
        'bill_data_id',
        'status',
        'remark',
        'action_by',
    ];

    public function bill()
    {
        return $this->belongsTo(Billdata::class, 'bill_data_id');
    }

    public function actionBy()
    {
        return $this->belongsTo(Admin::class, 'action_by');
    }
}