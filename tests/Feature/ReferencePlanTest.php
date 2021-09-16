<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\ReferencePlan;

class ReferencePlanTest extends TestCase
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

    factory(\App\ReferencePlan::class)->create([
      'company_id'  =>  $this->company->id
    ]);

    $this->payload = [
      'name'     =>  'CST',
    ];
  }

  /** @test */
  function user_must_be_logged_in_before_accessing_the_controller()
  {
    $this->json('post', '/api/reference_plans')
      ->assertStatus(401);
  }

  /** @test */
  function it_requires_following_details()
  {
    $this->json('post', '/api/reference_plans', [], $this->headers)
      ->assertStatus(422)
      ->assertExactJson([
        "errors"  =>  [
          "name"    =>  ["The name field is required."]
        ],
        "message" =>  "The given data was invalid."
      ]);
  }

  /** @test */
  function add_new_reference_plan()
  {
    $this->disableEH();
    $this->json('post', '/api/reference_plans', $this->payload, $this->headers)
      ->assertStatus(201)
      ->assertJson([
        'data'   => [
          'name' => 'CST'
        ]
      ])
      ->assertJsonStructureExact([
        'data'   => [
          'name',
          'company_id',
          'updated_at',
          'created_at',
          'id'
        ]
      ]);
  }

  /** @test */
  function list_of_reference_plans()
  {
    $this->json('GET', '/api/reference_plans', [], $this->headers)
      ->assertStatus(200)
      ->assertJsonStructure([
        'data' => [
          0 => [
            'name'
          ]
        ]
      ]);
    $this->assertCount(1, ReferencePlan::all());
  }

  /** @test */
  function show_single_reference_plan()
  {
    $this->disableEH();
    $this->json('get', "/api/reference_plans/1", [], $this->headers)
      ->assertStatus(200)
      ->assertJson([
        'data'  => [
          'name' => 'Mulund',
        ]
      ]);
  }

  /** @test */
  function update_single_reference_plan()
  {
    $payload = [
      'name'  =>  'Mulund Updated'
    ];

    $this->json('patch', '/api/reference_plans/1', $payload, $this->headers)
      ->assertStatus(200)
      ->assertJson([
        'data'    => [
          'name'  =>  'Mulund Updated',
        ]
      ])
      ->assertJsonStructureExact([
        'data'  => [
          'id',
          'company_id',
          'name',
          'created_at',
          'updated_at',
          'town'
        ]
      ]);
  }

  /** @test */
  function Beats_mapping()
  {
    $this->disableEH();
    // $payload = [
    //   'name'                 => 'sangeetha',
    //   'phone'                => 9844778380,
    //   'email'                => 'sangeetha@gmail.com',
    //   'doj'               =>  '12-02-2019',
    //   'dob'               =>  '04-05-1992',
    //   'company_designation_id'  =>  1,
    //   'company_state_id' => 1,
    //   'company_state_branch_id' => 1,
    //   'pf_no'                   =>  '1234567654',
    //   'uan_no'                  =>  '1234565432',
    //   'esi_no'                  =>  '234565'
    // ];
    $this->json('post', '/api/reference_plans/beats_mapping', $this->payload, $this->headers)
      ->assertStatus(201)
      ->assertJsonStructure([
        'data' => [
          0 => [
            'name'
          ]
          ],'success'
      ]);
  }
}
