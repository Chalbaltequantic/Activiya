<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DigiwimOperation extends Model
{
    protected $table = 'digiwim_operations';

    protected $fillable = [
        'operation_type',
        'invoice_challan_no',
        'invoice_date',
        'po_order_no',
        'po_order_date',
        'supplier_code_name',
        'transporter_name',
        'truck_number',
        'truck_type',
        'lr_no',
        'uom',
        'created_by',
        'status',
    ];

    public function items()
    {
        return $this->hasMany(DigiwimOperationItem::class, 'operation_id');
    }
	
	public function creator()
	{
		return $this->belongsTo(Admin::class, 'created_by');
	}
}