<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
  protected $fillable = [
    'user_id',
    'description',
  ];

  public function company()
  {
    return $this->belongsTo(Company::class);
  }
}
