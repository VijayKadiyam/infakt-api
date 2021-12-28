<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
        'company_id',
        'user_id',
        'date',
        'no_of_customer',
        'no_of_billed_customer',
        'more_than_two',
        'week_number'
    ];

    public function company() {
        return $this->belongsTo(Company::class);
    }
    public function user() {
        return $this->belongsTo(User::class);
    }
}
