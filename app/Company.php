<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
  protected $fillable = [
    'name', 'email', 'phone', 'address', 'logo_path', 'contact_person', 'time_zone', 'pds_word_path', 'pds_pdf_path', 'form_2_word_path', 'form_2_pdf_path', 'form_11_word_path', 'form_11_pdf_path', 'pf_word_path', 'pf_pdf_path', 'esic_benefit_word_path', 'esic_benefit_pdf_path', 'insurance_claim_word_path', 'insurance_claim_pdf_path', 'salary_slip_word_path', 'salary_slip_pdf_path', 'pms_policies_word_path', 'pms_policies_pdf_path', 'act_of_misconduct_word_path', 'act_of_misconduct_pdf_path', 'uan_activation_word_path', 'uan_activation_pdf_path', 'online_claim_word_path', 'online_claim_pdf_path', 'kyc_update_word_path', 'kyc_update_pdf_path', 'graduity_form_word_path', 'graduity_form_pdf_path', 'welcome_note', 'welcome_email_subject', 'welcome_email_body', 'df_1_email_subject', 'df_1_email_body', 'df_2_email_subject', 'df_2_email_body', 'attendance', 'leave', 'expenses', 'orders', 'recruiters'

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
}
