<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
  protected $fillable = [
    'allowance_type_id', 'date', 'plan'
  ];  

  /*
   * A plan belongs to company
   *
   *@
   */
  public function company()
  {
    return $this->belongsTo(Company::class);
  }

  /*
   * A plan belongs to user
   *
   *@
   */
  public function user()
  {
    return $this->belongsTo(User::class);
  }

  /*
   * A plan belongs to allowance
   *
   *@
   */
  public function allowance_type()
  {
    return $this->belongsTo(AllowanceType::class);
  }

  /*
   * A plan has many plan actual
   *
   *@
   */
  public function plan_actuals()
  {
    return $this->hasMany(PlanActual::class);
  }

  /*
   * A plan has many plan travelling details
   *
   *@
   */
  public function plan_travelling_details()
  {
    return $this->hasMany(PlanTravellingDetail::class)
      ->with('travelling_way', 'transport_mode')
      ->latest();
  }
}
