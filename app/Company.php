<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
  protected $fillable = [

    'name',
    'email',
    'phone',
    'address',
    'logo_path',
    'contact_person'
  ];

  /*
   * A company belongs to many users
   *
   *@
   */
  public function users()
  {
    return $this->belongsToMany(User::class)
      ->where('active', '=', 1)
      ->with('roles', 'companies');
  }

  public function allUsers()
  {
    return $this->belongsToMany(User::class)
      ->with('roles', 'companies');
  }

  public function standards()
  {
    return $this->hasMany(Standard::class);
  }
}
