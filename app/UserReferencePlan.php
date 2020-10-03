<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserReferencePlan extends Model
{
  protected $fillable = [
    'company_id', 'user_id', 'reference_plan_id', 'day', 'which_week'
  ];

  public function user()
  {
    return $this->belongsTo(User::class);
  }

  public function reference_plan()
  {
    return $this->belongsTo(ReferencePlan::class);
  }

  public function company()
  {
    return $this->belongsTo(Company::class);
  }
}
