<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
  protected $fillable = [
    'sku_id', 'qty', 'value', 'qty_delivered'
  ];

  public function order()
  {
    return $this->belongsTo(Order::class);
  }

  public function sku()
  {
    return $this->belongsTo(Sku::class);
  }
}
