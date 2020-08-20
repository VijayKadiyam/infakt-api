<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DataBaseTransactions;
use App\User;

class LogoutTest extends TestCase
{
  use DataBaseTransactions;

  /** @test */
  function user_logout_requires_user_must_be_logged_in()
  {
    $this->json('POST','/api/logout')
      ->assertStatus(204);
  }

  /** @test */
  function user_logged_out_successfully()
  {
    $user = factory(User::class)->create([
    'email'    => 'sangeetha@gmail.com',
    'password' => bcrypt('behappy')
    ]);
    $user->generateToken();
    $headers=[
      'Authorization'=>"Bearer $user->api_token"
    ];

    $this->json('POST','/api/logout', [], $headers)
      ->assertStatus(200)
      ->assertJson([
          'message'=>'user is logged out successfully'
        ]);
  }
}
