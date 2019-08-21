<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CompanyLeavePatternTest extends TestCase
{
  use DatabaseTransactions;

  public function setUp()
  {
    parent::setUp();

    $this->company = factory(\App\Company::class)->create([
      'name' => 'test'
    ]);
    $this->user->assignCompany($this->company->id);
    $this->headers['company-id'] = $this->company->id;
  }

  /** @test */
  function user_must_be_logged_in_before_accessing_the_controller()
  {
    $this->json('post', '/api/company_leave_pattern')
      ->assertStatus(401); 
  }

  /** @test */
  function it_requires_following_fields()
  {
    $this->json('post', '/api/company_leave_pattern', [], $this->headers)
         ->assertStatus(422)
         ->assertExactJson([
            "errors"            =>  [
              "company_id" =>  ["The company id field is required."],
              "leave_pattern_id"    =>  ["The leave pattern id field is required."]
            ],
            "message" =>  "The given data was invalid."
        ]);
  }

  /** @test */
  function assign_leave_pattern()
  {
    $company = factory(\App\Company::class)->create();
    $company->assignLeavePattern(1);
    $check = $company->hasLeavePattern(1);
    $this->assertTrue($check);
  }

  /** @test */
  function assign_leave_pattern_to_company()
  {
    $this->disableEH();
    $company = factory(\App\Company::class)->create();
    $this->payload      = [ 
      'leave_pattern_id'    => 1,
      'company_id' => $company->id
    ];
    $this->json('post', '/api/company_leave_pattern', $this->payload , $this->headers)
      ->assertStatus(201)
      ->assertJson([
            'data'  =>  [
              'name'  =>  $company->name
            ]
          ])
        ->assertJsonStructureExact([
          'data'  =>  [
            'id',
            'name',
            'email',
            'phone',
            'address',
            'logo_path',
            'contact_person',
            'created_at',
            'updated_at',
            'time_zone',
            'pds_word_path',
            'pds_pdf_path', 
            'form_2_word_path', 
            'form_2_pdf_path',
            'form_11_word_path',
            'form_11_pdf_path',
            'pf_word_path',
            'pf_pdf_path',
            'esic_benefit_word_path',
            'esic_benefit_pdf_path',
            'insurance_claim_word_path',
            'insurance_claim_pdf_path',
            'salary_slip_word_path',
            'salary_slip_pdf_path',
            'pms_policies_word_path',
            'pms_policies_pdf_path',
            'act_of_misconduct_word_path',
            'act_of_misconduct_pdf_path',
            'uan_activation_word_path',
            'uan_activation_pdf_path',
            'online_claim_word_path',
            'online_claim_pdf_path',
            'kyc_update_word_path',
            'kyc_update_pdf_path',
            'graduity_form_word_path',
            'graduity_form_pdf_path',
            'welcome_note',
            'welcome_email_subject',
            'welcome_email_body',
            'df_1_email_subject',
            'df_1_email_body',
            'df_2_email_subject',
            'df_2_email_body',
            'leave_patterns'
          ]
        ]);;;
  }
}
