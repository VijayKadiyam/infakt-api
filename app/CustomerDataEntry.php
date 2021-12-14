<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CustomerDataEntry extends Model
{
    protected $fillable = [
        'user_id',
        'retailer_id',
        'name',
        'number',
        'email',
        'product_brought',
        'sample_given',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function retailer()
    {
        return $this->belongsTo(Retailer::class);
    }
}
