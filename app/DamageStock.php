<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DamageStock extends Model
{
    protected $fillable = [
      'comapny_id', 'qty', 'mrp', 'manufacturing_date', 'sku_id', 'reference_plan_id', 'retailer_id'
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function retailer()
    {
        return $this->belongsTo(Retailer::class);
    }

    public function sku()
    {
      return $this->belongsTo(Sku::class);
    }
}
