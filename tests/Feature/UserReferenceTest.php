<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\UserReference;

class UserReferenceTest extends TestCase
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

    factory(UserReference::class)->create([
      'user_id'  =>  $this->user->id 
    ]);

    $this->payload = [ 
      'name'          =>  'Name 2',
      'company_name'  =>  'Company Name 2',
      'designation'   =>  'Designation 2',
      'contact_number'=>  'Contact Number 2'
    ];
  }

  /** @test */
  function user_must_be_logged_in_before_accessing_the_controller()
  {
    $this->json('post', '/api/users/' . $this->user->id . '/user_references')
      ->assertStatus(401); 
  }

  /** @test */
  function it_requires_following_details()
  {
    $this->json('post', '/api/users/' . $this->user->id . '/user_references', [], $this->headers)
      ->assertStatus(422)
      ->assertExactJson([
          "errors"  =>  [
            "name"            =>  ["The name field is required."],
            'company_name'    =>  ["The company name field is required."],
            'designation'     =>  ["The designation field is required."],
            'contact_number'  =>  ["The contact number field is required."],
          ],
          "message" =>  "The given data was invalid."
        ]);
  }

  /** @test */
  function add_new_reference()
  {
    $this->disableEH();
    $this->json('post', '/api/users/' . $this->user->id . '/user_references', $this->payload, $this->headers)
      ->assertStatus(201)
      ->assertJson([
          'data'   =>[
            'name'          =>  'Name 2',
            'company_name'  =>  'Company Name 2',
            'designation'   =>  'Designation 2',
            'contact_number'=>  'Contact Number 2'
          ]
        ])
      ->assertJsonStructureExact([
          'data'   => [
            'name',
            'company_name',
            'designation',
            'contact_number',
            'user_id',
            'updated_at',
            'created_at',
            'id',
          ],
          'success'
        ]);
  }

  /** @test */
  function list_of_references()
  {
    $this->json('GET', '/api/users/' . $this->user->id . '/user_references',[], $this->headers)
      ->assertStatus(200)
      ->assertJsonStructure([
          'data' => [
            0 =>  [
              'name',
            ] 
          ]
        ]);
    $this->assertCount(1, UserReference::all());
  }

  /** @test */
  function show_single_reference()
  {
    $this->json('get', "/api/users/" . $this->user->id . "/user_references/1", [], $this->headers)
      ->assertStatus(200)
      ->assertJson([
          'data'  => [
            'name'          =>  'Name 1',
          ]
        ]);
  }

  /** @test */
  function update_single_reference()
  {
    $payload = [ 
      'name'          =>  'Name 1 updated',
      'company_name'  =>  'Company Name 1',
      'designation'   =>  'Designation 1',
      'contact_number'=>  'Contact Number 1'
    ];

    $this->json('patch', '/api/users/' . $this->user->id . '/user_references/1', $payload, $this->headers)
      ->assertStatus(200)
      ->assertJson([
          'data'    => [
            'name'          =>  'Name 1 updated',
          ]
       ])
      ->assertJsonStructureExact([
          'data'  => [
            'id',
            'user_id',
            'name',
            'company_name',
            'designation',
            'contact_number',
            'created_at',
            'updated_at',
          ],
          'success'
      ]);
  }

  /** @test */
  function delete_single_reference()
  {
    $this->disableEH();
    $this->json('delete', "/api/users/" . $this->user->id . "/user_references/1", [], $this->headers);
  }
}
