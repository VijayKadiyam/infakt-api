<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CompanyBoard extends Model
{
    protected $fillable = [
        'board_id',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
