<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
  protected $fillable = [
    'user_id',
    'description',
    "is_read",
    "is_deleted",
  ];

  public function company()
  {
    return $this->belongsTo(Company::class);
  }
}
