<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\LeavePattern;

class LeavePatternTest extends TestCase
{
  use DatabaseTransactions;
  
  public function setUp()
  {
    parent::setUp();

    $this->payload = [ 
      'name'     =>  'Pattern 1',
    ];
  }

  /** @test */
  function it_requires_leave_pattern_name()
  {
    $this->json('post', '/api/leave_patterns', [])
      ->assertStatus(422)
      ->assertExactJson([
          "errors"  =>  [
            "name"  =>  ["The name field is required."]
          ],
          "message" =>  "The given data was invalid."
        ]);
  }

  /** @test */
  function add_new_leave_pattern()
  {
    $this->json('post', '/api/leave_patterns', $this->payload)
      ->assertStatus(201)
      ->assertJson([
          'data'  =>  [
            'name' => 'Pattern 1'
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
  function list_of_leave_patterns()
  {
    $this->json('GET', '/api/leave_patterns', [])
      ->assertStatus(200)
      ->assertJsonStructure([
          'data'  =>  [
            0 =>  [
              'name'
            ] 
          ]
      ]);
    $this->assertCount(2, LeavePattern::all());
  }

  /** @test */
  function show_single_role()
  {
    $this->json('get', "/api/leave_patterns/2", [])
      ->assertStatus(200)
      ->assertJson([
          'data'  => [
            'name'=> 'Pattern 2',
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
      'name'  =>  'Pattern x'
    ];

    $this->json('patch', '/api/leave_patterns/1', $payload)
      ->assertStatus(200)
      ->assertJson([
          'data'    => [
            'name'  =>  'Pattern x',
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
