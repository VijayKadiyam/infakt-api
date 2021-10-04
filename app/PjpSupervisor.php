<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PjpSupervisor extends Model
{
    protected $fillable = [
        'user_id',
        'date',
        'actual_pjp_id',
        'actual_pjp_market_id',
        'visited_pjp_id',
        'visited_pjp_market_id',
        'gps_address',
        'remarks',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
