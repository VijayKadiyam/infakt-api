<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserAttendance extends Model
{
  protected $fillable = [
    'date', 'login_time', 'logout_time', 'login_lat', 'login_lng', 'logout_lat', 'logout_lng'
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
