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
    'name', 'email', 'password', 'phone', 'doj', 'dob', 'company_designation_id', 'company_state_branch_id', 'pf_no', 'uan_no', 'esi_no', 'active', 'salary', 'image_path', 'employee_code', 'asm_area', 'asm_name', 'uid_no', 'company_state_id', 'address', 'unique_id'
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
    if($this->api_token == null)
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

  /*
   * An user has many attendances
   *
   *@
   */
  public function user_attendances()
  {
    return $this->hasMany(UserAttendance::class)
      ->with('user_attendance_breaks');
  }

  /*
   * An user has many applications
   *
   *@
   */
  public function user_applications()
  {
    return $this->hasMany(UserApplication::class)
      ->with('company_leave', 'application_approvals', 'user', 'leave_type')
      ->latest();
  }

  /*
   * An user belongs to many supervisor
   *
   *@
   */
  public function supervisors()
  {
    return $this->belongsToMany(User::class, 'supervisor_user', 'user_id', 'supervisor_id');
  }

  /*
   * A supervisor belongs to many user
   *
   *@
   */
  public function users()
  {
    return $this->belongsToMany(User::class, 'supervisor_user', 'supervisor_id', 'user_id');
  }

  /**
   * Assign supervisor to user
   *
   * @ 
   */
  public function assignSupervisor($supervisor)
  {
    return $this->supervisors()->sync([$supervisor]);
  }

  /**
   * Check if the user has supervisor
   *
   * @ 
   */
  public function hasSupervisor($supervisor)
  {
    return $this->supervisors ? in_array($supervisor, $this->supervisors->pluck('id')->toArray()) : false;
  }

  /*
   * An user has many user sales
   *
   *@
   */
  public function user_sales()
  {
    return $this->hasMany(UserSale::class);
  }

  /*
   * An user has many user breaks
   *
   *@
   */
  public function user_breaks()
  {
    return $this->hasMany(UserBreak::class);
  }

  /*
   * A user has many plans
   *
   *@
   */
  public function plans()
  {
    return $this->hasMany(Plan::class)
      ->with('plan_actuals')
      ->latest();
  }

  /*
   * A user has many vouchers
   *
   *@
   */
  public function vouchers()
  {
    return $this->hasMany(Voucher::class);
  }

  /*
   * An user has many user locations
   *
   *@
   */
  public function user_locations()
  {
    return $this->hasMany(UserLocation::class);
  }

  /*
   * An user has many geolocator user locations
   *
   *@
   */
  public function geolocator_user_locations()
  {
    return $this->hasMany(GeolocatorUserLocation::class);
  }
}

