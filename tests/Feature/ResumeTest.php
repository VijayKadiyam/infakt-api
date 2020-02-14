<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Resume;

class ResumeTest extends TestCase
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

    factory(Resume::class)->create([
      'company_id'  =>  $this->company->id,
      'user_id' =>  $this->user->id
    ]);

    $this->payload = [ 
      'user_id' =>  $this->user->id,
      'name'    =>  'Name 1',
      'mobile_1'  =>  'Mobile 2'
    ];
  }

  /** @test */
  function user_must_be_logged_in_before_accessing_the_controller()
  {
    $this->json('post', '/api/resumes')
      ->assertStatus(401); 
  }

  /** @test */
  function it_requires_following_details()
  {
    $this->json('post', '/api/resumes', [], $this->headers)
      ->assertStatus(422)
      ->assertExactJson([
          "errors"  =>  [
            "user_id"   =>  ["The user id field is required."],
            "name"      =>  ["The name field is required."],
            "mobile_1"  =>  ["The mobile 1 field is required."],
          ],
          "message" =>  "The given data was invalid."
        ]);
  }

  /** @test */
  function add_new_sku_type()
  {
    $this->json('post', '/api/resumes', $this->payload, $this->headers)
      ->assertStatus(201)
      ->assertJson([
          'data'   =>[
            'user_id' =>  $this->user->id
          ]
        ])
      ->assertJsonStructureExact([
          'data'   => [
            'user_id',
            'name',
            'mobile_1',
            'company_id',
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
    $this->json('GET', '/api/resumes',[], $this->headers)
      ->assertStatus(200)
      ->assertJsonStructure([
          'data' => [
            0=>[
              'name'
            ] 
          ]
        ]);
      $this->assertCount(1, Resume::all());
  }

  /** @test */
  public function list_of_inquiries_of_search()
  {
    $resume = factory(Resume::class)->create([
      'company_id'  =>  $this->company->id,
      'user_id'     =>  $this->user->id
    ]);

    $this->json('get', '/api/resumes?search=' . $resume->name, [], $this->headers)
      ->assertStatus(200)
      ->assertJsonStructure([
          'data' => [
            0 =>  [
              'name'
            ]
          ]
        ]);
  }

  /** @test */
  function show_single_inquiry()
  {
    $this->json('get', "/api/resumes/1", [], $this->headers)
      ->assertStatus(200)
      ->assertJson([
          'data'  => [
            'name'=> 'Name 1',
          ]
        ]);
  }

  /** @test */
  function update_single_inquiry()
  {
    $payload = [ 
      'name'=> 'Name 1 Updated',
    ];

    $this->json('patch', '/api/resumes/1', $payload, $this->headers)
      ->assertStatus(200)
      ->assertJson([
          'data'    => [
            'name'=> 'Name 1 Updated',
          ]
       ])
      ->assertJsonStructureExact([
          'data'  => [
            'id',
            'company_id',
            'user_id','name', 'gender', 'mobile_1', 'mobile_2', 'present_company_name', 'designation', 'work_experience', 'current_salary', 'location', 'lat', 'lng' ,
            'created_at',
            'updated_at'
          ]
      ]);
  }
}
