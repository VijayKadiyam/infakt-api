<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Holiday;

class HolidayTest extends TestCase
{
  use DatabaseTransactions;
  
  public function setUp()
  {
    parent::setUp();

    $this->payload = [ 
      'name'  =>  'Republic Day',
      'date'  =>  \Carbon\Carbon::now()->format('d-m-Y')
    ];
  }

  /** @test */
  function it_requires_holiday_name_and_date()
  {
    $this->json('post', '/api/holidays', [])
      ->assertStatus(422)
      ->assertExactJson([
          "errors"  =>  [
            "name"  =>  ["The name field is required."],
            "date"  =>  ["The date field is required."]
          ],
          "message" =>  "The given data was invalid."
        ]);
  }

  /** @test */
  function add_new_holiday()
  {
    $this->json('post', '/api/holidays', $this->payload)
      ->assertStatus(201)
      ->assertJson([
          'data'  =>  [
            'name'  =>  'Republic Day',
            'date'  =>  \Carbon\Carbon::now()->format('d-m-Y')
          ]
        ])
      ->assertJsonStructureExact([
          'data'  =>  [
            'name',
            'date',
            'updated_at',
            'created_at',
            'id'
          ]
        ]);
  }

  /** @test */
  function list_of_holidays()
  {
    $this->json('GET', '/api/holidays', [])
      ->assertStatus(200)
      ->assertJsonStructure([
          'data'  =>  [
            0 =>  [
              'name',
              'date'
            ] 
          ]
      ]);
    $this->assertCount(1, Holiday::all());
  }

  /** @test */
  function show_single_holiday()
  {
    $this->json('get', "/api/holidays/1", [])
      ->assertStatus(200)
      ->assertJson([
          'data'  => [
            'name'=> 'Happy New Year',
          ]
        ])
      ->assertJsonStructureExact([
          'data'    => [
            'id',
            'name',
            'date',
            'created_at',
            'updated_at'
          ]
        ]);
  }

  /** @test */
  function update_single_holiday()
  {
    $payload = [ 
      'name'  =>  'Republic'
    ];

    $this->json('patch', '/api/holidays/1', $payload)
      ->assertStatus(200)
      ->assertJson([
          'data'    => [
            'name'  =>  'Republic',
          ]
        ])
      ->assertJsonStructureExact([
          'data'    => [
            'id',
            'name',
            'date',
            'created_at',
            'updated_at'
          ]
        ]);
  }
}
