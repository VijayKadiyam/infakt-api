<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OfferType extends Model
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
}
