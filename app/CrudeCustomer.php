<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CrudeCustomer extends Model
{
    protected $fillable = [
        'company_id', 
        'store_code',
        'date',
        'no_of_customer',
        'no_of_billed_customer',
        'more_than_two',
    ];
}
