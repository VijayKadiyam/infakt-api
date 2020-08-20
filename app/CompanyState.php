<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CompanyState extends Model
{
  protected $fillable = [
    'name'
  ];

  /*
   * A company state belongs to company
   *
   *@
   */
  public function company()
  {
    return $this->belongsTo(Company::class);
  }

  /*
   * A company state has many company state branches
   *
   *@
   */
  public function company_state_branches()
  {
    return $this->hasMany(CompanyStateBranch::class);
  }

  /*
   * A company state has many company state holidays
   *
   *@
   */
  public function company_state_holidays()
  {
    return $this->hasMany(CompanyStateHoliday::class);
  }
}
