<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Notice;

class NoticeTest extends TestCase
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

    factory(\App\Notice::class)->create([
      'company_id'  =>  $this->company->id 
    ]);

    $this->payload = [ 
      'name'  =>  'Name 2',
      'title' =>  'Title 2',
      'description' =>  'Description 2',
    ];
  }

  /** @test */
  function user_must_be_logged_in_before_accessing_the_controller()
  {
    $this->json('post', '/api/notices')
      ->assertStatus(401); 
  }

  /** @test */
  function it_requires_following_details()
  {
    $this->json('post', '/api/notices', [], $this->headers)
      ->assertStatus(422)
      ->assertExactJson([
          "errors"  =>  [
            "title"           =>  ["The title field is required."],
            "description"     =>  ["The description field is required."]
          ],
          "message" =>  "The given data was invalid."
        ]);
  }

  /** @test */
  function add_new_notice()
  {
    $this->disableEH();
    $this->json('post', '/api/notices', $this->payload, $this->headers)
      ->assertStatus(201)
      ->assertJson([
          'data'   =>[
            'name'  =>  'Name 2',
            'title' =>  'Title 2',
            'description' =>  'Description 2',
          ]
        ])
      ->assertJsonStructureExact([
          'data'   => [
            'name',
            'title',
            'description',
            'company_id',
            'updated_at',
            'created_at',
            'id'
          ]
        ]);
  }

  /** @test */
  function list_of_notices()
  {
    $this->json('GET', '/api/notices',[], $this->headers)
      ->assertStatus(200)
      ->assertJsonStructure([
          'data' => [
            0=>[
              'name'
            ] 
          ]
        ]);
      $this->assertCount(1, Notice::all());
  }

  /** @test */
  function show_single_notice()
  {
    $this->disableEH();
    $this->json('get', "/api/notices/1", [], $this->headers)
      ->assertStatus(200)
      ->assertJson([
          'data'  => [
            'name'  =>  'Name 1',
          ]
        ]);
  }

  /** @test */
  function update_single_notice()
  {
    $payload = [ 
      'name'  =>  'Name 2 Updated',
    ];

    $this->json('patch', '/api/notices/1', $payload, $this->headers)
      ->assertStatus(200)
      ->assertJson([
          'data'    => [
            'name'  =>  'Name 2 Updated',
          ]
       ])
      ->assertJsonStructureExact([
          'data'  => [
            'id',
            'company_id',
            'name',
            'title',
            'description',
            'imagepath',
            'created_at',
            'updated_at',
            'link'
          ]
      ]);
  }
}
