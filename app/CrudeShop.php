<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CrudeShop extends Model
{
  protected $fillable = [
    'shop_name', 'address', 'contact_person', 'email', 'shop_type', 'beat', 'week_number', 'outlet_wisdom_code'
  ];
}
