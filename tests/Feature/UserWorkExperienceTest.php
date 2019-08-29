<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\UserWorkExperience;

class UserWorkExperienceTest extends TestCase
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

    factory(UserWorkExperience::class)->create([
      'user_id'  =>  $this->user->id 
    ]);

    $this->payload = [ 
      'company_name'  =>  'Company Name 2',
      'from'          =>  'From 2',
      'to'            =>  'To 2',
      'designation'   =>  'Designation 2',
      'uan_no'        =>  'UAN NO 2',
      'esic_no'       =>  'ESIC No 2'
    ];
  }

  /** @test */
  function user_must_be_logged_in_before_accessing_the_controller()
  {
    $this->json('post', '/api/users/' . $this->user->id . '/user_work_experiences')
      ->assertStatus(401); 
  }

  /** @test */
  function it_requires_following_details()
  {
    $this->json('post', '/api/users/' . $this->user->id . '/user_work_experiences', [], $this->headers)
      ->assertStatus(422)
      ->assertExactJson([
          "errors"  =>  [
            "company_name"  =>  ["The company name field is required."],
            'from'          =>  ["The from field is required."],
            'to'            =>  ["The to field is required."],
            'designation'   =>  ["The designation field is required."],
            'uan_no'        =>  ["The uan no field is required."],
            'esic_no'       =>  ["The esic no field is required."],
          ],
          "message" =>  "The given data was invalid."
        ]);
  }

  /** @test */
  function add_new_exp()
  {
    $this->disableEH();
    $this->json('post', '/api/users/' . $this->user->id . '/user_work_experiences', $this->payload, $this->headers)
      ->assertStatus(201)
      ->assertJson([
          'data'   =>[
            'company_name'  =>  'Company Name 2',
            'from'          =>  'From 2',
            'to'            =>  'To 2',
            'designation'   =>  'Designation 2',
            'uan_no'        =>  'UAN NO 2',
            'esic_no'       =>  'ESIC No 2'
          ]
        ])
      ->assertJsonStructureExact([
          'data'   => [
            'company_name',
            'from',
            'to',
            'designation',
            'uan_no',
            'esic_no',
            'user_id',
            'updated_at',
            'created_at',
            'id',
          ]
        ]);
  }

  /** @test */
  function list_of_exps()
  {
    $this->json('GET', '/api/users/' . $this->user->id . '/user_work_experiences',[], $this->headers)
      ->assertStatus(200)
      ->assertJsonStructure([
          'data' => [
            0 =>  [
              'company_name',
            ] 
          ]
        ]);
    $this->assertCount(1, UserWorkExperience::all());
  }

  /** @test */
  function show_single_letter()
  {
    $this->json('get', "/api/users/" . $this->user->id . "/user_work_experiences/1", [], $this->headers)
      ->assertStatus(200)
      ->assertJson([
          'data'  => [
            'company_name'  =>  'Company Name 1',
          ]
        ]);
  }

  /** @test */
  function update_single_letter()
  {
    $payload = [ 
      'company_name'  =>  'Company Name 1 Updated',
      'from'          =>  'From 1',
      'to'            =>  'To 1',
      'designation'   =>  'Designation 1',
      'uan_no'        =>  'UAN NO 1',
      'esic_no'       =>  'ESIC No 1'
    ];

    $this->json('patch', '/api/users/' . $this->user->id . '/user_work_experiences/1', $payload, $this->headers)
      ->assertStatus(200)
      ->assertJson([
          'data'    => [
            'company_name'  =>  'Company Name 1 Updated',
          ]
       ])
      ->assertJsonStructureExact([
          'data'  => [
            'id',
            'user_id',
            'company_name',
            'from',
            'to',
            'designation',
            'uan_no',
            'esic_no',
            'created_at',
            'updated_at',
          ]
      ]);
  }

}
