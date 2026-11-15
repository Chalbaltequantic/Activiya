<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class FreightOcrResult extends Model {protected $guarded=[];protected $casts=['reasons'=>'array','extracted'=>'array'];}
