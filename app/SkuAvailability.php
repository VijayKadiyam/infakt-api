<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SkuAvailability extends Model
{
  protected $fillable = [
    'reference_plan_id', 'retailer_id', 'sku_id', 'is_available', 'date'
  ];

  public function company()
  {
    return $this->belongsTo(Company::class);
  }

  public function retailer()
  {
    return $this->hasMany(Retailer::class);
  }

  public function reference_plans()
  {
    return $this->belongsTo(ReferencePlan::class);
  }
}
