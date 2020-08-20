<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LeavePattern extends Model
{
  protected $fillable = [
    'name'
  ];

  /*
   * A leave pattern has many company leaves
   *
   *@
   */
  public function company_leaves()
  {
    return $this->hasMany(CompanyLeave::class);
  }
}
