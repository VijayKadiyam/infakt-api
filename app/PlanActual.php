<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PlanActual extends Model
{
  protected $table = "plan_actual";

  protected $fillable  = [
    'status', 'details', 'lat', 'lng'
  ];

  /*
   * A plan actual belongs to plan
   *
   *@
   */
  public function plan()
  {
    return $this->belongsTo(Plan::class);
  }
}
