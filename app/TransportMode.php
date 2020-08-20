<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TransportMode extends Model
{
  protected $fillable = [
    'name'
  ];

  /*
   * A transport mode belongs to company
   *
   *@
   */
  public function company()
  {
    return $this->belongsTo(Company::class);
  }
}
