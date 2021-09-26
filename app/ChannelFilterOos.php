<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ChannelFilterOos extends Model
{
    protected $fillable=[
        'channel_filter_id',
        'retailer_id',
        'date',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function channel_filter_oos_skus()
    {
        return $this->hasMany(ChannelFilterOosSku::class);
    }
}
