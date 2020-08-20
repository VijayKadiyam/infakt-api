<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Role;

class RoleTest extends TestCase
{
  use DatabaseTransactions;
  
  public function setUp()
  {
    parent::setUp();

    $this->payload = [ 
      'name'     =>  'test role',
    ];
  }

  /** @test */
  function it_requires_role_name()
  {
    $this->json('post', '/api/roles', [])
      ->assertStatus(422)
      ->assertExactJson([
          "errors"  =>  [
            "name"  =>  ["The name field is required."]
          ],
          "message" =>  "The given data was invalid."
        ]);
  }

  /** @test */
  function add_new_role()
  {
    $this->json('post', '/api/roles', $this->payload)
      ->assertStatus(201)
      ->assertJson([
          'data'  =>  [
            'name' => 'test role'
          ]
        ])
      ->assertJsonStructureExact([
          'data'  =>  [
            'name',
            'updated_at',
            'created_at',
            'id'
          ]
        ]);
  }

  /** @test */
  function list_of_roles()
  {
    $this->json('GET', '/api/roles', [])
      ->assertStatus(200)
      ->assertJsonStructure([
          'data'  =>  [
            0 =>  [
              'name'
            ] 
          ]
      ]);
    $this->assertCount(4, Role::all());
  }

  /** @test */
  function show_single_role()
  {
    $this->json('get', "/api/roles/2", [])
      ->assertStatus(200)
      ->assertJson([
          'data'  => [
            'name'=> 'Admin',
          ]
        ])
      ->assertJsonStructureExact([
          'data'    => [
            'id',
            'name',
            'created_at',
            'updated_at'
          ]
        ]);
  }

  /** @test */
  function update_single_role()
  {
    $payload = [ 
      'name'  =>  'manager'
    ];

    $this->json('patch', '/api/roles/1', $payload)
      ->assertStatus(200)
      ->assertJson([
          'data'    => [
            'name'  =>  'manager',
          ]
        ])
      ->assertJsonStructureExact([
          'data'    => [
            'id',
            'name',
            'created_at',
            'updated_at'
          ]
        ]);
  }
}
