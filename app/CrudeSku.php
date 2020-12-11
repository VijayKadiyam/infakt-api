<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CrudeSku extends Model
{
  protected $fillable = [
    'sku_name', 'invoice_no', 'date', 'qty', 'unit', 'price_per_unit', 'total_price', 'sku_type', 'offer', 'offer_type', 'distributor_name'
  ];
}
