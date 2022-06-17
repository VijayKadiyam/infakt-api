<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\UserApplication;

class UserApplicationTest extends TestCase
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

    $userTwo  = factory(\App\User::class)->create();
    $userTwo->assignSupervisor($this->user->id);

    $this->companyLeave = factory(\App\CompanyLeave::class)->create([
      'company_id'  =>  $this->company->id
    ]);

    $this->leaveType = factory(\App\LeaveType::class)->create([
      'company_id'  =>  $this->company->id
    ]);

    factory(\App\UserApplication::class)->create([
      'user_id'           =>  $this->user->id,
      'company_leave_id'  =>  $this->companyLeave->id
    ]);

    factory(\App\UserApplication::class)->create([
      'user_id'           =>  $userTwo->id,
      'company_leave_id'  =>  $this->companyLeave->id
    ]);

    $this->payload = [ 
      'company_leave_id'  =>  $this->companyLeave->id,
      'from_date'         =>  '02-02-2019',
      'to_date'           =>  '05-02-2019',
      'description'       =>  'Not Well',
      'leave_type_id'     =>  $this->leaveType->id
    ];
  }

  /** @test */
  function user_must_be_logged_in_before_accessing_the_controller()
  {
    $this->json('post', '/api/user_applications')
      ->assertStatus(401); 
  }

  /** @test */
  function it_requires_following_details()
  {
    $this->json('post', '/api/user_applications', [], $this->headers)
      ->assertStatus(422)
      ->assertExactJson([
          "errors"  =>  [
            "company_leave_id"  =>  ["The company leave id field is required."],
            "from_date"         =>  ["The from date field is required."],
            "to_date"           =>  ["The to date field is required."],
            'description'       =>  ["The description field is required."],
            "leave_type_id"     =>  ["The leave type id field is required."],
          ],
          "message" =>  "The given data was invalid."
        ]);
  }

  /** @test */
  function add_new_user_application()
  {
    $this->json('post', '/api/user_applications', $this->payload, $this->headers)
      ->assertStatus(201)
      ->assertJson([
          'data'   =>[
            'company_leave_id'  =>  $this->companyLeave->id,
            'from_date'         =>  '02-02-2019',
            'to_date'           =>  '05-02-2019',          ]
        ])
      ->assertJsonStructureExact([
          'data'   => [
            'company_leave_id',
            'from_date',
            'to_date',
            'description',
            'leave_type_id',
            'user_id',
            'updated_at',
            'created_at',
            'id'
          ],
          'success'
        ]);
  }

  /** @test */
  function list_of_user_applications()
  {
    $this->disableEH();
    $this->json('GET', '/api/user_applications',[], $this->headers)
      ->assertStatus(200)
      ->assertJsonStructure([
          'data' => [
            0 =>  [
              'company_leave_id',
              'from_date',
              'to_date',
              'user_id',
              'updated_at',
              'created_at',
              'id'
            ] 
          ]
        ]);
      $this->assertCount(2, UserApplication::all());
  }

  /** @test */
  function list_of_user_applications_of_specific_supervisor()
  {
    $this->disableEH();
    $this->json('GET', '/api/user_applications?user=supervisor',[], $this->headers)
      ->assertStatus(200)
      ->assertJsonStructure([
          'data' => [
            0 =>  [
              'company_leave_id',
              'from_date',
              'to_date',
              'user_id',
              'updated_at',
              'created_at',
              'id'
            ] 
          ]
        ]);
      $this->assertCount(2, UserApplication::all());
  }

  /** @test */
  function list_of_user_applications_of_specific_user_id()
  {
    $this->disableEH();
    $this->json('GET', '/api/user_applications?user_id=' . $this->user->id,[], $this->headers)
      ->assertStatus(200)
      ->assertJsonStructure([
          'data' => [
            0 =>  [
              'company_leave_id',
              'from_date',
              'to_date',
              'user_id',
              'updated_at',
              'created_at',
              'id'
            ] 
          ]
        ]);
      $this->assertCount(2, UserApplication::all());
  }

  /** @test */
  function show_single_user_application()
  {
    $this->json('get', "/api/user_applications/1", [], $this->headers)
      ->assertStatus(200)
      ->assertJson([
          'data'  => [
            'company_leave_id'  =>  $this->companyLeave->id,
            'from_date'         =>  '02-02-2019',
            'to_date'           =>  '05-02-2019',          ]
        ]);
  }

  /** @test */
  function update_single_user_attendance()
  {
    $payload = [ 
      'company_leave_id'  =>  $this->companyLeave->id,
      'from_date'         =>  '03-02-2019',
      'to_date'           =>  '05-02-2019',   
      'description'       =>  'Very Sick',
      'leave_type_id'     =>  $this->leaveType->id
    ];

    $this->json('patch', '/api/user_applications/1', $payload, $this->headers)
      ->assertStatus(200)
      ->assertJson([
          'data'    => [
            'company_leave_id'  =>  $this->companyLeave->id,
            'from_date'         =>  '03-02-2019',
            'to_date'           =>  '05-02-2019',   
            'description'       =>  'Very Sick' 
          ]
       ])
      ->assertJsonStructureExact([
          'data'  => [
            'id',
            'user_id',
            'company_leave_id',
            'from_date',
            'to_date',
            'created_at',
            'updated_at',
            'description',
            'leave_type_id'
          ],
          'success'
      ]);
  }
}
