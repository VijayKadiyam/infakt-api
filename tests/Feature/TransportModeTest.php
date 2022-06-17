<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\TransportMode;

class TransportModeTest extends TestCase
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

    factory(TransportMode::class)->create([
      'company_id'  =>  $this->company->id 
    ]);

    $this->payload = [ 
      'name'     =>  'Car',
    ];
  }

  /** @test */
  function user_must_be_logged_in_before_accessing_the_controller()
  {
    $this->json('post', '/api/transport_modes')
      ->assertStatus(401); 
  }

  /** @test */
  function it_requires_following_details()
  {
    $this->json('post', '/api/transport_modes', [], $this->headers)
      ->assertStatus(422)
      ->assertExactJson([
          "errors"  =>  [
            "name"    =>  ["The name field is required."]
          ],
          "message" =>  "The given data was invalid."
        ]);
  }

  /** @test */
  function add_new_company_break_type()
  {
    $this->disableEH();
    $this->json('post', '/api/transport_modes', $this->payload, $this->headers)
      ->assertStatus(201)
      ->assertJson([
          'data'   =>[
            'name' => 'Car'
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
  function list_of_transport_modes()
  {
    $this->json('GET', '/api/transport_modes',[], $this->headers)
      ->assertStatus(200)
      ->assertJsonStructure([
          'data' => [
            0=>[
              'name'
            ] 
          ]
        ]);
      $this->assertCount(1, TransportMode::all());
  }

  /** @test */
  function show_single_transport_mode()
  {
    $this->disableEH();
    $this->json('get', "/api/transport_modes/1", [], $this->headers)
      ->assertStatus(200)
      ->assertJson([
          'data'  => [
            'name'  => 'Bike',
          ]
        ]);
  }

  /** @test */
  function update_single_transport_mode()
  {
    $this->disableEH();
    $payload = [ 
      'name'  =>  'Riksha'
    ];

    $this->json('patch', '/api/transport_modes/1', $payload, $this->headers)
      ->assertStatus(200)
      ->assertJson([
          'data'    => [
            'name'  =>  'Riksha',
          ]
       ])
      ->assertJsonStructureExact([
          'data'  => [
            'id',
            'company_id',
            'name',
            'created_at',
            'updated_at'
          ]
      ]);
  }
}
