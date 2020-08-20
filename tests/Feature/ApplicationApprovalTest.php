<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\ApplicationApproval;

class ApplicationApprovalTest extends TestCase
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

    $this->companyLeave = factory(\App\CompanyLeave::class)->create([
      'company_id'  =>  $this->company->id
    ]);

    $this->userApplication = factory(\App\UserApplication::class)->create([
      'user_id'           =>  $this->user->id,
      'company_leave_id'  =>  $this->companyLeave->id
    ]);

    $this->supervisor  = factory(\App\User::class)->create();
    $this->user->assignSupervisor($this->supervisor->id);

    $applicationApproval = new ApplicationApproval([
      'supervisor_id'       =>  $this->supervisor->id,
      'status'              =>  0
    ]);
    $this->userApplication->application_approvals()->save($applicationApproval);

    $this->payload = [ 
      'supervisor_id' =>  $this->supervisor->id,
      'status'        =>  1
    ];
  }

  /** @test */
  function user_must_be_logged_in_before_accessing_the_controller()
  {
    $this->json('post', '/api/user_applications/' . $this->userApplication->id . '/application_approvals')
      ->assertStatus(401); 
  }

  /** @test */
  function it_requires_following_details()
  {
    $this->json('post', '/api/user_applications/' . $this->userApplication->id . '/application_approvals', [], $this->headers)
      ->assertStatus(422)
      ->assertExactJson([
          "errors"  =>  [
            "supervisor_id"  =>  ["The supervisor id field is required."],
            "status"         =>  ["The status field is required."],
          ],
          "message" =>  "The given data was invalid."
        ]);
  }

  /** @test */
  function add_new_application_approval()
  {
    $this->disableEH();
    $this->json('post', '/api/user_applications/' . $this->userApplication->id . '/application_approvals', $this->payload, $this->headers)
      ->assertStatus(201)
      ->assertJson([
          'data'  => [
            'supervisor_id' =>  $this->supervisor->id, 
            'status'        =>  1
          ]
        ])
      ->assertJsonStructureExact([
          'data'   => [
            'id',
            'user_application_id',
            'supervisor_id',
            'status',
            'created_at',
            'updated_at',
          ]
        ]);
  }

  /** @test */
  function list_of_application_approvals()
  {
    $this->json('GET', '/api/user_applications/' . $this->userApplication->id . '/application_approvals',[], $this->headers)
      ->assertStatus(200)
      ->assertJsonStructure([
          'data' => [
            0 =>  [
              'user_application_id',
              'supervisor_id',
              'status'
            ] 
          ]
        ]);
  }

  /** @test */
  function show_single_application_approvals()
  {
    $this->json('get', '/api/user_applications/' . $this->userApplication->id . '/application_approvals/1', [], $this->headers)
      ->assertStatus(200)
      ->assertJson([
          'data'  => [
            'supervisor_id'       =>  $this->supervisor->id,
            'status'              =>  0
          ]
        ]);
  }

  /** @test */
  function update_single_user_attendance()
  {
    $payload = [ 
      'supervisor_id'       =>  $this->supervisor->id,
      'status'              =>  1
    ];

    $this->json('patch', '/api/user_applications/' . $this->userApplication->id . '/application_approvals/1', $payload, $this->headers)
      ->assertStatus(200)
      ->assertJson([
          'data'    => [
            'supervisor_id'       =>  $this->supervisor->id,
            'status'              =>  1 
          ]
       ])
      ->assertJsonStructureExact([
          'data'  => [
            'id',
            'user_application_id',
            'supervisor_id',
            'status',
            'created_at',
            'updated_at',
          ]
      ]);
  }
}
