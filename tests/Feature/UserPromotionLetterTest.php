<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\UserPromotionLetter;

class UserPromotionLetterTest extends TestCase
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

    factory(UserPromotionLetter::class)->create([
      'user_id'  =>  $this->user->id 
    ]);

    $this->payload = [ 
      'letter'          =>  'Letter 2',
    ];
  }

  /** @test */
  function user_must_be_logged_in_before_accessing_the_controller()
  {
    $this->json('post', '/api/users/' . $this->user->id . '/user_promotion_letters')
      ->assertStatus(401); 
  }

  /** @test */
  function it_requires_following_details()
  {
    $this->json('post', '/api/users/' . $this->user->id . '/user_promotion_letters', [], $this->headers)
      ->assertStatus(422)
      ->assertExactJson([
          "errors"  =>  [
            "letter"          =>  ["The letter field is required."],
          ],
          "message" =>  "The given data was invalid."
        ]);
  }

  /** @test */
  function add_new_letter()
  {
    $this->disableEH();
    $this->json('post', '/api/users/' . $this->user->id . '/user_promotion_letters', $this->payload, $this->headers)
      ->assertStatus(201)
      ->assertJson([
          'data'   =>[
            'letter'        =>  'Letter 2'
          ]
        ])
      ->assertJsonStructureExact([
          'data'   => [
            'letter',
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
    $this->json('GET', '/api/users/' . $this->user->id . '/user_promotion_letters',[], $this->headers)
      ->assertStatus(200)
      ->assertJsonStructure([
          'data' => [
            0 =>  [
              'letter',
            ] 
          ]
        ]);
    $this->assertCount(1, UserPromotionLetter::all());
  }

  /** @test */
  function show_single_letter()
  {
    $this->json('get', "/api/users/" . $this->user->id . "/user_promotion_letters/1", [], $this->headers)
      ->assertStatus(200)
      ->assertJson([
          'data'  => [
            'letter'        =>  'Letter 1',         
          ]
        ]);
  }

  /** @test */
  function update_single_letter()
  {
    $payload = [ 
      'letter'        =>  'Letter 1 Updated',
    ];

    $this->json('patch', '/api/users/' . $this->user->id . '/user_promotion_letters/1', $payload, $this->headers)
      ->assertStatus(200)
      ->assertJson([
          'data'    => [
            'letter'        =>  'Letter 1 Updated',
          ]
       ])
      ->assertJsonStructureExact([
          'data'  => [
            'id',
            'user_id',
            'letter',
            'signed',
            'sign_path',
            'created_at',
            'updated_at',
          ]
      ]);
  }
}
