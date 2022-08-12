<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class MeTest extends TestCase
{
  use DatabaseTransactions;

  /** @test */
  function get_logged_in_user()
  {
    $this->json('get', '/api/me', [], $this->headers)
      ->assertStatus(200)
      ->assertJson([
        'data'    =>  [
          'phone' =>  $this->user->phone,
          'email' =>  $this->user->email,
        ]
      ])
      ->assertJsonStructureExact([
        'data'  => [
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
          'roles',
          'companies',
        ],
        'version',
        'success'
      ]);
  }
}
