<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CompanyStateBranch extends Model
{
  protected $fillable = [
    'name'
  ];

  /*
   * A company state branch belongs to company state
   *
   *@
   */
  public function company_state()
  {
    return $this->belongsTo(CompanyState::class);
  }
}
