<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserAttendance extends Model
{
  protected $fillable = [
    'date', 'login_time', 'logout_time', 'login_lat', 'login_lng', 'logout_lat', 'logout_lng', 'battery', 'session_type', 'remarks', 'login_address', 'logout_address', 'company_id', 'selfie_path','logout_selfie_path'
  ];

  /*
   * An user attendance belongs to user
   *
   *@
   */
  public function user()
  {
    return $this->belongsTo(User::class);
  }

  /*
   * A user attendance has many user attendance breaks
   *
   *@
   */
  public function user_attendance_breaks()
  {
    return $this->hasMany(UserAttendanceBreak::class)
      ->with('break_type');
  }
}
