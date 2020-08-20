<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Mark extends Model
{
  protected $fillable = [
    'user_id', 'in_lat', 'in_lng', 'out_lat', 'out_lng'
  ];

  /*
   * A mark belongs to user
   *
   *@
   */
  public function user()
  {
    return $this->belongsTo(User::class);
  }
}
