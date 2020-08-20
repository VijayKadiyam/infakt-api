<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserApplication extends Model
{
  protected $fillable = [
    'company_leave_id', 'from_date', 'to_date', 'description', 'leave_type_id'
  ];

  /*
   * A application belongs to user
   *
   *@
   */
  public function user()
  {
    return $this->belongsTo(User::class)
      ->with('supervisors');
  }

  /*
   * An user application belongs to company leave
   *
   *@
   */
  public function company_leave()
  {
    return $this->belongsTo(CompanyLeave::class);
  }

  /*
   * An user application belongs to leave type
   *
   *@
   */
  public function leave_type()
  {
    return $this->belongsTo(LeaveType::class);
  }

  /*
   * An user application has many application approvals
   *
   *@
   */
  public function application_approvals()
  {
    return $this->hasMany(ApplicationApproval::class);
  }
}
