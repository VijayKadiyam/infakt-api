<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
  protected $fillable = [
    'notification', 'status'
  ];

  public function user()
  {
    return $this->belongsTo(User::class);
  }
}
