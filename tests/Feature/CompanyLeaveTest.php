<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\LeavePattern;
use App\CompanyLeave;

class CompanyLeaveTest extends TestCase
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

    factory(\App\CompanyLeave::class)->create([
      'company_id'  =>  $this->company->id
    ]);

    $this->leavePattern = LeavePattern::find(1);

    $this->payload = [ 
      'leave_pattern_id'  =>  $this->leavePattern->id,
      'name'     => 'Sick Leave',
      'leaves'   => '20'
    ];
  }

  /** @test */
  function user_must_be_logged_in_before_accessing_the_controller()
  {
    $this->json('post', '/api/company_leaves')
      ->assertStatus(401); 
  }

  /** @test */
  function it_requires_following_details()
  {
    $this->json('post', '/api/company_leaves', [], $this->headers)
      ->assertStatus(422)
      ->assertExactJson([
          "errors"  =>  [
            "leave_pattern_id"  =>  ["The leave pattern id field is required."],
            "name"              =>  ["The name field is required."],
            "leaves"            =>  ["The leaves field is required."]
          ],
          "message" =>  "The given data was invalid."
        ]);
  }

  /** @test */
  function add_new_company_leave()
  {
    $this->disableEH();
    $this->json('post', '/api/company_leaves', $this->payload, $this->headers)
      ->assertStatus(201)
      ->assertJson([
          'data'   =>[
            'leave_pattern_id'  =>  $this->leavePattern->id,
            'name'     => 'Sick Leave',
            'leaves'   => '20'
          ]
        ])
      ->assertJsonStructureExact([
          'data'   => [
            'leave_pattern_id',
            'name',
            'leaves',
            'company_id',
            'updated_at',
            'created_at',
            'id',
          ]
        ]);
  }

  /** @test */
  function list_of_company_leaves()
  {
    $this->json('GET', '/api/company_leaves',[], $this->headers)
      ->assertStatus(200)
      ->assertJsonStructure([
          'data' => [
            0=>[
              'name'
            ] 
          ]
        ]);
      $this->assertCount(13, CompanyLeave::all());
  }

  /** @test */
  function show_single_company_leaves()
  {
    $this->json('get', "/api/company_leaves/13", [], $this->headers)
      ->assertStatus(200)
      ->assertJson([
          'data'  => [
            'name'=> 'Casual Leave',
          ]
        ]);
  }

  /** @test */
  function update_single_company_leave()
  {
    $payload = [ 
      'name'  =>  'Leave'
    ];

    $this->json('patch', '/api/company_leaves/13', $payload, $this->headers)
      ->assertStatus(200)
      ->assertJson([
          'data'    => [
            'name'  =>  'Leave',
          ]
       ])
      ->assertJsonStructureExact([
          'data'  => [
            'id',
            'company_id',
            'leave_pattern_id',
            'name',
            'leaves',
            'created_at',
            'updated_at'
          ]
      ]);
  }
}
