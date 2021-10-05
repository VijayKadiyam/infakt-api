<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PasswordResetTest extends TestCase
{
  use DatabaseTransactions;

  /** @test */
  function password_resets_requires_email_token()
  {
    $this->json('post', '/api/reset_password', [], $this->headers)
      ->assertStatus(422)
      ->assertExactJson([
        "errors"   =>  [
          "email"     =>  ["The email field is required."],
          "password"  =>  ["The password field is required."]
        ],
        "message"  =>  "The given data was invalid."
      ]);
  }

  /** @test */
  function reset_password()
  {
    $this->disableEH();

    $payLoad  = [
      'email'                 => $this->user->email,
      'password'              => '1234567',
      'password_confirmation' => '1234567',
    ];

    $this->json('post', '/api/reset_password', $payLoad, $this->headers)
      ->assertStatus(201)
      ->assertJson([
        'data'    => [
          'email' => $this->user->email
        ]
      ])
      ->assertJsonStructureExact([
        'data'   => [
          'id',
          'email',
          'email_verified_at',
          'active',
          'phone',
          'api_token',
          'created_at',
          'updated_at',
          'image_path',
          'address',
          'resume_path',
          'photo_path',
          'residential_proof_path',
          'education_proof_path',
          'pan_card_path',
          'adhaar_card_path',
          'esi_card_path',
          'cancelled_cheque_path',
          'salary_slip_path',
          'birth_certificate_path',
          'passport_path',
          'driving_license_path',
          'school_leaving_certificate_path',
          'mark_sheet_path',
          'experience_certificate_path',
          'prev_emp_app_letter_path',
          'form_2_path',
          'form_11_path',
          'graduity_form_path',
          'app_letter_path',
          'pds_form_path',
          'full_name',
          'father_name',
          'surname',
          'mother_name',
          'marital_status',
          'pan_no',
          'adhaar_no',
          'pre_room_no',
          'pre_building',
          'pre_area',
          'pre_road',
          'pre_city',
          'pre_state',
          'pre_pincode',
          'pre_mobile',
          'pre_email',
          'per_room_no',
          'per_building',
          'per_area',
          'per_road',
          'per_city',
          'per_state',
          'per_pincode',
          'per_mobile',
          'per_email',
          'blood_group',
          'bank_name',
          'bank_acc_no',
          'bank_ifsc_code',
          'bank_branch_name',
          'data_submitted',
          'is_fresher',
          'pds_form_sign_path',
          'form_2_sign_path',
          'form_11_sign_path',
          'graduity_form_sign_path',
          'password_backup',
          'pds_form_checked',
          'form_2_checked',
          'form_11_checked',
          'graduity_form_checked',
          'beat_type_id',
          'so_id',
          'asm_id',
          'rms_id',
          'nsm_id',
          'distributor_id',
          'name',
          'doj',
          'dob',
          'company_designation_id',
          'company_state_branch_id',
          'pf_no',
          'uan_no',
          'esi_no',
          'salary',
          'employee_code',
          'asm_area',
          'asm_name',
          'uid_no',
          'terms_accepted',
          'company_state_id',
          'unique_id',
          'appointment_letter',
          'contract_expiry',
          'gender',
          'region',
          'state_code',
          'supervisor_id',
          'channel',
          'chain_name',
          'billing_code',
          'ba_name',
          'location',
          'city',
          'state',
          'rsm',
          'asm',
          'supervisor_name',
          'store_type',
          'brand',
        ]
      ]);
  }
}
