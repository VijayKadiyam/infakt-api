<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tracker extends Model
{
    protected $fillable = [
        'retailer_id',
        'customer_name',
        'contact_no',
        'email_id',
        'tracker_type',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function tracker_skus()
    {
        return $this->hasMany(TrackerSku::class);
    }
}
