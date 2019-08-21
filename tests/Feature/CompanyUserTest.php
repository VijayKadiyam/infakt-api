<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CompanyUserTest extends TestCase
{
  use DatabaseTransactions;

  /** @test */
  function it_requires_following_fields()
  {
    $this->json('post', '/api/company_user', [], $this->headers)
         ->assertStatus(422)
         ->assertExactJson([
            "errors"            =>  [
              "company_id" =>  ["The company id field is required."],
              "user_id"    =>  ["The user id field is required."]
            ],
            "message" =>  "The given data was invalid."
        ]);
  }

  /** @test */
  function assign_company()
  {
    $userTwo = factory(\App\User::class)->create();
    $company = factory(\App\Company::class)->create();
    $userTwo->assignCompany($company->id);
    $check = $userTwo->hasCompany($company->id);
    $this->assertTrue($check);
  }

  /** @test */
  function assign_organization_to_user()
  {
    $this->disableEH();
    $userTwo = factory(\App\User::class)->create();
    $company = factory(\App\Company::class)->create();
    $this->payload      = [ 
      'user_id'    => $userTwo->id,
      'company_id' => $company->id
    ];
    $this->json('post', '/api/company_user', $this->payload , $this->headers)
      ->assertStatus(201)
      ->assertJson([
            'data'  =>  [
              'name'                    =>  $userTwo->name,
              'phone'                   =>  $userTwo->phone,
              'email'                   =>  $userTwo->email,
              'doj'                     =>  $userTwo->doj,
              'dob'                     =>  $userTwo->dob,
              'company_designation_id'  =>  $userTwo->company_designation_id,
              'company_state_branch_id' =>  $userTwo->company_state_branch_id,
              'pf_no'                   =>  $userTwo->pf_no,
              'uan_no'                  =>  $userTwo->uan_no,
              'esi_no'                  =>  $userTwo->esi_no,
              'salary'                  =>  $userTwo->salary,
              'image_path'              =>  $userTwo->image_path,
              'terms_accepted'          =>  $userTwo->terms_accepted,
              'companies'                   =>  [
                0 =>  [
                  'name'  =>  $company->name
                ]
              ]
            ]
          ])
        ->assertJsonStructureExact([
          'data'  =>  [
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
            'companies',
          ]
        ]);;;
  }
}
