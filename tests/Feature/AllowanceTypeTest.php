<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\AllowanceType;

class AllowanceTypeTest extends TestCase
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

    factory(AllowanceType::class)->create([
      'company_id'  =>  $this->company->id 
    ]);

    $this->payload = [ 
      'name'     => 'HO 1',
      'amount'   => '100'
    ];
  }

  /** @test */
  function user_must_be_logged_in_before_accessing_the_controller()
  {
    $this->json('post', '/api/allowance_types')
      ->assertStatus(401); 
  }

  /** @test */
  function it_requires_following_details()
  {
    $this->json('post', '/api/allowance_types', [], $this->headers)
      ->assertStatus(422)
      ->assertExactJson([
          "errors"  =>  [
            "name"    =>  ["The name field is required."],
            'amount'  =>  ["The amount field is required."],
          ],
          "message" =>  "The given data was invalid."
        ]);
  }

  /** @test */
  function add_new_allowance_type()
  {
    $this->disableEH();
    $this->json('post', '/api/allowance_types', $this->payload, $this->headers)
      ->assertStatus(201)
      ->assertJson([
          'data'   =>[
            'name' => 'HO 1'
          ]
        ])
      ->assertJsonStructureExact([
          'data'   => [
            'name',
            'amount',
            'company_id',
            'updated_at',
            'created_at',
            'id',
          ]
        ]);
  }

  /** @test */
  function list_of_allowance_types()
  {
    $this->json('GET', '/api/allowance_types',[], $this->headers)
      ->assertStatus(200)
      ->assertJsonStructure([
          'data' => [
            0=>[
              'name'
            ] 
          ]
        ]);
      $this->assertCount(1, AllowanceType::all());
  }

  /** @test */
  function show_single_allowance_type()
  {
    $this->disableEH();
    $this->json('get', "/api/allowance_types/1", [], $this->headers)
      ->assertStatus(200)
      ->assertJson([
          'data'  => [
            'name'  => 'HO',
          ]
        ]);
  }

  /** @test */
  function update_single_company_designation()
  {
    $this->disableEH();
    $payload = [ 
      'name'  =>  'HO 2'
    ];

    $this->json('patch', '/api/allowance_types/1', $payload, $this->headers)
      ->assertStatus(200)
      ->assertJson([
          'data'    => [
            'name'  =>  'HO 2',
          ]
       ])
      ->assertJsonStructureExact([
          'data'  => [
            'id',
            'company_id',
            'name',
            'amount',
            'created_at',
            'updated_at'
          ]
      ]);
  }
}
