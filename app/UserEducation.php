<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserEducation extends Model
{
  protected $fillable = [
    'examination', 'school', 'passing_year', 'percent'
  ];

  public function user()
  {
    return $this->belongsTo(User::class);
  }
}
