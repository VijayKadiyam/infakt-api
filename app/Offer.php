<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Offer extends Model
{
  protected $fillable = [
    'offer_type_id', 'offer'
  ];

  /*
   * An offer belongs to company
   *
   *@
   */
  public function company()
  {
    return $this->belongsTo(Company::class);
  }

  /*
   * An offer belongs to offer type
   *
   *@
   */
  public function offer_type()
  {
    return $this->belongsTo(OfferType::class);
  }
}
