<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Resume extends Model
{
  protected $fillable = [
    'user_id', 'name', 'gender', 'mobile_1', 'mobile_2', 'present_company_name', 'designation', 'work_experience', 'current_salary', 'location', 'lat', 'lng' 
  ];
  
  public function user()
  {
    return $this->belongsTo(User::class);
  }
}
