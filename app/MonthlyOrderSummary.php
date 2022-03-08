<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MonthlyOrderSummary extends Model
{
    protected $fillable  = [
        'sku_id', 'company_id', 'user_id', 'month', 'year', 'opening_stock', 'received_stock', 'purchase_returned_stock', 'sales_stock', 'returned_stock', 'closing_stock'
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function sku()
    {
        return $this->belongsTo(Sku::class)
            ->with('product');
    }
}
