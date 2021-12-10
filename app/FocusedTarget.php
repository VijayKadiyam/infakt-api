<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FocusedTarget extends Model
{
    protected $fillable = [
        'store_code',
        'month',
        'year',
        'target',
        'achieved',
        'category',
        'company_id',
        'user_id',
        
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
