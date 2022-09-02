<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Grade extends Model
{
    protected $fillable =[
        'name',
        'is_active',
        'is_deleted',
    ];
}
