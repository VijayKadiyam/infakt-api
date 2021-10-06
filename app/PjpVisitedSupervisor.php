<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PjpVisitedSupervisor extends Model
{
    protected $fillable = [
        'pjp_supervisor_id',
        'visited_pjp_id',
        'visited_pjp_market_id',
        'remarks',
        'gps_address',
    ];

    public function company() {
        return $this->belongsTo(Company::class);
    }

    public function pjp_supervisor() {
        return $this->belongsTo(PjpSupervisor::class);
    }
}
