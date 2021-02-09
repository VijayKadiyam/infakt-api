<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DamageStock extends Model
{
    protected $fillable = [
        'comapny_id', 'qty', 'mrp', 'manufacturing_date'
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
