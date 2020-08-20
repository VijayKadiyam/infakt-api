<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\PlanActual;

class PlanActualTest extends TestCase
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

    $this->allowanceType = factory(\App\AllowanceType::class)->create([
      'company_id'  =>  $this->company->id 
    ]);

    $this->plan = factory(\App\Plan::class)->create([
      'user_id'  =>  $this->user->id 
    ]);

    factory(PlanActual::class)->create([
      'plan_id'  =>  $this->plan->id 
    ]);

    $this->payload = [ 
      'status'  =>  1,
      'details' =>  'Done',
      'lat'     =>  '12',
      'lng'     =>  '321'
    ];
  }

  /** @test */
  function user_must_be_logged_in_before_accessing_the_controller()
  {
    $this->json('post', '/api/plans/' . $this->plan->id . '/plan_actual')
      ->assertStatus(401); 
  }

  /** @test */
  function it_requires_following_details()
  {
    $this->json('post', '/api/plans/' . $this->plan->id . '/plan_actual', [], $this->headers)
      ->assertStatus(422)
      ->assertExactJson([
          "errors"  =>  [
            "status"               =>  ["The status field is required."],
            "details"               =>  ["The details field is required."]
          ],
          "message" =>  "The given data was invalid."
        ]);
  }

  /** @test */
  function add_new_plan_actual()
  {
    $this->disableEH();
    $this->json('post', '/api/plans/' . $this->plan->id . '/plan_actual', $this->payload, $this->headers)
      ->assertStatus(201)
      ->assertJson([
          'data'   =>[
            'status'  =>  1,
            'details' =>  'Done',
          ]
        ])
      ->assertJsonStructureExact([
          'data'   => [
            'id',
            'plan_id',
            'status',
            'details',
            'created_at',
            'updated_at',
            'lat',
            'lng'
          ],
          'success'
        ]);
  }

  /** @test */
  function list_of_plan_actuals()
  {
    $this->json('GET', '/api/plans/' . $this->plan->id . '/plan_actual', [], $this->headers)
      ->assertStatus(200)
      ->assertJsonStructure([
          'data' => [
            0=>[
              'status'
            ] 
          ]
        ]);
      $this->assertCount(1, PlanActual::all());
  }

  /** @test */
  function show_single_plan_actual()
  {
    $this->disableEH();
    $this->json('get', '/api/plans/' . $this->plan->id . '/plan_actual/1', [], $this->headers)
      ->assertStatus(200)
      ->assertJson([
          'data'  => [
            'status' => 1,
          ]
        ]);
  }

  /** @test */
  function update_single_plan()
  {
    $this->disableEH();
    $payload = [ 
      'status'  =>  0,
      'details' =>  'Done Updated',
    ];

    $this->json('post', '/api/plans/' . $this->plan->id . '/plan_actual', $payload, $this->headers)
      ->assertStatus(201)
      ->assertJson([
          'data'    => [
            'status'  =>  0,
            'details' =>  'Done Updated',
          ]
       ])
      ->assertJsonStructureExact([
          'data'  => [
            'id',
            'plan_id',
            'status',
            'details',
            'created_at',
            'updated_at',
            'lat',
            'lng'
          ],
          'success'
      ]);
  }
}
