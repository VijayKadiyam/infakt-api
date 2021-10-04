<?php

namespace Tests\Feature;

use App\PjpMarket;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PjpMarketTest extends TestCase
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
  
      factory(PjpMarket::class)->create([
        'company_id'  =>  $this->company->id 
      ]);
  
      $this->payload = [ 
        'pjp_id' => 1,
        'market_name' => 'Market Name',
        'gps_address' => 'Gps Address',
      ];
    }
  
    /** @test */
    function user_must_be_logged_in_before_accessing_the_controller()
    {
      $this->json('post', '/api/users/' . $this->user->id .  '/targets')
        ->assertStatus(401); 
    }
  
    /** @test */
    function it_requires_following_details()
    {
      $this->json('post', '/api/users/' . $this->user->id .  '/targets', [], $this->headers)
        ->assertStatus(422)
        ->assertExactJson([
            "errors"  =>  [
              "month"   =>  ["The month field is required."],
              "year"     =>  ["The year field is required."],
              "target"  =>  ["The target field is required."],
            ],
            "message" =>  "The given data was invalid."
          ]);
    }
  
    /** @test */
    function add_new_target()
    {
      $this->disableEH();
      $this->json('post', '/api/users/' . $this->user->id .  '/targets', $this->payload, $this->headers)
        ->assertStatus(201)
        ->assertJson([
            'data'   =>[
              'month' =>  2,
              'year'  =>  2,
              'target'=>  200,
            ]
          ])
        ->assertJsonStructureExact([
            'data'   => [
              'month',
              'year',
              'target',
              'user_id',
              'updated_at',
              'created_at',
              'id',
            ]
          ]);
    }
  
    /** @test */
    function list_of_targets()
    {
      $this->json('GET', '/api/users/' . $this->user->id .  '/targets',[], $this->headers)
        ->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
              0 =>  [
                'month',
              ] 
            ]
          ]);
      $this->assertCount(1, Target::all());
    }
  
    /** @test */
    function show_single_target()
    {
      $this->json('get', "/api/users/" . $this->user->id .  "/targets/1", [], $this->headers)
        ->assertStatus(200)
        ->assertJson([
            'data'  => [
              'month' =>  1
            ]
          ]);
    }
  
    /** @test */
    function update_single_target()
    {
      $payload = [ 
        'month' =>  3,
        'year'  =>  3,
        'target'=>  300,
      ];
  
      $this->json('patch', '/api/users/' . $this->user->id .  '/targets/1', $payload, $this->headers)
        ->assertStatus(200)
        ->assertJson([
            'data'    => [
              'month' =>  3,
              'year'  =>  3,
              'target'=>  300,
            ]
         ])
        ->assertJsonStructureExact([
            'data'  => [
              'id',
              'user_id',
              'month',
              'year',
              'target',
              'created_at',
              'updated_at',
              'company_id'
            ]
        ]);
    }
  }
