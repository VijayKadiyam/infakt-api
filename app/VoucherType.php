<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VoucherType extends Model
{
  protected $fillable = [
    'name'
  ];

  /*
   * A voucher type belongs to company
   *
   *@
   */
  public function company()
  {
    return $this->belongsTo(Company::class);
  }
}
