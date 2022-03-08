<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Notice extends Model
{
  protected $fillable = [
    'name', 'title', 'description', 'imagepath', 'link', 'channel', 'brand', 'role_name'
  ];

  public function company()
  {
    return $this->belongsTo(Company::class);
  }
}
