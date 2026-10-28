<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class TransportRateMaster extends Model {
    protected $table = 'transport_rate_master';
    protected $guarded = ['id'];
    protected $casts = ['validity_start_date'=>'date:Y-m-d', 'validity_end_date'=>'date:Y-m-d'];
}
