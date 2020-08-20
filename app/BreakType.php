<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BreakType extends Model
{
  protected $fillable = [
    'name'
  ];

  /*
   * A break type belongs to company
   *
   *@
   */
  public function company()
  {
    return $this->belongsTo(Company::class);
  }
}
