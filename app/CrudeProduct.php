<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CrudeProduct extends Model
{
  protected $fillable = [
    'product_name', 'sku_name', 'invoice_no', 'qty', 'unit', 'price'
  ];
}
