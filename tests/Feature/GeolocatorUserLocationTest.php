<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\GeolocatorUserLocation;

class GeolocatorUserLocationTest extends TestCase
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

    factory(GeolocatorUserLocation::class)->create([
      'user_id'  =>  $this->user->id 
    ]);

    $this->date = (\Carbon\Carbon::now()->format('Y-m-d'));

    $this->payload = [ 
      'lat' =>  'lat 1',
      'long' =>  'long 1',
    ];
  }

  /** @test */
  function user_must_be_logged_in_before_accessing_the_controller()
  {
    $this->json('post', '/api/geolocator_user_locations')
      ->assertStatus(401); 
  }

  /** @test */
  function it_requires_following_details()
  {
    $this->json('post', '/api/geolocator_user_locations', [], $this->headers)
      ->assertStatus(422)
      ->assertExactJson([
          "errors"  =>  [
            "lat"               =>  ["The lat field is required."],
            "long"               =>  ["The long field is required."]
          ],
          "message" =>  "The given data was invalid."
        ]);
  }

  /** @test */
  function add_new_user_location()
  {
    $this->disableEH();
    $this->json('post', '/api/geolocator_user_locations', $this->payload, $this->headers)
      ->assertStatus(201)
      ->assertJson([
          'data'   =>[
            'lat' =>  'lat 1',
            'long'  =>  'long 1'
          ]
        ])
      ->assertJsonStructureExact([
          'data'   => [
            'lat',
            'long',
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
    $this->json('GET', '/api/geolocator_user_locations',[], $this->headers)
      ->assertStatus(200)
      ->assertJsonStructure([
          'data' => [
            0 =>  [
              'lat'
            ] 
          ]
        ]);
      $this->assertCount(1, GeolocatorUserLocation::all());
  }

  /** @test */
  function list_of_user_location_of_request_user()
  {
    $this->json('GET', '/api/geolocator_user_locations?user_id=' . $this->user->id . '&date=' . $this->date,[], $this->headers)
      ->assertStatus(200)
      ->assertJsonStructure([
          'data' => [
            0=>[
              'lat'
            ] 
          ]
        ]);
  }

  /** @test */
  function show_single_user_location()
  {
    $this->disableEH();
    $this->json('get', "/api/geolocator_user_locations/1", [], $this->headers)
      ->assertStatus(200)
      ->assertJson([
          'data'  => [
            'lat' => '13.2',
          ]
        ]);
  }

  /** @test */
  function update_single_plan()
  {
    $this->disableEH();
    $payload = [ 
      'lat' => '13.3',
      'long' => '13.3',
    ];

    $this->json('patch', '/api/geolocator_user_locations/1', $payload, $this->headers)
      ->assertStatus(200)
      ->assertJson([
          'data'    => [
            'lat' => '13.3',
          ]
       ])
      ->assertJsonStructureExact([
          'data'  => [
            'id',
            'user_id',
            'lat',
            'long',
            'created_at',
            'updated_at',
          ],
          'success'
      ]);
  }

}
