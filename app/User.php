<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
  use Notifiable;

  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'name', 'email', 'password', 'phone',
    'joining_date', 'first_name', 'last_name', 'gender', 'image_path', 'id_given_by_school',
    'contact_number',
    'active',
    'is_deleted',
    'is_password_reseted',
    'is_mail_sent',
    'board_id',
  ];

  /**
   * The attributes that should be hidden for arrays.
   *
   * @var array
   */
  protected $hidden = [
    'password', 'remember_token',
  ];

  /*
   * To generate api token
   *
   *@
   */
  public function generateToken()
  {
    if ($this->api_token == null)
      $this->api_token = str_random(60);
    $this->save();
    return $this;
  }

  /*
   * An user belongs to company designation
   *
   *@
   */
  public function company_designation()
  {
    return $this->belongsTo(CompanyDesignation::class);
  }

  /*
   * An user belongs to company state
   *
   *@
   */
  public function company_state()
  {
    return $this->belongsTo(CompanyState::class);
  }

  /*
   * An user belongs to company state branch
   *
   *@
   */
  public function company_state_branch()
  {
    return $this->belongsTo(CompanyStateBranch::class);
  }

  /*
   * A user belongs to many roles
   *
   *@
   */
  public function roles()
  {
    return $this->belongsToMany(Role::class)
      ->with('permissions');
  }

  /**
   * Assign role to user
   *
   * @ 
   */
  public function assignRole($role)
  {
    return $this->roles()->sync([$role]);
  }

  /**
   * Check if the user has role
   *
   * @ 
   */
  public function hasRole($roles)
  {
    return $this->roles ? in_array($roles, $this->roles->pluck('id')->toArray()) : false;
  }

  /*
   * An user belongs to many companies
   *
   *@
   */
  public function companies()
  {
    return $this->belongsToMany(Company::class);
  }

  /**
   * Assign company to user
   *
   * @ 
   */
  public function assignCompany($company)
  {
    return $this->companies()->sync([$company]);
  }

  /**
   * Check if the user has company
   *
   * @ 
   */
  public function hasCompany($company)
  {
    return $this->companies ? in_array($company, $this->companies->pluck('id')->toArray()) : false;
  }

  public function content_reads()
  {
    return $this->hasMany(ContentRead::class);
  }
  public function user_classcodes()
  {
    return $this->hasMany(UserClasscode::class)
      ->with('classcode');
  }

  public function user_assignments()
  {
    return $this->hasMany(UserAssignment::class)
      ->with('assignment');
  }
  public function assignments()
  {
    return $this->hasMany(Assignment::class, 'created_by_id')->where('is_deleted', false);
  }
}
