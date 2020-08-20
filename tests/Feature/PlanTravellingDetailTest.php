<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Plan;
use App\PlanTravellingDetail;

class PlanTravellingDetailTest extends TestCase
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

    $this->plan = factory(Plan::class)->create([
      'user_id'  =>  $this->user->id 
    ]);

    factory(PlanTravellingDetail::class)->create([
      'plan_id'  =>  $this->plan->id 
    ]);

    $this->payload = [ 
      'from'              => 'Mumbai',
      'to'                => 'Calcutta',
      'travelling_way_id' =>  '2',
      'transport_mode_id' =>  '1',
      'fare'              =>  '500',
      'image_path'        =>  '123'
    ];
  }

  /** @test */
  function user_must_be_logged_in_before_accessing_the_controller()
  {
    $this->json('post', '/api/plans/' . $this->plan->id . '/plan_travelling_details')
      ->assertStatus(401); 
  }

  /** @test */
  function it_requires_following_details()
  {
    $this->json('post', '/api/plans/' . $this->plan->id . '/plan_travelling_details', [], $this->headers)
      ->assertStatus(422)
      ->assertExactJson([
          "errors"  =>  [
            "from"              =>  ["The from field is required."],
            "to"                =>  ["The to field is required."],
            "travelling_way_id" =>  ["The travelling way id field is required."],
            "transport_mode_id" =>  ["The transport mode id field is required."],
          ],
          "message" =>  "The given data was invalid."
        ]);
  }

  /** @test */
  function add_new_plan_travelling_detail()
  {
    $this->disableEH();
    $this->json('post', '/api/plans/' . $this->plan->id . '/plan_travelling_details', $this->payload, $this->headers)
      ->assertStatus(201)
      ->assertJson([
          'data'   =>[
            'from'              => 'Mumbai',
            'to'                => 'Calcutta',
            'travelling_way_id' =>  '2',
            'transport_mode_id' =>  '1',
            'fare'              =>  '500',
            'image_path'        =>  '123'
          ]
        ])
      ->assertJsonStructureExact([
          'data'   => [
            'from',
            'to',
            'travelling_way_id',
            'transport_mode_id',
            'fare',
            'image_path',
            'plan_id',
            'updated_at',
            'created_at',
            'id'
          ],
          'success'
        ]);
  }

  /** @test */
  function list_of_plan_travelling_details()
  {
    $this->json('GET', '/api/plans/' . $this->plan->id . '/plan_travelling_details', [], $this->headers)
      ->assertStatus(200)
      ->assertJsonStructure([
          'data' => [
            0=>[
              'from'
            ] 
          ]
        ]);
      $this->assertCount(1, PlanTravellingDetail::all());
  }

  /** @test */
  function show_single_plan_travelling_detail()
  {
    $this->disableEH();
    $this->json('get', '/api/plans/' . $this->plan->id . '/plan_travelling_details/1', [], $this->headers)
      ->assertStatus(200)
      ->assertJson([
          'data'  => [
            'from' => 'Mumbai',
          ]
        ]);
  }

  /** @test */
  function update_single_plan_travelling_detail()
  {
    $this->disableEH();
    $payload = [ 
      'from'              => 'Mumbai 1',
      'to'                => 'Calcutta',
      'travelling_way_id' =>  '2',
      'transport_mode_id' =>  '1',
      'fare'              =>  '500'
    ];

    $this->json('patch', '/api/plans/' . $this->plan->id . '/plan_travelling_details/1', $payload, $this->headers)
      ->assertStatus(200)
      ->assertJson([
          'data'    => [
            'from'              => 'Mumbai 1',
            'to'                => 'Calcutta',
            'travelling_way_id' =>  '2',
            'transport_mode_id' =>  '1',
            'fare'              =>  '500'
          ]
       ])
      ->assertJsonStructureExact([
          'data'  => [
            'id',
            'plan_id',
            'from',
            'to',
            'travelling_way_id',
            'transport_mode_id',
            'fare',
            'created_at',
            'updated_at',
            'image_path'
          ],
          'success'
      ]);
  }
}
