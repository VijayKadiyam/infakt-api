<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserAttendanceBreak extends Model
{
  protected $fillable = [
    'break_type_id', 'start_time', 'end_time'
  ];

  /*
   * A user attendance break belongs to user attendance
   *
   *@
   */
  public function user_attendance()
  {
    return $this->belongsTo(UserAttendance::class);
  }

  /*
   * A user attendance break belongs to break type
   *
   *@
   */
  public function break_type()
  {
    return $this->belongsTo(BreakType::class);
  }
}
