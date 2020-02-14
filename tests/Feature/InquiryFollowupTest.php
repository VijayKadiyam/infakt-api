<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Inquiry;
use App\InquiryFollowup;

class InquiryFollowupTest extends TestCase
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

    $this->inquiry = factory(Inquiry::class)->create([
      'company_id'  =>  $this->company->id 
    ]);

    factory(InquiryFollowup::class)->create([
      'inquiry_id'  =>  $this->inquiry->id,
      'user_id' =>  $this->user->id
    ]);

    $this->payload = [ 
      'user_id' =>  $this->user->id
    ];
  }

  /** @test */
  function user_must_be_logged_in_before_accessing_the_controller()
  {
    $this->json('post', '/api/inquiries/' . $this->inquiry->id . '/inquiry_followups')
      ->assertStatus(401); 
  }

  /** @test */
  function it_requires_following_details()
  {
    $this->json('post', '/api/inquiries/' . $this->inquiry->id . '/inquiry_followups', [], $this->headers)
      ->assertStatus(422)
      ->assertExactJson([
          "errors"  =>  [
            "user_id"          =>  ["The user id field is required."],
          ],
          "message" =>  "The given data was invalid."
        ]);
  }

  /** @test */
  function add_new_sku_type()
  {
    $this->json('post', '/api/inquiries/' . $this->inquiry->id . '/inquiry_followups', $this->payload, $this->headers)
      ->assertStatus(201)
      ->assertJson([
          'data'   =>[
            'user_id' => $this->user->id
          ]
        ])
      ->assertJsonStructureExact([
          'data'   => [
            'user_id',
            'inquiry_id',
            'updated_at',
            'created_at',
            'id'
          ]
        ]);
  }

  /** @test */
  function list_of_inquirys()
  {
    $this->disableEH();
    $this->json('GET', '/api/inquiries/' . $this->inquiry->id . '/inquiry_followups',[], $this->headers)
      ->assertStatus(200)
      ->assertJsonStructure([
          'data' => [
            0=>[
              'user_id'
            ] 
          ]
        ]);
      $this->assertCount(1, InquiryFollowup::all());
  }

  /** @test */
  function show_single_inquiry()
  {
    $this->json('get', '/api/inquiries/' . $this->inquiry->id . '/inquiry_followups/1', [], $this->headers)
      ->assertStatus(200)
      ->assertJson([
          'data'  => [
            'user_id' => $this->user->id
          ]
        ]);
  }

  /** @test */
  function update_single_inquiry()
  {
    $payload = [ 
      'date'  =>  'GRAM 1'
    ];

    $this->json('patch', '/api/inquiries/' . $this->inquiry->id . '/inquiry_followups/1', $payload, $this->headers)
      ->assertStatus(200)
      ->assertJson([
          'data'    => [
            'date'  =>  'GRAM 1',
          ]
       ])
      ->assertJsonStructureExact([
          'data'  => [
            'id',
            'inquiry_id',
            'user_id', 'date', 'status', 'description', 
            'created_at',
            'updated_at'
          ]
      ]);
  }
}
