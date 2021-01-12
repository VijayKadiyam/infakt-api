<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Notice extends Model
{
  protected $fillable = [
    'name', 'title', 'description', 'imagepath'
  ];

  public function company()
  {
    return $this->belongsTo(Company::class);
  }
}
