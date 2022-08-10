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
    $this->json('post', '/api/company_user', $this->payload, $this->headers)
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
          'email',
          'email_verified_at',
          'active',
          'phone',
          'api_token',
          'created_at',
          'updated_at',
          'address',
          'name',
          'doj',
          'dob',
          'company_designation_id',
          'company_state_branch_id',
          'pf_no',
          'uan_no',
          'esi_no',
          'companies',
        ]
      ]);
  }
}
