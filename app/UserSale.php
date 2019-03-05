<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserSale extends Model
{
  protected $fillable = [
    'date', 'amount'
  ];

  /*
   * An user sale belongs to user
   *
   *@
   */
  public function user()
  {
    return $this->belongsTo(User::class);
  }
}
