<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
  protected $fillable = [
    'qty', 'retailer_id', 'user_id', 'company_id', 'value', 'order_id', 'quantity', 'unit_price', 'bill_value', 'sku_type', 'offer', 'offer_type', 'offer_amount', 'total_bill_value', 'invoice_no', 'company_id', 'sku_id'
  ];

  /*
   * A sale belongs to sku
   *
   *@
   */
  public function sku()
  {
    return $this->belongsTO(Sku::class);
  }

  /*
   * A sale belongs to retailer
   *
   *@
   */
  public function retailer()
  {
    return $this->belongsTo(Retailer::class)
      ->with('retailer_category', 'retailer_classification', 'reference_plan');
  }

  public function user()
  {
    return $this->belongsTo(User::class);
  }
}
