<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Manufacturer extends Model
{
  protected $fillable = [
    'name', 'email'
  ];

  public function company()
  {
    return $this->belongsTo(Company::class);
  }
}
