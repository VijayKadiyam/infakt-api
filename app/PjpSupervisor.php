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

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    // public function pjp()
    // {
    //     return $this->belongsTo(Pjp::class,'actual_pjp_id','id');
    // }

    public function pjp()
    {
        return $this->belongsTo(Pjp::class, 'actual_pjp_id')
            ->with('pjp_markets');
    }
    public function pjp_visited_supervisors()
    {
        return $this->hasMany(PjpVisitedSupervisor::class);
    }
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

}
