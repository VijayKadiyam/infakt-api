<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserFamilyDetail extends Model
{
  protected $fillable = [
    'name', 'dob', 'gender', 'relation', 'occupation', 'contact_number'
  ];

  public function user()
  {
    return $this->belongsTo(User::class);
  }
}
