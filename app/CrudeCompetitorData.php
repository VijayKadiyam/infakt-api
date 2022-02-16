<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CrudeCompetitorData extends Model
{
    protected $fillable = [
        'company_id',
        'region',
        'channel',
        'chain_name',
        'city',
        'state',
        'store_code',
        'store_name',
        'ba_name',
        'pms_emp_id',
        'supervisor_name',
        'month',
        'year',
        'amount',
        'bio_tech',
        'derma_fique',
        'nivea',
        'neutrogena',
        'olay',
        'plum',
        'wow',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
