<?php

namespace Tests\Feature;

use App\ChannelFilter;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ChannelFilterTest extends TestCase
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

    factory(ChannelFilter::class)->create([
      'company_id'  =>  $this->company->id
    ]);

    $this->payload = [
      'name'     =>  'Name 1',
    ];
  }

  /** @test */
  function it_requires_following_details()
  {
    $this->json('post', '/api/channel_filters', [], $this->headers)
      ->assertStatus(422)
      ->assertExactJson([
        "errors"  =>  [
          "name"    =>  ["The name field is required."]
        ],
        "message" =>  "The given data was invalid."
      ]);
  }

  /** @test */
  function add_new_channel_filter()
  {
    $this->json('post', '/api/channel_filters', $this->payload, $this->headers)
      ->assertStatus(201)
      ->assertJson([
        'data'   => [
          'name' => 'Name 1'
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
  function list_of_channel_filters()
  {
    $this->json('GET', '/api/channel_filters', [], $this->headers)
      ->assertStatus(200)
      ->assertJsonStructure([
        'data' => [
          0 => [
            'name'
          ]
        ]
      ]);
    $this->assertCount(1, ChannelFilter::all());
  }

  /** @test */
  function show_single_channel_filter()
  {
    $this->json('get', "/api/channel_filters/1", [], $this->headers)
      ->assertStatus(200)
      ->assertJson([
        'data'  => [
          'name' => 'Name 1',
        ]
      ]);
  }

  /** @test */
  function update_single_channel_filter()
  {
    $payload = [
      'name'  =>  'Name 2'
    ];

    $this->json('patch', '/api/channel_filters/1', $payload, $this->headers)
      ->assertStatus(200)
      ->assertJson([
        'data'    => [
          'name'  =>  'Name 2',
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
