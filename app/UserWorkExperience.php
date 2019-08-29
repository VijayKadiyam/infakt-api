<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserWorkExperience extends Model
{
  protected $fillable = [
    'company_name', 'from', 'to', 'designation', 'uan_no', 'esic_no'
  ];

  public function user()
  {
    return $this->belongsTo(User::class);
  }
}
