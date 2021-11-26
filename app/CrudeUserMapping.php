<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CrudeUserMapping extends Model
{
    protected $fillable = [
        'company_id',
        'region',
        'channel',
        'chain_name',
        'billing_code',
        'store_code',
        'store_name',
        'store_address',
        'emp_id',
        'ba_name',
        'location',
        'city',
        'state',
        'rsm',
        'asm',
        'supervisor_name',
        'store_type',
        'brand',
        'ba_status',
        'store_status',
        'user_login_id',
        'user_password',
        'remark',
        'doj',
    ];
}
