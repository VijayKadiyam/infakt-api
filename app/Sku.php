<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Sku extends Model
{
  protected $fillable = [
    'name', 'sku_type_id', 'company_id', 'offer_id'
  ];

  /*
   * A company state belongs to product
   *
   *@
   */
  public function product()
  {
    return $this->belongsTo(Product::class);
  }

  public function company()
  {
    return $this->belongsTo(Company::class);
  }

  /*
   * A sku has many stocks
   *
   *@
   */
  public function stocks()
  {
    return $this->hasMany(Stock::class)
      ->with('sku', 'sku_type', 'offer', 'unit');
  }

  /*
   * A sku has many sales
   *
   *@
   */
  public function sales()
  {
    return $this->hasMany(Sale::class);
  }

  public function offer()
  {
    return $this->belongsTo(Offer::class)
      ->with('offer_type');
  }
  public function damageStock()
  {
    return $this->hasMany(DamageStock::class)
      ->with('sku_id');
  }
}
