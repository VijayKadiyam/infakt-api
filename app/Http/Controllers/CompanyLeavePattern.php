<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CompanyLeavePattern extends Controller
{
  protected $fillable = [
    'company_id', 'leave_pattern_id'
  ];

  /*
   * A company leave pattern belings to many companies
   *
   *@
   */
  public function companies()
  {
    return $this->belongsToMany(Company::class);
  }
}
