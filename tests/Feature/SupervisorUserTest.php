<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class SupervisorUserTest extends TestCase
{

  use DatabaseTransactions;

  /** @test */
  function user_must_be_logged_in()
  {
    $this->json('post', '/api/supervisor_user')
      ->assertStatus(401); 
  }

  /** @test */
  function it_requires_following_details()
  {
    $this->json('post', '/api/supervisor_user', [], $this->headers)
         ->assertStatus(422)
         ->assertExactJson([
            "errors"     =>  [
              "supervisor_id"  =>  ["The supervisor id field is required."],
              "user_id"        =>  ["The user id field is required."]
            ],
            "message"    =>  "The given data was invalid."
        ]);
  }

  /** @test */
  function assign_supervisor()
  {
    $userTwo  = factory(\App\User::class)->create();
    $this->user->assignSupervisor($userTwo->id);
    $check    = $this->user->hasSupervisor($userTwo->id);
    $this->assertTrue($check);
  }

  /** @test */
  function assign_supervisor_to_user()
  {
    $this->disableEH();
    $userTwo       = factory(\App\User::class)->create();
    $this->payload = [ 
      'user_id'       => $this->user->id,
      'supervisor_id' => $userTwo->id
    ];
    $this->json('post', '/api/supervisor_user', $this->payload , $this->headers)
         ->assertStatus(201)
         ->assertJson([
          'data'  =>  [
            'name'                    =>  $this->user->name,
            'phone'                   =>  $this->user->phone,
            'email'                   =>  $this->user->email,
            'doj'                     =>  $this->user->doj,
            'dob'                     =>  $this->user->dob,
            'company_designation_id'  =>  $this->user->company_designation_id,
            'company_state_branch_id' =>  $this->user->company_state_branch_id,
            'pf_no'                   =>  $this->user->pf_no,
            'uan_no'                  =>  $this->user->uan_no,
            'esi_no'                  =>  $this->user->esi_no,
            'salary'                  =>  $this->user->salary,
            'terms_accepted'          =>  $this->user->terms_accepted,
            'supervisors'             =>  [
              0 =>  [
                'name'  =>  $userTwo->name
              ]
            ]
          ]
        ])
      ->assertJsonStructureExact([
        'data'  =>  [
          'id',
          'name',
          'email',
          'email_verified_at',
          'active',
          'phone',
          'api_token',
          'doj',
          'dob',
          'company_designation_id',
          'company_state_branch_id',
          'pf_no',
          'uan_no',
          'esi_no',
          'created_at',
          'updated_at',
          'salary',
          'image_path',
          'employee_code',
          'asm_area',
          'asm_name',
          'uid_no',
          'terms_accepted',
          'company_state_id',
          'address',
          'unique_id',
          'supervisors'
        ]
      ]);
  }
}
