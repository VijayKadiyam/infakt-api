<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
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

  /*
   * A product has many skus
   *
   *@
   */
  public function skus()
  {
    return $this->hasMany(Sku::class)
      ->with('stocks');
  }

}
