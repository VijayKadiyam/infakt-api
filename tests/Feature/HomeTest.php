<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class HomeTest extends TestCase
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

    $this->date = (\Carbon\Carbon::now()->format('Y-m-d'));
  }

  /** @test */
  function user_must_be_logged_in_before_accessing_the_controller()
  {
    $this->json('get', '/api/count')
      ->assertStatus(401); 
  }

  /** @test */
  function get_the_counts()
  {
    $this->disableEH();
    $user  = factory(\App\User::class)->create();
    $user->assignRole(3);
    $user->assignCompany($this->company->id);

    factory(\App\UserAttendance::class)->create([
      'user_id'  =>  $this->user->id,
      'date'     => $this->date
    ]);

    $this->json('get', '/api/count?date=' . $this->date, [], $this->headers)
      ->assertStatus(200);
  }
}
