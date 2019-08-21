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
            "email"     =>  ["The email field is required."] ,
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
            'name',
            'email',
            'email_verified_at',
            'active',
            'phone',
            'api_token',
            'doj',
            'dob',
            'company_designation_id',
            'company_state_branch_id',
            'pf_no',
            'uan_no',
            'esi_no',
            'created_at',
            'updated_at',
            'salary',
            'image_path',
            'employee_code',
            'asm_area',
            'asm_name',
            'uid_no',
            'terms_accepted',
            'company_state_id',
            'address',
            'unique_id',
            'appointment_letter',
            'contract_expiry',
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
          ]
        ]);
  }
}
