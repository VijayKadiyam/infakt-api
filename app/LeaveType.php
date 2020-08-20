<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LeaveType extends Model
{
  protected $fillable = [
    'name'
  ];

  /*
   * A allowance type belongs to company
   *
   *@
   */
  public function company()
  {
    return $this->belongsTo(Company::class);
  }
}
