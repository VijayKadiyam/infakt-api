<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\CompanyStateBranch;

class CompanyStateBranchTest extends TestCase
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

    $this->companyState = factory(\App\CompanyState::class)->create([
      'company_id'  =>  $this->company->id 
    ]);

    factory(\App\CompanyStateBranch::class)->create([
      'company_state_id'  =>  $this->companyState->id
    ]);

    $this->payload = [ 
      'name'     =>  'Kalyan',
    ];
  }

  /** @test */
  function user_must_be_logged_in_before_accessing_the_controller()
  {
    $this->json('post', "/api/company_states/$this->companyState->id/company_state_branches")
      ->assertStatus(401); 
  }

  /** @test */
  function it_requires_following_details()
  {
    $this->json('post', "/api/company_states/" .$this->companyState->id. "/company_state_branches", [], $this->headers)
      ->assertStatus(422)
      ->assertExactJson([
          "errors"  =>  [
            "name"    =>  ["The name field is required."]
          ],
          "message" =>  "The given data was invalid."
        ]);
  }

  /** @test */
  function add_new_company_state_branch()
  {
    $this->disableEH();
    $this->json('post', "/api/company_states/" . $this->companyState->id . "/company_state_branches", $this->payload, $this->headers)
      ->assertStatus(201)
      ->assertJson([
          'data'   =>[
            'name' => 'Kalyan'
          ]
        ])
      ->assertJsonStructureExact([
          'data'   => [
            'name',
            'company_state_id',
            'updated_at',
            'created_at',
            'id'
          ]
        ]);
  }

  /** @test */
  function list_of_company_state_branches()
  {
    $this->json('GET', "/api/company_states/" . $this->companyState->id . "/company_state_branches",[], $this->headers)
      ->assertStatus(200)
      ->assertJsonStructure([
          'data' => [
            0=>[
              'name'
            ] 
          ]
        ]);
      $this->assertCount(1, CompanyStateBranch::all());
  }

  /** @test */
  function show_single_company_state_branch()
  {
    $this->json('get', "/api/company_states/" . $this->companyState->id . "/company_state_branches/1", [], $this->headers)
      ->assertStatus(200)
      ->assertJson([
          'data'  => [
            'name'=> 'Thane',
          ]
        ]);
  }

  /** @test */
  function update_single_company_state_branch()
  {
    $payload = [ 
      'name'  =>  'Panvel'
    ];

    $this->json('patch', "/api/company_states/" . $this->companyState->id . "/company_state_branches/1", $payload, $this->headers)
      ->assertStatus(200)
      ->assertJson([
          'data'    => [
            'name'  =>  'Panvel',
          ]
       ])
      ->assertJsonStructureExact([
          'data'  => [
            'id',
            'company_state_id',
            'name',
            'created_at',
            'updated_at'
          ]
      ]);
  }
}
