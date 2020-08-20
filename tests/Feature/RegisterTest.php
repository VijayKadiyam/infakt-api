<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class RegisterTest extends TestCase
{
  use DatabaseTransactions;

  /** @test */
  public function registration_requires_following_fields()
  {
    $this->json('POST','/api/register')
      ->assertStatus(422)
      ->assertExactJson([
          "errors"  =>  [
            "name"                    =>  ["The name field is required."],
            "email"                   =>  ["The email field is required."],
            "phone"                   =>  ["The phone field is required."],
            "password"                =>  ["The password field is required."],
            "doj"                     =>  ["The doj field is required."],
            "dob"                     =>  ["The dob field is required."],
            "company_designation_id"  =>  ["The company designation id field is required."],
          ],
          "message" =>  "The given data was invalid."
        ]);
  }

  /** @test */
  function user_registered_successfully()
  {
    $this->disableEH();

    $userDetails = [
      'name'                 =>'sangeetha',
      'phone'                => 9844778380,
      'email'                =>'sangeetha@gmail.com',
      'password'             =>'behappy',
      'password_confirmation'=>'behappy',
      'doj'               =>  '12-02-2019',
      'dob'               =>  '04-05-1992',
      'company_designation_id'  =>  1,
      'company_state_branch_id' => 1,
      'pf_no'                   =>  '1234567654',
      'uan_no'                  =>  '1234565432',
      'esi_no'                  =>  '234565'
    ];

    $this->json('POST','/api/register', $userDetails)
      ->assertStatus(201)
      ->assertJson([
        'data'  =>  [
          'name'                 =>'sangeetha',
          'phone'                => 9844778380,
          'email'                =>'sangeetha@gmail.com',
          'doj'               =>  '12-02-2019',
          'dob'               =>  '04-05-1992',
          'company_designation_id'  =>  1,
          'company_state_branch_id' => 1,
          'pf_no'                   =>  '1234567654',
          'uan_no'                  =>  '1234565432',
          'esi_no'                  =>  '234565'
        ]
      ])
      ->assertJsonStructure([
          'data'  =>  [
            'name',
            'phone',
            'email',
            'api_token'
          ]
        ])
      ->assertJsonStructureExact([
          'data'  =>  [
            'name',
            'email',
            'phone',
            'doj',
            'dob',
            'company_designation_id',
            'company_state_branch_id',
            'pf_no',
            'uan_no',
            'esi_no',
            'updated_at',
            'created_at',
            'id',
            'api_token'
          ]
        ]);
  }
}
