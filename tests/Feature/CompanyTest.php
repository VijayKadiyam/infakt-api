<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Company;

class CompanyTest extends TestCase
{
  use DatabaseTransactions;

  protected $company;
  
  public function setUp()
  {
    parent::setUp();

    $this->company = factory(\App\Company::class)->create([
      'name' => 'test'
    ]);

    $this->user->assignRole(1);
    $this->user->assignCompany($this->company->id);

    $this->payload = [
      'name'    =>  'AAIBUZZ',
      'phone'   =>  345765433,
      'email'   =>  'email@gmail.com',
      'address' =>  '606, Vardhaman Plaza',
      'time_zone' =>  'Asia/Calcutta'
    ];
  }

  /** @test */
  function user_must_be_logged_in_before_accessing_the_controller()
  {
    $this->json('post', '/api/companies')
      ->assertStatus(401); 
  }

  /** @test */
  function it_requires_following_details()
  {
    $this->json('post', '/api/companies', [], $this->headers)
      ->assertStatus(422)
      ->assertExactJson([
          "errors"  =>  [
            "name"    =>  ["The name field is required."],
            "email"   =>  ["The email field is required."],
            "phone"   =>  ["The phone field is required."],
            "address" =>  ["The address field is required."],
            "time_zone" =>  ["The time zone field is required."],
          ],
          "message" =>  "The given data was invalid."
        ]);
  }

  /** @test */
  function add_new_organization()
  {
    $this->disableEH();
    $this->json('post', '/api/companies', $this->payload, $this->headers)
      ->assertStatus(201)
      ->assertJson([
          'data'   =>[
            'name' => 'AAIBUZZ'
          ]
        ])
      ->assertJsonStructureExact([
          'data'   => [
            'name',
            'phone',
            'email',
            'address',
            'time_zone',  
            'updated_at',
            'created_at',
            'id'
          ]
        ]);
    $this->company->saveDefaultDesignations();
    $this->assertCount(1, $this->company->company_designations);
    $this->company->saveDefaultCompanyLeaves();
    $this->assertCount(12, $this->company->company_leaves);
  }

  /** @test */
  function list_of_companies()
  {
    $this->json('GET', '/api/companies',[], $this->headers)
      ->assertStatus(200)
      ->assertJsonStructure([
          'data' => [
            0=>[
              'name'
            ] 
          ]
        ]);
      $this->assertCount(2, Company::all());
  }

  /** @test */
  function show_single_company()
  {
    $this->json('get', "/api/companies/2", [], $this->headers)
      ->assertStatus(200)
      ->assertJson([
          'data'  => [
            'name'=> 'test',
          ]
        ]);
  }

  /** @test */
  function update_single_company()
  {
    $payload = [ 
      'name'  =>  'AAIBUZZZ'
    ];

    $this->json('patch', '/api/companies/2', $payload, $this->headers)
      ->assertStatus(200)
      ->assertJson([
          'data'    => [
            'name'  =>  'AAIBUZZZ',
          ]
       ])
      ->assertJsonStructureExact([
          'data'  => [
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
            'attendance', 'leave', 'expenses', 'orders', 'recruiters'
          ]
      ]);
  }

}
