<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
  protected $fillable = [
    'name', 'email', 'phone', 'address', 'logo_path', 'contact_person', 'time_zone', 'pds_word_path', 'pds_pdf_path', 'form_2_word_path', 'form_2_pdf_path', 'form_11_word_path', 'form_11_pdf_path', 'pf_word_path', 'pf_pdf_path', 'esic_benefit_word_path', 'esic_benefit_pdf_path', 'insurance_claim_word_path', 'insurance_claim_pdf_path', 'salary_slip_word_path', 'salary_slip_pdf_path', 'pms_policies_word_path', 'pms_policies_pdf_path', 'act_of_misconduct_word_path', 'act_of_misconduct_pdf_path', 'uan_activation_word_path', 'uan_activation_pdf_path', 'online_claim_word_path', 'online_claim_pdf_path', 'kyc_update_word_path', 'kyc_update_pdf_path', 'graduity_form_word_path', 'graduity_form_pdf_path', 'welcome_note', 'welcome_email_subject', 'welcome_email_body', 'df_1_email_subject', 'df_1_email_body', 'df_2_email_subject', 'df_2_email_body'

  ];

  /*
   * A company belongs to many users
   *
   *@
   */
  public function users()
  {
    return $this->belongsToMany(User::class)
      ->with('roles', 'companies', 'company_designation', 'company_state', 'company_state_branch', 'supervisors', 'user_attendances', 'user_appointment_letters');
  }

  /*
   * A company has many states
   *
   *@
   */
  public function company_states()
  {
    return $this->hasMany(CompanyState::class)
      ->with('company_state_branches');
  }

  /*
   * A company has many company designations
   *
   *@
   */
  public function company_designations()
  {
    return $this->hasMany(CompanyDesignation::class);
  }

  /*
   * Save Default companydesignations
   *
   *@
   */
  public function saveDefaultDesignations()
  {
    $designations[] = new CompanyDesignation(['name' => 'Admin']);
    $this->company_designations()->saveMany($designations);
  }

  /*
   * A company belongs to leave pattern
   *
   *@
   */
  public function leave_patterns()
  {
    return $this->belongsToMany(LeavePattern::class);
  }

  /**
   * Assign leave patter to company
   *
   * @ 
   */
  public function assignLeavePattern($leave_pattern)
  {
    return $this->leave_patterns()->sync([$leave_pattern]);
  }

  /**
   * Check if the company has leave pattern
   *
   * @ 
   */
  public function hasLeavePattern($leave_pattern)
  {
    return $this->leave_patterns ? in_array($leave_pattern, $this->leave_patterns->pluck('id')->toArray()) : false;
  }

  /*
   * A company has many company leaves
   *
   *@
   */
  public function company_leaves()
  {
    return $this->hasMany(CompanyLeave::class);
  }

  /*
   * Save Default company leaves
   *
   *@
   */
  public function saveDefaultCompanyLeaves()
  {
    $companyLeaves[] = new CompanyLeave(['leave_pattern_id' =>  2, 'name' => 'January', 'leaves' =>  0]);
    $companyLeaves[] = new CompanyLeave(['leave_pattern_id' =>  2, 'name' => 'February', 'leaves' =>  0]);
    $companyLeaves[] = new CompanyLeave(['leave_pattern_id' =>  2, 'name' => 'March', 'leaves' =>  0]);
    $companyLeaves[] = new CompanyLeave(['leave_pattern_id' =>  2, 'name' => 'April', 'leaves' =>  0]);
    $companyLeaves[] = new CompanyLeave(['leave_pattern_id' =>  2, 'name' => 'May', 'leaves' =>  0]);
    $companyLeaves[] = new CompanyLeave(['leave_pattern_id' =>  2, 'name' => 'June', 'leaves' =>  0]);
    $companyLeaves[] = new CompanyLeave(['leave_pattern_id' =>  2, 'name' => 'July', 'leaves' =>  0]);
    $companyLeaves[] = new CompanyLeave(['leave_pattern_id' =>  2, 'name' => 'August', 'leaves' =>  0]);
    $companyLeaves[] = new CompanyLeave(['leave_pattern_id' =>  2, 'name' => 'September', 'leaves' =>  0]);
    $companyLeaves[] = new CompanyLeave(['leave_pattern_id' =>  2, 'name' => 'October', 'leaves' =>  0]);
    $companyLeaves[] = new CompanyLeave(['leave_pattern_id' =>  2, 'name' => 'November', 'leaves' =>  0]);
    $companyLeaves[] = new CompanyLeave(['leave_pattern_id' =>  2, 'name' => 'December', 'leaves' =>  0]);
    $this->company_leaves()->saveMany($companyLeaves);
  }

  /*
   * A company has many break types
   *
   *@
   */
  public function break_types()
  {
    return $this->hasMany(BreakType::class);
  }

  /*
   * A company has many feedbacks
   *
   *@
   */
  public function feedbacks()
  {
    return $this->hasMany(Feedback::class);
  }

  /*
   * A company has many allowance types
   *
   *@
   */
  public function allowance_types()
  {
    return $this->hasMany(AllowanceType::class);
  }

  /*
   * A company has many transport modes
   *
   *@
   */
  public function transport_modes()
  {
    return $this->hasMany(TransportMode::class);
  }

  /*
   * A company has many travelling ways
   *
   *@
   */
  public function travelling_ways()
  {
    return $this->hasMany(TravellingWay::class);
  }

  /*
   * A company has many voucher types
   *
   *@
   */
  public function voucher_types()
  {
    return $this->hasMany(VoucherType::class);
  }

  /*
   * A company has many leave types
   *
   *@
   */
  public function leave_types()
  {
    return $this->hasMany(LeaveType::class);
  }

  /*
   * A company has many skus
   *
   *@
   */
  public function skus()
  {
    return $this->hasMany(Sku::class)
      ->with('stocks');
  }

  /*
   * A company has many sku types
   *
   *@
   */
  public function sku_types()
  {
    return $this->hasMany(SkuType::class);
  }

  /*
   * A company has many offer types
   *
   *@
   */
  public function offer_types()
  {
    return $this->hasMany(OfferType::class);
  }

  /*
   * A company has many offers
   *
   *@
   */
  public function offers()
  {
    return $this->hasMany(Offer::class)
      ->with('offer_type');
  }

  /*
   * A company has many retailers
   *
   *@
   */
  public function retailers()
  {
    return $this->hasMany(Retailer::class);
  }

  /*
   * A company has many units
   *
   *@
   */
  public function units()
  {
    return $this->hasMany(Unit::class);
  }

  /*
   * A company has many products
   *
   *@
   */
  public function products()
  {
    return $this->hasMany(Product::class)
      ->with('skus');
  }

  /*
   * A company has many reference plans
   *
   *@
   */
  public function reference_plans()
  {
    return $this->hasMany(ReferencePlan::class)
      ->with('retailers');
  }

  /*
   * A company has many reasons
   *
   *@
   */
  public function reasons()
  {
    return $this->hasMany(Reason::class);
  }

  /*
   * A company has many retailer categories
   *
   *@
   */
  public function retailer_categories()
  {
    return $this->hasMany(RetailerCategory::class);
  }

  /*
   * A company has many retailer classifications
   *
   *@
   */
  public function retailer_classifications()
  {
    return $this->hasMany(RetailerClassification::class);
  }

  public function inquiries()
  {
    return $this->hasMany(Inquiry::class);
  }
}
