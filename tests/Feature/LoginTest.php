<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DataBaseTransactions;
use App\User;

class LoginTest extends TestCase
{
  use DataBaseTransactions;

  /** @test */
  function login_requires_email_and_password()
  {
    $this->json('POST','/api/login')
      ->assertStatus(422)
      ->assertExactJson([
          "errors"  =>  [
            "email"     =>  ["The email field is required."],
            "password"  =>  ["The password field is required."]
          ],
          "message" =>  "The given data was invalid."
        ]);
  }

  /** @test */
  function user_logged_in_successfully()
  {
    $this->disableEH();
    factory(User::class)->create([
      'email'    => 'sangeetha@gmail.com',
      'phone'    =>  '9987667431',
      'password' => bcrypt('behappy'),
      'active'   => 1
    ]);
    $payLoad=[
      'email'    =>  'sangeetha@gmail.com',
      'password' => 'behappy'
    ];
    $this->json('POST','/api/login',$payLoad)
      ->assertStatus(200)
      ->assertJsonStructure([
          'data'  =>[ 
            'phone',
            'email',
            'api_token'
          ]
        ])
      ->assertJsonStructureExact([
          'data',
          'message',
          'token',
          'success'
        ]);; 
  }
}
