<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;

class Admin extends Authenticatable
{
    use HasFactory;

    protected $guard = "admin";

    protected $fillable = [
        'name',
        'email',
        'mobile',
        'role_id',
        'status',
        'password',
        'vendor_code',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];
    
    public function role(){
        return $this->belongsTo(Role::class);
    }
	public function vendor(){
        return $this->belongsTo(Vendor::class);
    }

     public function hasPermission($permission): bool
     {
        return $this->role->permissions()->where('slug',$permission)->first() ? true : false;

    }
}	