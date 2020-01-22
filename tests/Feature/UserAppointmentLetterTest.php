<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\UserAppointmentLetter;

class UserAppointmentLetterTest extends TestCase
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

    factory(UserAppointmentLetter::class)->create([
      'user_id'  =>  $this->user->id 
    ]);

    $this->payload = [ 
      'letter'          =>  'Letter 2',
      'start_date'      =>  'Start Date 2',
      'end_date'        =>  'End Date 2',
      'stc_issue_date'  =>  'Stc Issue Date 2',
    ];
  }

  /** @test */
  function user_must_be_logged_in_before_accessing_the_controller()
  {
    $this->json('post', '/api/users/' . $this->user->id .  '/user_appointment_letters')
      ->assertStatus(401); 
  }

  /** @test */
  function it_requires_following_details()
  {
    $this->json('post', '/api/users/' . $this->user->id .  '/user_appointment_letters', [], $this->headers)
      ->assertStatus(422)
      ->assertExactJson([
          "errors"  =>  [
            "letter"          =>  ["The letter field is required."],
            "start_date"      =>  ["The start date field is required."],
            "end_date"        =>  ["The end date field is required."],
            "stc_issue_date"  =>  ["The stc issue date field is required."],
          ],
          "message" =>  "The given data was invalid."
        ]);
  }

  /** @test */
  function add_new_letter()
  {
    $this->disableEH();
    $this->json('post', '/api/users/' . $this->user->id .  '/user_appointment_letters', $this->payload, $this->headers)
      ->assertStatus(201)
      ->assertJson([
          'data'   =>[
            'letter'        =>  'Letter 2'
          ]
        ])
      ->assertJsonStructureExact([
          'data'   => [
            'letter',
            'start_date',
             'end_date', 
             'stc_issue_date',
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
    $this->json('GET', '/api/users/' . $this->user->id .  '/user_appointment_letters',[], $this->headers)
      ->assertStatus(200)
      ->assertJsonStructure([
          'data' => [
            0 =>  [
              'letter',
            ] 
          ]
        ]);
    $this->assertCount(1, UserAppointmentLetter::all());
  }

  /** @test */
  function list_of_letters_of_all_users()
  {
    $this->disableEH();
    $this->json('GET', '/api/user_appointment_letters',[], $this->headers)
      ->assertStatus(200)
      ->assertJsonStructure([
          'data' => [
            0 =>  [
              'letter',
            ] 
          ]
        ]);
  }


  /** @test */
  function show_single_letter()
  {
    $this->json('get', "/api/users/" . $this->user->id .  "/user_appointment_letters/1", [], $this->headers)
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

    $this->json('patch', '/api/users/' . $this->user->id .  '/user_appointment_letters/1', $payload, $this->headers)
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
            'start_date',
            'end_date',
            'stc_issue_date',
          ]
      ]);
  }
}
