<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PjpVisitedSupervisorExpense extends Model
{
    protected $fillable = [
        'company_id',
        'pjp_visited_supervisor_id',
        'expense_type',
        'travelling_way',
        'transport_mode',
        'km_travelled',
        'amount',
        'description',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function pjp_visited_supervisor()
    {
        return $this->belongsTo(PjpVisitedSupervisor::class);
    }
}
