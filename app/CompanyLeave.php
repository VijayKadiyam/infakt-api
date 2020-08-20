<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CompanyLeave extends Model
{
  protected $fillable = [
    'leave_pattern_id', 'name', 'leaves'
  ];

  /*
   * A company leave belongs to company
   *
   *@
   */
  public function company()
  {
    return $this->belongsTo(Company::class);
  }

  /*
   * A company leave beleongs to leave pattern
   *
   *@
   */
  public function leave_pattern()
  {
    return $this->belongsTo(LeavePattern::class);
  }
}
