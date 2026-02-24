<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoadSummaryItem extends Model
{
    protected $fillable = [
        'load_summary_id',
        'load_optimizer_id',
		'reference_no',
		'sku_code',
		'sku_description',
        'weight',
        'volume','qty','updated_by'
    ];
	public function sku()
    {
        return $this->belongsTo(Loadoptimizer::class, 'load_optimizer_id');
    }
}