<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use PhpParser\Node\Expr\Assign;

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


  public function user_standards()
  {
    return $this->hasMany(UserStandard::class);
  }

  public function standards()
  {
    return $this->hasMany(Standard::class);
  }

  public function user_sections()
  {
    return $this->hasMany(UserSection::class);
  }

  public function sections()
  {
    return $this->hasMany(Section::class);
  }

  public function classcodes()
  {
    return $this->hasMany(Classcode::class);
  }

  public function assignments()
  {
    return $this->hasMany(Assignment::class);
  }

  public function assignment_classcodes()
  {
    return $this->hasMany(AssignmentClasscode::class);
  }

  public function user_assignments()
  {
    return $this->hasMany(UserAssignment::class);
  }
}
