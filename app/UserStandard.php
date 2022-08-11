<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserStandard extends Model
{
    protected $fillable=[
        'company_id',
        'user_id',
        'standard_id',
        'start_date',
        'end_date' ,
        'is_active',
        'is_deleted'
    ];

    public function company()
    {
      return $this->belongsTo(Company::class);
    }
}
















