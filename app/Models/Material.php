<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    use HasFactory;
	protected $table = 'materials';

	protected $fillable = [
							'material_code',
							'material_description',
							'uom',
							'division',
							'piece_per_box',
							'length_cm',
							'width_cm',
							'height_cm',
							'net_weight_kg',
							'gross_weight_kg',
							'volume_cft',
							'category',
							'pallets',
							'brand',
							'sub_brand',
							'thickness',
							'load_sequence',
							'associated',
							'parent',
							'child',
							'updated_at',
							'updated_by',
							'status'
							
						  ];						  
	
}
