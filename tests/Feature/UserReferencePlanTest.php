<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\ReferencePlan;
use App\UserReferencePlan;

class UserReferencePlanTest extends TestCase
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

    $this->referencePlan = factory(\App\ReferencePlan::class)->create([
      'company_id'  =>  $this->company->id 
    ]);

    factory(\App\UserReferencePlan::class)->create([
      'company_id'  =>  $this->company->id,
      'user_id'     =>  $this->user->id,
      'reference_plan_id' =>  $this->referencePlan->id,
    ]);    

    $this->payload = [ 
      'user_id'     =>  $this->user->id,
      'reference_plan_id' =>  $this->referencePlan->id,
      'day'         =>  2,
      'which_week'  =>  2 
    ];
  }

  /** @test */
  function user_must_be_logged_in_before_accessing_the_controller()
  {
    $this->json('post', '/api/user_reference_plans')
      ->assertStatus(401); 
  }

  /** @test */
  function it_requires_following_details()
  {
    $this->json('post', '/api/user_reference_plans', [], $this->headers)
      ->assertStatus(422)
      ->assertExactJson([
          "errors"  =>  [
            "user_id"               =>  ["The user id field is required."],
            "reference_plan_id"     =>  ["The reference plan id field is required."],
            "day"                   =>  ["The day field is required."],
            "which_week"            =>  ["The which week field is required."],
          ],
          "message" =>  "The given data was invalid."
        ]);
  }

   /** @test */
  function add_new_reference_plan()
  {
    $this->disableEH();
    $this->json('post', '/api/user_reference_plans', $this->payload, $this->headers)
      ->assertStatus(201)
      ->assertJson([
          'data'   =>[
            'user_id'     =>  $this->user->id,
            'reference_plan_id' =>  $this->referencePlan->id,
            'day'         =>  2,
            'which_week'  =>  2 
          ]
        ])
      ->assertJsonStructureExact([
          'data'   => [
            'user_id',
            'reference_plan_id',
            'day',
            'which_week',
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
    $this->json('GET', '/api/user_reference_plans',[], $this->headers)
      ->assertStatus(200)
      ->assertJsonStructure([
          'data' => [
            0=>[
              'reference_plan_id'
            ] 
          ]
        ]);
      $this->assertCount(1, UserReferencePlan::all());
  }

  /** @test */
  function show_single_reference_plan()
  {
    $this->disableEH();
    $this->json('get', "/api/user_reference_plans/1", [], $this->headers)
      ->assertStatus(200)
      ->assertJson([
          'data'  => [
            'reference_plan_id' =>  $this->referencePlan->id,
          ]
        ]);
  }

  /** @test */
  function update_single_reference_plan()
  {
    $payload = [ 
      'day'         =>  3,
    ];

    $this->json('patch', '/api/user_reference_plans/1', $payload, $this->headers)
      ->assertStatus(200)
      ->assertJson([
          'data'    => [
            'day'         =>  3,
          ]
       ])
      ->assertJsonStructureExact([
          'data'  => [
            'id',
            'company_id',
            'user_id',
            'reference_plan_id',
            'day',
            'which_week',
            'created_at',
            'updated_at'
          ]
      ]);
  }

}
