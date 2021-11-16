<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
        'user_id',
        'date',
        'no_of_customer',
        'no_of_billed_customer',
        'more_than_two',
    ];

    public function company() {
        return $this->belongsTo(Company::class);
    }
    public function user() {
        return $this->belongsTo(User::class);
    }
}
