<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Mark;

class MarkTest extends TestCase
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

    factory(\App\Mark::class)->create([
      'user_id'  =>  $this->user->id 
    ]);

    $this->date = (\Carbon\Carbon::now()->format('Y-m-d'));
    $this->toDate = (\Carbon\Carbon::now()->addDay()->format('Y-m-d'));
    $this->payload = [ 
      'in_lat'   =>  '23.34',
      'in_lng'   =>  '23.34',
      'out_lat'  =>  '34.34',
      'out_lng'  =>  '34.34'
    ];
  }

  /** @test */
  function user_must_be_logged_in_before_accessing_the_controller()
  {
    $this->json('post', '/api/marks')
      ->assertStatus(401); 
  }

  /** @test */
  function it_requires_following_details()
  {
    $this->json('post', '/api/marks', [], $this->headers)
      ->assertStatus(422)
      ->assertExactJson([
          "errors"  =>  [
            "in_lat"   =>  ["The in lat field is required."],
            "in_lng"   =>  ["The in lng field is required."],
          ],
          "message" =>  "The given data was invalid."
        ]);
  }

  /** @test */
  function add_new_mark()
  {
    $this->disableEH();
    $this->json('post', '/api/marks', $this->payload, $this->headers)
      ->assertStatus(201)
      ->assertJson([
          'data'   =>[
            'in_lat'   =>  '23.34',
            'in_lng'   =>  '23.34',        ]
        ])
      ->assertJsonStructureExact([
          'data'   => [
            'in_lat',
            'in_lng',
            'out_lat',
            'out_lng',
            'user_id',
            'updated_at',
            'created_at',
            'id'
          ],
          'success'
        ]);
  }

  /** @test */
  function list_of_marks()
  {
    $this->disableEH();

    $this->json('GET', '/api/marks',[], $this->headers)
      ->assertStatus(200)
      ->assertJsonStructure([
          'data' => [
            0 =>  [
              'in_lat',
              'in_lng',
              'out_lat',
              'out_lng'
            ] 
          ]
        ]);
    $this->assertCount(1, Mark::all());
  }

  /** @test */
  function list_of_marks_of_specific_dat()
  {
    $this->disableEH();
    $this->json('GET', "/api/marks?date=" . $this->date . "&user_id=" . $this->user->id,[], $this->headers)
      ->assertStatus(200)
      ->assertJsonStructure([
          'data' => [
            0 =>  [
              'in_lat',
              'in_lng',
              'out_lat',
              'out_lng'
            ]
          ]
        ]);
    $this->assertCount(1, Mark::all());
  }

  /** @test */
  function show_single_mark()
  {
    $this->json('get', "/api/marks/1", [], $this->headers)
      ->assertStatus(200)
      ->assertJson([
          'data'  => [
            'in_lat'   =>  '23.34',
            'in_lng'   =>  '23.34',
            'out_lat'  =>  '34.34',
            'out_lng'  =>  '34.34'          ]
        ]);
  }

  /** @test */
  function update_single_mark()
  {
    $payload = [ 
      'in_lat'   =>  '23.34',
      'in_lng'   =>  '23.34',
      'out_lat'  =>  '34.34',
      'out_lng'  =>  '34.34'    
    ];

    $this->json('patch', '/api/marks/1', $payload, $this->headers)
      ->assertStatus(200)
      ->assertJson([
          'data'    => [
            'in_lat'   =>  '23.34',
            'in_lng'   =>  '23.34',
            'out_lat'  =>  '34.34',
            'out_lng'  =>  '34.34'   
          ]
       ])
      ->assertJsonStructureExact([
          'data'  => [
            'id',
            'user_id',
            'in_lat',
            'in_lng',
            'out_lat',
            'out_lng',
            'created_at',
            'updated_at',
          ],
          'success'
      ]);
  }
  
}
