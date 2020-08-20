<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserAppointmentLetter extends Model
{
  protected $fillable = [
    'letter', 'signed','sign_path', 'start_date', 'end_date', 'stc_issue_date'
  ];

  public function user()
  {
    return $this->belongsTo(User::class);
  }
}
