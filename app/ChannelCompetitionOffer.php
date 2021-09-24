<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ChannelCompetitionOffer extends Model
{
    protected $fillable = [
        'channel_filter_id',
        'competitor_name',
        'description',
        'top_articles',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
