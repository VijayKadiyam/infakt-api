<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CompanyStateHoliday extends Model
{
  protected $fillable = [
    'holiday_id'
  ];

  /*
   * A company state holiday belongs to company state
   *
   *@
   */
  public function company_state()
  {
    return $this->belongsTo(CompanyState::class);
  }
}
