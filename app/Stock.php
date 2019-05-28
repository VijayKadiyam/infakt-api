<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
  protected $fillable = [
    'qty', 'sku_type_id', 'offer_id', 'price', 'invoice_no'
  ];

  /*
   * A stock belongs to sku
   *
   *@
   */
  public function sku()
  {
    return $this->belongsTo(Sku::class);
  }

  /*
   *  A stock belongs to sku type
   *
   *@
   */
  public function sku_type()
  {
    return $this->belongsTo(SkuType::class);
  }

  /*
   * A stock belongs to offer
   *
   *@
   */
  public function offer()
  {
    return $this->belongsTo(Offer::class)
      ->with('offer_type');
  }

  /*
   * A stock has many sales
   *
   *@
   */
  public function sales()
  {
    return $this->hasMany(Sale::class);
  }
}
