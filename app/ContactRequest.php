<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ContactRequest extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone_no',
        'interested_in',
        'description',
        'status',
        'remarks',
        'is_deleted',
        'school_name',
        'role',
        'city',
        'state',
        'pincode',
    ];
}
