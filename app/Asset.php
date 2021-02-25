<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    protected $fillable = [
        'company_id', 'retailer_id', 'asset_name', 'status', 'description'
      ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function retailer()
    {
        return $this->belongsTo(Retailer::class);
    }
}
