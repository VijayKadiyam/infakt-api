<?php

namespace Tests\Feature;

use App\Company;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Value;

class ValueTest extends TestCase
{
  use DatabaseTransactions;

  public function setUp()
  {
    parent::setUp();

    $this->company = factory(Company::class)->create();

    $this->user->assignRole(1);
    $this->user->assigncompany($this->company->id);
    $this->headers['company-id'] = $this->company->id;

    factory(Value::class)->create([
      'company_id' =>  $this->company->id
    ]);

    $this->payload = [
      'name'  =>  'Value 1',
    ];
  }

  /** @test */
  function it_requires_following_details()
  {
    $this->json('post', '/api/values', [], $this->headers)
      ->assertStatus(422)
      ->assertExactJson([
        "errors"  =>  [
          "name"        =>  ["The name field is required."],
        ],
        "message" =>  "The given data was invalid."
      ]);
  }

  /** @test */
  function add_new_value()
  {
    $this->json('post', '/api/values', $this->payload, $this->headers)
      ->assertStatus(201)
      ->assertJson([
        'data'   => [
          'name'  =>  'Value 1',
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
  function list_of_values()
  {
    $this->disableEH();
    $this->json('GET', '/api/values', [], $this->headers)
      ->assertStatus(200)
      ->assertJsonStructure([
        'data' => [
          0 =>  [
            'name'
          ]
        ]
      ]);
    $this->assertCount(1, Value::all());
  }

  /** @test */
  function show_single_value()
  {

    $this->json('get', "/api/values/1", [], $this->headers)
      ->assertStatus(200)
      ->assertJson([
        'data'  => [
          'name'  =>  'Value 1',
        ]
      ]);
  }

  /** @test */
  function update_single_value()
  {
    $payload = [
      'name'  =>  'Value 1 Updated',
    ];

    $this->json('patch', '/api/values/1', $payload, $this->headers)
      ->assertStatus(200)
      ->assertJson([
        'data'    => [
          'name'  =>  'Value 1 Updated',
        ]
      ])
      ->assertJsonStructureExact([
        'data'  => [
          'id',
          'company_id',
          'name',
          'created_at',
          'updated_at',
        ]
      ]);
  }

  /** @test */
  function delete_value()
  {
    $this->json('delete', '/api/values/1', [], $this->headers)
      ->assertStatus(204);

    $this->assertCount(0, Value::all());
  }
}
