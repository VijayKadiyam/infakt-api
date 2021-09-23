<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ChannelFilterFifoExpiry extends Model
{
    protected $fillable=[
        'sku_id',
        'status'
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
    
    public function channel_filter_fifo(){
        return $this->belongsTo(ChannelFilterFifo::class);
    }
}
