<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AllowanceType extends Model
{
  protected $fillable = [
    'name', 'amount'
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
