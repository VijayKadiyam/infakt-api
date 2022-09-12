<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Search extends Model
{
    protected $fillable =[
        'user_id',
        'search_type',
        'search',
    ];
    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
