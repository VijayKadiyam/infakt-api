<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CrudeUser extends Model
{
  protected $fillable = [
    'company_id',
    'empid',
    'phone',
    'region',
    'channel',
    'chain_name',
    'billing_code',
    'store_code',
    'store_name',
    'store_address',
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
  ];
}
