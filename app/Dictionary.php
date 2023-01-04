<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Dictionary extends Model
{
    protected $fillable = [
        'company_id',
        'keyword',
        'response'
    ];

    protected $casts = [
        'response'  =>  'array'
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
