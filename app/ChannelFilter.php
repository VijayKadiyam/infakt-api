<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ChannelFilter extends Model
{
    protected $fillable=[
        'name'
    ];


    public function company()
  {
    return $this->belongsTo(Company::class);
  }
}

