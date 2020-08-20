<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
  protected $fillable = [
    'voucher_type_id', 'amount', 'description'
  ];

  /*
   * A voucher belongs to user
   *
   *@
   */
  public function user()
  {
    return $this->belongsTo(User::class);
  }

  /*
   * A voucher belongs to voucher type
   *
   *@
   */
  public function voucher_type()
  {
    return $this->belongsTo(VoucherType::class);
  }
}
