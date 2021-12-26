<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CompetitorData extends Model
{
    protected $fillable = [
        'user_id',
        'competitor',
        'amount',
        'week',
        'month',
        'year',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
