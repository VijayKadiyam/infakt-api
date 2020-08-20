<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Feedback;

class FeedbackTest extends TestCase
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

    factory(\App\Feedback::class)->create([
      'company_id'  =>  $this->company->id 
    ]);

    $this->payload = [ 
      'description'     =>  'Feedback 2',
    ];
  }

  /** @test */
  function user_must_be_logged_in_before_accessing_the_controller()
  {
    $this->json('post', '/api/feedbacks')
      ->assertStatus(401); 
  }

  /** @test */
  function it_requires_following_details()
  {
    $this->json('post', '/api/feedbacks', [], $this->headers)
      ->assertStatus(422)
      ->assertExactJson([
          "errors"  =>  [
            "description"    =>  ["The description field is required."]
          ],
          "message" =>  "The given data was invalid."
        ]);
  }

  /** @test */
  function add_new_feedback()
  {
    $this->disableEH();
    $this->json('post', '/api/feedbacks', $this->payload, $this->headers)
      ->assertStatus(201)
      ->assertJson([
          'data'   =>[
            'description' => 'Feedback 2'
          ]
        ])
      ->assertJsonStructureExact([
          'data'   => [
            'description',
            'company_id',
            'updated_at',
            'created_at',
            'id'
          ],
          'success'
        ]);
  }

  /** @test */
  function list_of_feedbacks()
  {
    $this->json('GET', '/api/feedbacks',[], $this->headers)
      ->assertStatus(200)
      ->assertJsonStructure([
          'data' => [
            0=>[
              'description'
            ] 
          ]
        ]);
      $this->assertCount(1, Feedback::all());
  }

  /** @test */
  function show_single_feedback()
  {
    $this->disableEH();
    $this->json('get', "/api/feedbacks/1", [], $this->headers)
      ->assertStatus(200)
      ->assertJson([
          'data'  => [
            'description' => 'Feedback 1',
          ]
        ]);
  }

  /** @test */
  function update_single_feedback()
  {
    $this->disableEH();
    $payload = [ 
      'description'  =>  'Feedback Updated'
    ];

    $this->json('patch', '/api/feedbacks/1', $payload, $this->headers)
      ->assertStatus(200)
      ->assertJson([
          'data'    => [
            'description'  =>  'Feedback Updated',
          ]
       ])
      ->assertJsonStructureExact([
          'data'  => [
            'id',
            'company_id',
            'description',
            'created_at',
            'updated_at'
          ],
          'success'
      ]);
  }
}
