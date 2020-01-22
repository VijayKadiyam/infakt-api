<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Version;

class VersionTest extends TestCase
{
  use DatabaseTransactions;
  
  public function setUp()
  {
    parent::setUp();

    factory(Version::class)->create();

    $this->payload = [ 
      'version'     =>  '1.1',
    ];
  }

  /** @test */
  function it_requires_role_name()
  {
    $this->json('post', '/api/versions', [])
      ->assertStatus(422)
      ->assertExactJson([
          "errors"  =>  [
            "version"  =>  ["The version field is required."]
          ],
          "message" =>  "The given data was invalid."
        ]);
  }

  /** @test */
  function add_new_role()
  {
    $this->json('post', '/api/versions', $this->payload)
      ->assertStatus(201)
      ->assertJson([
          'data'  =>  [
            'version' => '1.1'
          ]
        ])
      ->assertJsonStructureExact([
          'data'  =>  [
            'version',
            'updated_at',
            'created_at',
            'id'
          ]
        ]);
  }

  /** @test */
  function get_latest_version()
  {
    $this->disableEH();
    $this->json('GET', '/api/versions?search=latest', [])
      ->assertStatus(200)
      ->assertJsonStructure([
          'data'  =>  [
            'version'
          ]
      ]);
    // $this->assertCount(1, Version::all());
  }
}
