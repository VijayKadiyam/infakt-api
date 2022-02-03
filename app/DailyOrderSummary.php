<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DailyOrderSummary extends Model
{
    protected $fillable  = [ 
       'sku_id', 'company_id', 'user_id', 'date', 'opening_stock', 'received_stock', 'purchase_returned_stock', 'sales_stock', 'returned_stock', 'closing_stock'
    ];
}
