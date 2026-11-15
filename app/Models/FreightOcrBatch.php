<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class FreightOcrBatch extends Model {protected $guarded=[];public function results(){return $this->hasMany(FreightOcrResult::class,'batch_id');}}
