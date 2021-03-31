<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    protected $fillable = [
        'company_id', 'retailer_id', 'asset_name', 'status', 'description', 'reference_plan_id', 'unique_id', 'size', 'manufacturer_id'
      ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function retailer()
    {
        return $this->belongsTo(Retailer::class);
    }

    public function reference_plan()
    {
        return $this->belongsTo(ReferencePlan::class);
    }

    public function asset_statuses()
    {
        return $this->hasMany(AssetStatus::class)
          ->latest();
    }

    public function manufacturer()
    {
      return $this->belongsTo(Manufacturer::class);
    }
}
