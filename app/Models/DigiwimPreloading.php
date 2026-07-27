<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DigiwimPreloading extends Model
{
    protected $table = 'digiwim_preloading';

    protected $fillable = [
        'indent_id',
        'invoice_challan_no',
        'consignor_code',
        'consignor_name',
        'consignee_code',
        'consignee_location',
        'material_code',
        'material_description',

        'batch_no',

        'mfg_date',
        'expiry_date',

        'uom',
        'qty',

        'bin_no',
        'goods_status',

        'transporter_code',
        'transporter_name',

        'truck_code',
        'truck_description',

        'truck_number',

        'lr_no',

        'remarks',

        'created_by',
        'updated_by',
        'status'
    ];
	
	
	
	/* IRA Header*/

	public function ira()
	{
		return $this->hasOne(
			\App\Models\DigiwimInventoryIra::class,
			'digiwim_preloading_id',
			'id'
		);
	}


	/* Pending IRA*/

	public function pendingIra()
	{
		return $this->hasOne(
			\App\Models\DigiwimInventoryIra::class,
			'digiwim_preloading_id',
			'id'
		)->where(
			'status',
			'pending'
		);
	}


	/* Completed IRA*/

	public function completedIra()
	{
		return $this->hasOne(
			\App\Models\DigiwimInventoryIra::class,
			'digiwim_preloading_id',
			'id'
		)->where(
			'status',
			'completed'
		);
	}
}