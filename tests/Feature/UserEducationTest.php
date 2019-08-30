<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\UserEducation;

class UserEducationTest extends TestCase
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

    factory(UserEducation::class)->create([
      'user_id'  =>  $this->user->id 
    ]);

    $this->payload = [ 
      'examination' =>  'Examination 2',
      'school'      =>  'School 2',
      'passing_year'=>  'Passing Year 2',
      'percent'     =>  'Percent 2'
    ];
  }

  /** @test */
  function user_must_be_logged_in_before_accessing_the_controller()
  {
    $this->json('post', '/api/users/' . $this->user->id . '/user_educations')
      ->assertStatus(401); 
  }

  /** @test */
  function it_requires_following_details()
  {
    $this->json('post', '/api/users/' . $this->user->id . '/user_educations', [], $this->headers)
      ->assertStatus(422)
      ->assertExactJson([
          "errors"  =>  [
            "examination"   =>  ["The examination field is required."],
            'school'        =>  ["The school field is required."],
            'passing_year'  =>  ["The passing year field is required."],
            'percent'       =>  ["The percent field is required."],
          ],
          "message" =>  "The given data was invalid."
        ]);
  }

  /** @test */
  function add_new_education()
  {
    $this->disableEH();
    $this->json('post', '/api/users/' . $this->user->id . '/user_educations', $this->payload, $this->headers)
      ->assertStatus(201)
      ->assertJson([
          'data'   =>[
            'examination' =>  'Examination 2',
            'school'      =>  'School 2',
            'passing_year'=>  'Passing Year 2',
            'percent'     =>  'Percent 2'
          ]
        ])
      ->assertJsonStructureExact([
          'data'   => [
            'examination',
            'school',
            'passing_year',
            'percent',
            'user_id',
            'updated_at',
            'created_at',
            'id',
          ],
          'success'
        ]);
  }

  /** @test */
  function list_of_educations()
  {
    $this->json('GET', '/api/users/' . $this->user->id . '/user_educations',[], $this->headers)
      ->assertStatus(200)
      ->assertJsonStructure([
          'data' => [
            0 =>  [
              'examination',
            ] 
          ]
        ]);
    $this->assertCount(1, UserEducation::all());
  }

  /** @test */
  function show_single_education()
  {
    $this->json('get', "/api/users/" . $this->user->id . "/user_educations/1", [], $this->headers)
      ->assertStatus(200)
      ->assertJson([
          'data'  => [
            'examination' =>  'Examination 1',
          ]
        ]);
  }

  /** @test */
  function update_single_letter()
  {
    $payload = [ 
      'examination' =>  'Examination 1 Updated',
      'school'      =>  'School 1',
      'passing_year'=>  'Passing Year 1',
      'percent'     =>  'Percent 1'
    ];

    $this->json('patch', '/api/users/' . $this->user->id . '/user_educations/1', $payload, $this->headers)
      ->assertStatus(200)
      ->assertJson([
          'data'    => [
            'examination' =>  'Examination 1 Updated',
          ]
       ])
      ->assertJsonStructureExact([
          'data'  => [
            'id',
            'user_id',
            'examination',
            'school',
            'passing_year',
            'percent',
            'created_at',
            'updated_at',
          ],
          'success'
      ]);
  }

}
