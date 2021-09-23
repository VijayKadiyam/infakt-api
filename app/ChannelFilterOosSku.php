<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ChannelFilterOosSku extends Model
{
    protected $fillable=[
        'sku_id',
        'status'
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
    
    public function channel_filter_oos(){
        return $this->belongsTo(ChannelFilterOos::class);
    }
}
