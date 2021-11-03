<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CrudeTarget extends Model
{
    protected $fillable =[
        'company_id', 
        'store_code', 
        'month',
        'year',
        'target',
        'achieved',
    ];
}
