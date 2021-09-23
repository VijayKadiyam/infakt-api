<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ChannelFilterFifo extends Model
{
    protected $fillable=[
        'channel_filter_id',
        'retailer_id',
        'date',
        'is_sample_article',
        'is_sellable_article',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }


    public function channel_filter_fifo_expiries()
    {
        return $this->hasMany(ChannelFilterFifoExpiry::class);
    }
}
