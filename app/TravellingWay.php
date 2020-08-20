<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TravellingWay extends Model
{
  protected $fillable = [
    'name'
  ];

  /*
   * A travelling way belongs to company
   *
   *@
   */
  public function company()
  {
    return $this->belongsTo(Company::class);
  }
}
