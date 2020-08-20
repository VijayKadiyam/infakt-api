<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\UserAttendance;

class UserAttendanceTest extends TestCase
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

    factory(\App\UserAttendance::class)->create([
      'user_id'  =>  $this->user->id 
    ]);

    factory(\App\UserAttendance::class)->create([
      'user_id'  =>  $this->user->id,
      'date'     => "2019-03-09" 
    ]);

    $this->date = (\Carbon\Carbon::now()->format('Y-m-d'));
    $this->toDate = (\Carbon\Carbon::now()->addDay()->format('Y-m-d'));
    $this->payload = [ 
      'date'        =>  $this->date,
      'login_time'  =>  '10.15',
      'logout_time' =>  '6.20',
      'login_lat'   =>  '23.34',
      'login_lng'   =>  '23.34',
      'logout_lat'  =>  '34.34',
      'logout_lng'  =>  '34.34'
    ];

  }

  /** @test */
  function user_must_be_logged_in_before_accessing_the_controller()
  {
    $this->json('post', '/api/user_attendances')
      ->assertStatus(401); 
  }

  /** @test */
  function it_requires_following_details()
  {
    $this->json('post', '/api/user_attendances', [], $this->headers)
      ->assertStatus(422)
      ->assertExactJson([
          "errors"  =>  [
            "date"        =>  ["The date field is required."],
            "login_time"  =>  ["The login time field is required."],
            // "logout_time" =>  ["The logout time field is required."],
            // "login_lat"   =>  ["The login lat field is required."],
            // "login_lng"   =>  ["The login lng field is required."],
            // "logout_lat"  =>  ["The logout lat field is required."],
            // "logout_lng"  =>  ["The logout lng field is required."],
          ],
          "message" =>  "The given data was invalid."
        ]);
  }

  /** @test */
  function add_new_user_attendance()
  {
    $this->disableEH();
    $this->json('post', '/api/user_attendances', $this->payload, $this->headers)
      ->assertStatus(201)
      ->assertJson([
          'data'   =>[
            'date'        =>  $this->date,
            'login_time'  =>  '10.15',
            'logout_time' =>  '6.20',
            'login_lat'   =>  '23.34',
            'login_lng'   =>  '23.34',
            'logout_lat'  =>  '34.34',
            'logout_lng'  =>  '34.34'          ]
        ])
      ->assertJsonStructureExact([
          'data'   => [
            'date',
            'login_time',
            'logout_time',
            'login_lat',
            'login_lng',
            'logout_lat',
            'logout_lng',
            'user_id',
            'updated_at',
            'created_at',
            'id'
          ],
          'success'
        ]);
  }

  /** @test */
  function list_of_user_attendances()
  {
    $this->disableEH();

    $this->json('GET', '/api/user_attendances',[], $this->headers)
      ->assertStatus(200)
      ->assertJsonStructure([
          'data' => [
            0 =>  [
              'date',
              'login_time',
              'logout_time',
              'login_lat',
              'login_lng',
              'logout_lat',
              'logout_lng'
            ] 
          ]
        ]);
    $this->assertCount(2, UserAttendance::all());
  }

  /** @test */
  function list_of_user_attendances_of_specific_dat()
  {
    $this->json('GET', "/api/user_attendances?date=" . $this->date,[], $this->headers)
      ->assertStatus(200)
      ->assertJsonStructure([
          'data' => [
            'date',
            'login_time',
            'logout_time',
            'login_lat',
            'login_lng',
            'logout_lat',
            'logout_lng'
          ]
        ]);
    $this->assertCount(2, UserAttendance::all());
  }

  /** @test */
  function list_of_user_attendances_of_specific_month()
  {
    $this->disableEH();
    $this->json('GET', "/api/user_attendances?month=" . \Carbon\Carbon::now()->format('m'),[], $this->headers)
      ->assertStatus(200)
      ->assertJsonStructure([
          'data' => [
            0 =>  [
              'date',
              'login_time',
              'logout_time',
              'login_lat',
              'login_lng',
              'logout_lat',
              'logout_lng'
            ]
          ]
        ]);
    $this->assertCount(2, UserAttendance::all());
  }

  /** @test */
  function list_of_user_attendances_of_specific_company_of_a_date()
  {
    $this->disableEH();
    $this->json('GET', '/api/user_attendances?searchDate=' . $this->date,[], $this->headers)
      ->assertStatus(200)
      ->assertJsonStructure([
          'data' => [
            0 =>  [
              'name',
              'roles',
              'companies',
              'company_designation',
              'company_state_branch',
              'supervisors',
              'user_attendances'
            ]
          ]
        ]);
    $this->assertCount(2, UserAttendance::all());
  }

  /** @test */
  function list_of_user_attendances_of_specific_company_and_between_dates()
  {
    $this->disableEH();
    $this->json('GET', '/api/user_attendances?fromDate=' . $this->date . '&toDate=' . $this->toDate,[], $this->headers)
      ->assertStatus(200)
      ->assertJsonStructure([
          'data' => [
            0 =>  [
              'name',
              'roles',
              'companies',
              'company_designation',
              'company_state_branch',
              'supervisors',
              'user_attendances'
            ]
          ]
        ]);
    $this->assertCount(2, UserAttendance::all());
  }

  /** @test */
  function show_single_user_attendance()
  {
    $this->json('get', "/api/user_attendances/1", [], $this->headers)
      ->assertStatus(200)
      ->assertJson([
          'data'  => [
            'date'        =>  $this->date,
            'login_time'  =>  '10.15',
            'logout_time' =>  '6.20',
            'login_lat'   =>  '23.34',
            'login_lng'   =>  '23.34',
            'logout_lat'  =>  '34.34',
            'logout_lng'  =>  '34.34'          ]
        ]);
  }

  /** @test */
  function update_single_user_attendance()
  {
    $payload = [ 
      'date'        =>  '2019-01-02',
      'login_time'  =>  '10.15',
      'logout_time' =>  '6.20',
      'login_lat'   =>  '23.34',
      'login_lng'   =>  '23.34',
      'logout_lat'  =>  '34.34',
      'logout_lng'  =>  '34.34'    
    ];

    $this->json('patch', '/api/user_attendances/1', $payload, $this->headers)
      ->assertStatus(200)
      ->assertJson([
          'data'    => [
            'date'        =>  '2019-01-02',
            'login_time'  =>  '10.15',
            'logout_time' =>  '6.20',
            'login_lat'   =>  '23.34',
            'login_lng'   =>  '23.34',
            'logout_lat'  =>  '34.34',
            'logout_lng'  =>  '34.34'   
          ]
       ])
      ->assertJsonStructureExact([
          'data'  => [
            'id',
            'user_id',
            'date',
            'login_time',
            'logout_time',
            'login_lat',
            'logout_lat',
            'login_lng',
            'logout_lng',
            'created_at',
            'updated_at',
          ],
          'success'
      ]);
  }
}
