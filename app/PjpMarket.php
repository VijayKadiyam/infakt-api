<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PjpMarket extends Model
{
    protected $fillable = [
        'pjp_id',
        'market_name',
        'gps_address',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function pjp()
    {
        return $this->belongsTo(Pjp::class)
        ->with('pjp_markets');
    }
}
