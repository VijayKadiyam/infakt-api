<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserTimestamp extends Model
{
    protected $fillable =[
        'user_id',
        'timestamp',
        'event',
    ];
    
    public function company()
    {
      return $this->belongsTo(Company::class);
    }
}
