<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\UserLocation;

class UserLocationTest extends TestCase
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

    factory(UserLocation::class)->create([
      'user_id'  =>  $this->user->id 
    ]);

    $this->date = (\Carbon\Carbon::now()->format('Y-m-d'));

    $this->payload = [ 
      'content' =>  [
        "uuid"    => "0b1ce00c-6950-4e6b-be2c-69bed89f39e5",
        "coords"  =>  [
          "speed"     => -1, 
          "heading"   => -1, 
          "accuracy"  => 38.2, 
          "altitude"  => 526,
          "latitude"  => 18.3573019, 
          "longitude" =>   74.6269666
        ], 
        "extras"  => [],
        "battery" => [
          "level"       =>  0.65,
          "is_charging" => false
        ], 
        "activity"  => [
          "type"        => "still", 
          "confidence"  => 100
        ], 
        "odometer"  => 73057.3, 
        "is_moving" =>true, 
        "timestamp" => "2019-08-08T06:20:21.950Z"
      ]
    ];
  }

  /** @test */
  function user_must_be_logged_in_before_accessing_the_controller()
  {
    $this->json('post', '/api/user_locations')
      ->assertStatus(401); 
  }

  /** @test */
  function it_requires_following_details()
  {
    $this->json('post', '/api/user_locations', [], $this->headers)
      ->assertStatus(422)
      ->assertExactJson([
          "errors"  =>  [
            "content"               =>  ["The content field is required."]
          ],
          "message" =>  "The given data was invalid."
        ]);
  }

  /** @test */
  function add_new_user_location()
  {
    $this->disableEH();
    $this->json('post', '/api/user_locations', $this->payload, $this->headers)
      ->assertStatus(201)
      ->assertJson([
          'data'   => [
            'content' =>  [
              'coords'  => []
            ]
          ]
        ])
      ->assertJsonStructureExact([
          'data'   => [
            'content',
            'address',
            'user_id',
            'updated_at',
            'created_at',
            'id'
          ],
          'success'
        ]);
  }

  /** @test */
  function list_of_suer_locations()
  {
    $this->json('GET', '/api/user_locations',[], $this->headers)
      ->assertStatus(200)
      ->assertJsonStructure([
          'data' => [
            0 =>  [
              'content'
            ] 
          ]
        ]);
      $this->assertCount(1, UserLocation::all());
  }

  /** @test */
  function list_of_user_location_of_request_user()
  {
    $this->json('GET', '/api/user_locations?user_id=' . $this->user->id . '&date=' . $this->date,[], $this->headers)
      ->assertStatus(200)
      ->assertJsonStructure([
          'data' => [
            0=>[
              'content'
            ] 
          ]
        ]);
  }

  /** @test */
  function show_single_user_location()
  {
    $this->disableEH();
    $this->json('get', "/api/user_locations/1", [], $this->headers)
      ->assertStatus(200)
      ->assertJson([
          'data'  => [
            'content' => 'Hello',
          ]
        ]);
  }

  /** @test */
  function update_single_plan()
  {
    $this->disableEH();
    $payload = [ 
      'content'  =>  'Hello Updated'
    ];

    $this->json('patch', '/api/user_locations/1', $payload, $this->headers)
      ->assertStatus(200)
      ->assertJson([
          'data'    => [
            'content'  =>  'Hello Updated'
          ]
       ])
      ->assertJsonStructureExact([
          'data'  => [
            'id',
            'user_id',
            'content',
            'created_at',
            'updated_at',
            'address'
          ],
          'success'
      ]);
  }
}
