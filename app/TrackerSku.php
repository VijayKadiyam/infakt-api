<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TrackerSku extends Model
{
    protected $fillable=[
        'sku_id'
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function tracker()
    {
        return $this->belongsTo(Tracker::class);
    }
}
