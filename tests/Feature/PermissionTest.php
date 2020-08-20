<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Permission; 

class PermissionTest extends TestCase
{
  use DatabaseTransactions;
  
  public function setUp()
  {
    parent::setUp();

    $this->payload = [ 
      'name'     =>  'Settings 1',
    ];
  }

  /** @test */
  function user_must_be_logged_in_before_accessing_the_controller()
  {
    $this->json('post', '/api/permissions')
        ->assertStatus(401); 
  }

  /** @test */
  function it_requires_permission_name()
  {
    $this->json('post', '/api/permissions', [], $this->headers)
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
    $this->json('post', '/api/permissions', $this->payload, $this->headers)
      ->assertStatus(201)
      ->assertJson([
          'data'  =>  [
            'name' => 'Settings 1'
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
  function list_of_permissions()
  {
    $this->json('GET', '/api/permissions', [], $this->headers)
      ->assertStatus(200)
      ->assertJsonStructure([
          'data'  =>  [
            0 =>  [
              'name'
            ] 
          ]
      ]);
    $this->assertCount(26, Permission::all());
  }

  /** @test */
  function show_single_permission()
  {
    $this->json('get', "/api/permissions/1", [], $this->headers)
      ->assertStatus(200)
      ->assertJson([
          'data'  => [
            'name'=> 'Settings',
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
  function update_single_permission()
  {
    $payload = [ 
      'name'  =>  'Settings 2'
    ];

    $this->json('patch', '/api/permissions/1', $payload, $this->headers)
      ->assertStatus(200)
      ->assertJson([
          'data'    => [
            'name'  =>  'Settings 2',
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
