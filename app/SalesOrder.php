<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SalesOrder extends Model
{
  protected $fillable  = [
    'order_id', 'sku_id', 'user_id', 'retailer_id', 'distributor_id', 'invoice_no', 'invoice_date', 'price_unit', 'quantity_placed', 'placed_bill_value', 'quantity_delivered', 'delivered_bill_value', 'scheme', 'quantity_returned'
  ];

  public function order()
  {
    return $this->belongsTo(Order::class);
  }
}
