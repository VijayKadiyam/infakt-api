<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CareerRequest extends Model
{
    protected $fillable = [
        'name',
        'email',
        'description',
        'status',
        'remarks',
        'is_deleted',
        'phone_no',
        'interested_in',
        'attachment',
    ];
}
