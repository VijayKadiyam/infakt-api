<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserReference extends Model
{
  protected $fillable = [
    'name', 'company_name', 'designation', 'contact_number'
  ];

  public function user()
  {
    return $this->belongsTo(User::class);
  }
}
