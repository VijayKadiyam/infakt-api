<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\UserAttendanceBreak;

class UserAttendanceBreakTest extends TestCase
{
 use DatabaseTransactions;
  
  public function setUp()
  {
    parent::setUp();

    $this->userAttendance = factory(\App\UserAttendance::class)->create([
      'user_id'  =>  $this->user->id 
    ]);

    $this->company = factory(\App\Company::class)->create([
      'name' => 'test'
    ]);
    $this->user->assignCompany($this->company->id);
    $this->headers['company-id'] = $this->company->id;
    $this->breakType = factory(\App\BreakType::class)->create([
      'company_id'  =>  $this->company->id 
    ]);

    factory(\App\UserAttendanceBreak::class)->create([
      'user_attendance_id'  =>  $this->userAttendance->id,
      'break_type_id'       =>  $this->breakType->id 
    ]);

    $this->payload = [ 
      'break_type_id'       =>  $this->breakType->id,
      'start_time'          =>  '11.20',
    ];
  }

  /** @test */
  function user_must_be_logged_in_before_accessing_the_controller()
  {
    $this->json('post', '/api/user_attendances/' . $this->userAttendance->id . '/user_attendance_breaks')
      ->assertStatus(401); 
  }

  /** @test */
  function it_requires_following_details()
  {
    $this->json('post', '/api/user_attendances/' . $this->userAttendance->id . '/user_attendance_breaks', [], $this->headers)
      ->assertStatus(422)
      ->assertExactJson([
          "errors"  =>  [
            "break_type_id" =>  ["The break type id field is required."],
            "start_time"    =>  ["The start time field is required."]
          ],
          "message" =>  "The given data was invalid."
        ]);
  }

  /** @test */
  function add_new_user_attendance_break()
  {
    $this->disableEH();
    $this->json('post', '/api/user_attendances/' . $this->userAttendance->id . '/user_attendance_breaks', $this->payload, $this->headers)
      ->assertStatus(201)
      ->assertJson([
          'data'   =>[
            'break_type_id' =>  $this->breakType->id,
            'start_time'    =>  '11.20',
            'break_type'    =>  [
              'name'  =>  'PEE'
            ]    
          ]
        ])
      ->assertJsonStructureExact([
          'data'   => [
            'break_type_id',
            'start_time',
            'user_attendance_id',
            'updated_at',
            'created_at',
            'id',
            'break_type'
          ]
        ]);
  }

  /** @test */
  function list_of_user_attendance_breaks()
  {
    $this->json('GET', '/api/user_attendances/' . $this->userAttendance->id . '/user_attendance_breaks',[], $this->headers)
      ->assertStatus(200)
      ->assertJsonStructure([
          'data' => [
            0 =>  [
              'break_type_id',
              'start_time',
              'break_type'
            ] 
          ]
        ]);
    $this->assertCount(1, UserAttendanceBreak::all());
  }

  /** @test */
  function show_single_user_attendance_break()
  {
    $this->json('get', '/api/user_attendances/' . $this->userAttendance->id . '/user_attendance_breaks/1', [], $this->headers)
      ->assertStatus(200)
      ->assertJson([
          'data'  => [
            'break_type_id'       =>  $this->breakType->id,
            'start_time'          =>  '11.20',
          ]
        ]);
  }

  /** @test */
  function update_single_user_attendance()
  {
    $payload = [ 
      'break_type_id'       =>  $this->breakType->id,
      'start_time'          =>  '11.30',
    ];

    $this->json('patch', '/api/user_attendances/' . $this->userAttendance->id . '/user_attendance_breaks/1', $payload, $this->headers)
      ->assertStatus(200)
      ->assertJson([
          'data'    => [
            'break_type_id'       =>  $this->breakType->id,
            'start_time'          =>  '11.30', 
          ]
       ])
      ->assertJsonStructureExact([
          'data'  => [
            'id',
            'user_attendance_id',
            'break_type_id',
            'start_time',
            'end_time',
            'created_at',
            'updated_at',
          ]
      ]);
  }

}
