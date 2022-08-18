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
    // return $this->belongsTo(User::class);
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

  /*
   * An user has many marks
   *
   *@
   */
  public function marks()
  {
    return $this->hasMany(Mark::class);
  }

  public function user_offer_letters()
  {
    return $this->hasMany(UserOfferLetter::class)
      ->with('user');
  }

  public function user_appointment_letters()
  {
    return $this->hasMany(UserAppointmentLetter::class)
      ->with('user');
  }

  public function user_experience_letters()
  {
    return $this->hasMany(UserExperienceLetter::class);
  }

  public function user_warning_letters()
  {
    return $this->hasMany(UserWarningLetter::class);
  }

  public function user_promotion_letters()
  {
    return $this->hasMany(UserPromotionLetter::class);
  }

  public function user_renewal_letters()
  {
    return $this->hasMany(UserRenewalLetter::class);
  }

  public function user_increemental_letters()
  {
    return $this->hasMany(UserIncreementalLetter::class);
  }

  public function user_termination_letters()
  {
    return $this->hasMany(UserTerminationLetter::class);
  }

  public function user_full_final_letters()
  {
    return $this->hasMany(UserFullFinalLetter::class);
  }

  public function user_work_experiences()
  {
    return $this->hasMany(UserWorkExperience::class);
  }

  public function user_educations()
  {
    return $this->hasMany(UserEducation::class);
  }

  public function user_family_details()
  {
    return $this->hasMany(UserFamilyDetail::class);
  }

  public function user_references()
  {
    return $this->hasMany(UserReference::class);
  }

  public function notifications()
  {
    return $this->hasMany(Notification::class)
      ->with('user');
  }

  public function salaries()
  {
    return $this->hasMany(Salary::class);
  }

  public function inquiry_remarks()
  {
    return $this->hasMany(InquiryRemark::class);
  }

  public function inquiry_followups()
  {
    return $this->hasMany(InquiryFollowup::class);
  }

  public function resumes()
  {
    return $this->hasMany(Resume::class);
  }

  public function sales()
  {
    return $this->hasMany(Sale::class)
      ->with('retailer', 'sku', 'user');
  }

  public function reference_plan()
  {
    return $this->belongsTo(ReferencePlan::class, 'beat_type_id');
  }

  public function supervisor()
  {
    return $this->belongsTo(User::class);
  }

  public function distributor()
  {
    return $this->belongsTo(User::class);
  }

  public function targets()
  {
    return $this->hasMany(Target::class)
      ->with('user');
  }
  public function focused_targets()
  {
    return $this->hasMany(FocusedTarget::class)
      ->with('user');
  }

  public function rms()
  {
    return $this->belongsTo(User::class);
  }

  public function asm()
  {
    return $this->belongsTo(User::class);
  }

  public function so()
  {
    return $this->belongsTo(User::class);
  }

  public function sos()
  {
    return $this->hasMany(User::class);
  }

  public function distributors()
  {
    return $this->belongsToMany(User::class, 'distributor_user', 'user_id', 'distributor_id');
  }

  public function assignDistributor($distributor)
  {
    return $this->distributors()->syncWithoutDetaching([$distributor]);
  }

  public function unassignDistributor($distributor)
  {
    $this->distributors()->detach([$distributor]);
    $this->refresh();

    return $this;
  }

  public function hasDistributor($distributors)
  {
    return $this->distributors ? in_array($distributors, $this->distributors->pluck('id')->toArray()) : false;
  }

  public function reference_plans()
  {
    return $this->belongsToMany(ReferencePlan::class, 'distributor_reference_plan', 'distributor_id', 'reference_plan_id');
  }
  public function pjp_supervisors()
  {
    return $this->belongsToMany(PjpSupervisor::class);
  }

  public function customers()
  {
    return $this->hasMany(Customer::class);
  }

  public function assignReferencePlan($reference_plan)
  {
    $this->reference_plans()->syncWithoutDetaching([$reference_plan]);
    $this->refresh();
    return;
  }

  public function unassignReferencePlan($reference_plan)
  {
    $this->reference_plans()->detach([$reference_plan]);
    $this->refresh();

    return $this;
  }

  public function hasReferencePlan($reference_plans)
  {
    return $this->reference_plans ? in_array($reference_plans, $this->reference_plans->pluck('id')->toArray()) : false;
  }

  public function tickets()
  {
    return $this->hasMany(Ticket::class);
  }
  public function daily_order_summaries()
  {
    return $this->hasMany(DailyOrderSummary::class);
  }
  public function monthly_order_summaries()
  {
    return $this->hasMany(MonthlyOrderSummary::class);
  }
  public function user_classcodes()
  {
    return $this->hasMany(UserClasscode::class);
  }
}
