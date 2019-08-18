<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserAppointmentLetter extends Model
{
  protected $fillable = [
    'letter', 'signed','sign_path'
  ];

  public function user()
  {
    return $this->belongsTo(User::class);
  }
}
