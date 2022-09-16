<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Board extends Model
{
    protected $fillable = [
        'name',
        'is_active',
    ];

    public function schools()
    {
        return $this->belongsToMany(Company::class, 'company_boards', 'board_id', 'company_id');
    }
}
