<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\UserFamilyDetail;

class UserFamilyDetailTest extends TestCase
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

    factory(UserFamilyDetail::class)->create([
      'user_id'  =>  $this->user->id 
    ]);

    $this->payload = [ 
      'name'  =>  'Name 2',
      'dob'   =>  'DOB 2',
      'gender'=>  'Gender 2',
      'relation'  =>  'Relation 2',
      'occupation'=>  'Occupation 2',
      'contact_number'  =>  'Contact Number 2'
    ];
  }

  /** @test */
  function user_must_be_logged_in_before_accessing_the_controller()
  {
    $this->json('post', '/api/users/' . $this->user->id . '/user_family_details')
      ->assertStatus(401); 
  }

  /** @test */
  function it_requires_following_details()
  {
    $this->json('post', '/api/users/' . $this->user->id . '/user_family_details', [], $this->headers)
      ->assertStatus(422)
      ->assertExactJson([
          "errors"  =>  [
            "name"            =>  ["The name field is required."],
            'dob'             =>  ["The dob field is required."],
            'gender'          =>  ["The gender field is required."],
            'relation'        =>  ["The relation field is required."],
            'occupation'      =>  ["The occupation field is required."],
            'contact_number'  =>  ["The contact number field is required."],
          ],
          "message" =>  "The given data was invalid."
        ]);
  }

  /** @test */
  function add_new_detail()
  {
    $this->disableEH();
    $this->json('post', '/api/users/' . $this->user->id . '/user_family_details', $this->payload, $this->headers)
      ->assertStatus(201)
      ->assertJson([
          'data'   =>[
            'name'  =>  'Name 2',
            'dob'   =>  'DOB 2',
            'gender'=>  'Gender 2',
            'relation'  =>  'Relation 2',
            'occupation'=>  'Occupation 2',
            'contact_number'  =>  'Contact Number 2'
          ]
        ])
      ->assertJsonStructureExact([
          'data'   => [
            'name',
            'dob',
            'gender',
            'relation',
            'occupation',
            'contact_number',
            'user_id',
            'updated_at',
            'created_at',
            'id',
          ]
        ]);
  }

  /** @test */
  function list_of_details()
  {
    $this->json('GET', '/api/users/' . $this->user->id . '/user_family_details',[], $this->headers)
      ->assertStatus(200)
      ->assertJsonStructure([
          'data' => [
            0 =>  [
              'name',
            ] 
          ]
        ]);
    $this->assertCount(1, UserFamilyDetail::all());
  }

  /** @test */
  function show_single_detail()
  {
    $this->json('get', "/api/users/" . $this->user->id . "/user_family_details/1", [], $this->headers)
      ->assertStatus(200)
      ->assertJson([
          'data'  => [
            'name'  =>  'Name 1',
          ]
        ]);
  }

  /** @test */
  function update_single_detail()
  {
    $payload = [ 
      'name'  =>  'Name 1 Updated',
      'dob'   =>  'DOB 1',
      'gender'=>  'Gender 1',
      'relation'  =>  'Relation 1',
      'occupation'=>  'Occupation 1',
      'contact_number'  =>  'Contact Number 1'
    ];

    $this->json('patch', '/api/users/' . $this->user->id . '/user_family_details/1', $payload, $this->headers)
      ->assertStatus(200)
      ->assertJson([
          'data'    => [
            'name'  =>  'Name 1 Updated',
          ]
       ])
      ->assertJsonStructureExact([
          'data'  => [
            'id',
            'user_id',
            'name',
            'dob',
            'gender',
            'relation',
            'occupation',
            'contact_number',
            'created_at',
            'updated_at',
          ]
      ]);
  }
}
