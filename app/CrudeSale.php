<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CrudeSale extends Model
{
  protected $fillable = [
    'outlet_name', 'uid', 'name_of_person', 'cell_no', 'sku', 'qty', 'unit_price', 'bill_value', 'sku_type', 'offer', 'offer_type', 'offer_amount', 'total_bill_value', 'invoice_no', 'company_id', 'qty_returned', 'final_bill_value'
  ];
}
