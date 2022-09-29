<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserTimestamp extends Model
{
  protected $fillable = [
    'company_id',
    'user_id',
    'timestamp',
    'event',
    'total_time_spent',
  ];

  public function company()
  {
    return $this->belongsTo(Company::class);
  }
  public function user()
  {
    return $this->belongsTo(User::class);
  }
}
