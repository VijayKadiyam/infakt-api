<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Notification;

class NotificationTest extends TestCase
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

    factory(Notification::class)->create([
      'user_id'  =>  $this->user->id 
    ]);

    $this->payload = [ 
      'notification'  =>  'Notification 2',
    ];
  }

  /** @test */
  function user_must_be_logged_in_before_accessing_the_controller()
  {
    $this->json('post', '/api/users/' . $this->user->id .  '/notifications')
      ->assertStatus(401); 
  }

  /** @test */
  function it_requires_following_details()
  {
    $this->json('post', '/api/users/' . $this->user->id .  '/notifications', [], $this->headers)
      ->assertStatus(422)
      ->assertExactJson([
          "errors"  =>  [
            "notification"  =>  ["The notification field is required."],
          ],
          "message" =>  "The given data was invalid."
        ]);
  }

  /** @test */
  function add_new_letter()
  {
    $this->disableEH();
    $this->json('post', '/api/users/' . $this->user->id .  '/notifications', $this->payload, $this->headers)
      ->assertStatus(201)
      ->assertJson([
          'data'   =>[
            'notification'  =>  'Notification 2'
          ]
        ])
      ->assertJsonStructureExact([
          'data'   => [
            'notification',
            'user_id',
            'updated_at',
            'created_at',
            'id',
          ]
        ]);
  }

  /** @test */
  function list_of_letters()
  {
    $this->json('GET', '/api/users/' . $this->user->id .  '/notifications',[], $this->headers)
      ->assertStatus(200)
      ->assertJsonStructure([
          'data' => [
            0 =>  [
              'notification',
            ] 
          ]
        ]);
    $this->assertCount(1, Notification::all());
  }

  /** @test */
  function show_single_letter()
  {
    $this->json('get', "/api/users/" . $this->user->id .  "/notifications/1", [], $this->headers)
      ->assertStatus(200)
      ->assertJson([
          'data'  => [
            'notification'  =>  'Notification 1',         
          ]
        ]);
  }

  /** @test */
  function update_single_letter()
  {
    $payload = [ 
      'notification'        =>  'notification 1 Updated',
    ];

    $this->json('patch', '/api/users/' . $this->user->id .  '/notifications/1', $payload, $this->headers)
      ->assertStatus(200)
      ->assertJson([
          'data'    => [
            'notification'        =>  'notification 1 Updated',
          ]
       ])
      ->assertJsonStructureExact([
          'data'  => [
            'id',
            'user_id',
            'notification',
            'status',
            'created_at',
            'updated_at',
          ]
      ]);
  }
}
