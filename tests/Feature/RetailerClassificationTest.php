<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\RetailerClassification;

class RetailerClassificationTest extends TestCase
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

    factory(RetailerClassification::class)->create([
      'company_id'  =>  $this->company->id 
    ]);

    $this->payload = [ 
      'name'     =>  'Class 2',
    ];
  }

  /** @test */
  function user_must_be_logged_in_before_accessing_the_controller()
  {
    $this->json('post', '/api/retailer_classifications')
      ->assertStatus(401); 
  }

   /** @test */
  function it_requires_following_details()
  {
    $this->json('post', '/api/retailer_classifications', [], $this->headers)
      ->assertStatus(422)
      ->assertExactJson([
          "errors"  =>  [
            "name"    =>  ["The name field is required."]
          ],
          "message" =>  "The given data was invalid."
        ]);
  }

  /** @test */
  function add_new_classification()
  {
    $this->disableEH();
    $this->json('post', '/api/retailer_classifications', $this->payload, $this->headers)
      ->assertStatus(201)
      ->assertJson([
          'data'   =>[
            'name' => 'Class 2'
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
  function list_of_classifications()
  {
    $this->disableEH();
    $this->json('GET', '/api/retailer_classifications',[], $this->headers)
      ->assertStatus(200)
      ->assertJsonStructure([
          'data' => [
            0=>[
              'name'
            ] 
          ]
        ]);
      $this->assertCount(1, RetailerClassification::all());
  }

  /** @test */
  function show_single_classification()
  {
    $this->json('get', "/api/retailer_classifications/1", [], $this->headers)
      ->assertStatus(200)
      ->assertJson([
          'data'  => [
            'name'=> 'Class 1',
          ]
        ]);
  }

  /** @test */
  function update_single_classification()
  {
    $payload = [ 
      'name'  =>  'Class 1 updated'
    ];

    $this->json('patch', '/api/retailer_classifications/1', $payload, $this->headers)
      ->assertStatus(200)
      ->assertJson([
          'data'    => [
            'name'  =>  'Class 1 updated',
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
