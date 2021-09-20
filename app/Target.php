<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Target extends Model
{
  protected $fillable = [
    'month', 'year', 'target', 'company_id', 'user_id'
  ];

  public function user()
  {
    return $this->belongsTo(User::class);
  }
}
