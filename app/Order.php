<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
  protected $fillable = [
    'distributor_id', 'user_id', 'retailer_id', 'status', 'total', 'is_order_taken', 'reason_for_no_order'
  ];

  public function company()
  {
    return $this->belongsTo(Company::class);
  }

  public function distributor()
  {
    return $this->belongsTo(User::class);
  }

  public function user()
  {
    return $this->belongsTo(User::class); 
  }

  public function retailer()
  {
    return $this->belongsTo(Retailer::class)
      ->with('reference_plan', 'retailer_category', 'retailer_classification');
  }

  public function order_details()
  {
    return $this->hasMany(OrderDetail::class)
      ->with('sku');
  }
}
