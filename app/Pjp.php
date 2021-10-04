<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Pjp extends Model
{
    protected $fillable=[
        'location',
        'region'
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
