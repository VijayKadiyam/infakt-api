<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CrudePjp extends Model
{
    protected $fillable = [
        'visit_date',
        'day',
        'region',
        'location',
        'market_working_details',
        'joint_working_with',
        'employee_code',
        'supervisor_name',
        'remarks',
        'store_name',
        'store_code',
    ];
}
