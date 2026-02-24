<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpotbyVendor extends Model
{
    use HasFactory;
    protected $table = 'spotby_vendors';
    protected $fillable = ['spotby_id', 'vendor_id'];

   /* public function spotby()
    {
        return $this->belongsTo(Spotby::class, 'spotby_id');
    }*/
	
	public function spotby()
    {
        return $this->belongsTo(Spotby::class);
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }
	
}

