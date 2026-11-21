<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VendorPod extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'vendor_code', 'invoice_no', 'lr_no', 'lr_no_key',
        'pod_front_path', 'pod_back_path', 'other_document_path',
        'uploaded_by', 'updated_by', 'deleted_by',
    ];

    protected $casts = ['deleted_at' => 'datetime'];
}
