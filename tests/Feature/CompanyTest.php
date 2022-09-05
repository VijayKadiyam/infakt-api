<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Company;

class CompanyTest extends TestCase
{
  use DatabaseTransactions;

  protected $company;

  public function setUp()
  {
    parent::setUp();

    $this->company = factory(\App\Company::class)->create([
      'name' => 'test'
    ]);

    $this->user->assignRole(1);
    $this->user->assignCompany($this->company->id);

    $this->payload = [
      'name'    =>  'AAIBUZZ',
      'phone'   =>  345765433,
      'email'   =>  'email@gmail.com',
      'address' =>  '606, Vardhaman Plaza',
      'time_zone' =>  'Asia/Calcutta'
    ];
  }

  /** @test */
  function user_must_be_logged_in_before_accessing_the_controller()
  {
    $this->json('post', '/api/companies')
      ->assertStatus(401);
  }

  /** @test */
  function it_requires_following_details()
  {
    $this->json('post', '/api/companies', [], $this->headers)
      ->assertStatus(422)
      ->assertExactJson([
        "errors"  =>  [
          "name"    =>  ["The name field is required."],
          "email"   =>  ["The email field is required."],
          "phone"   =>  ["The phone field is required."],
          "address" =>  ["The address field is required."],
        ],
        "message" =>  "The given data was invalid."
      ]);
  }

  /** @test */
  function add_new_organization()
  {
    $this->disableEH();
    $this->json('post', '/api/companies', $this->payload, $this->headers)
      ->assertStatus(201)
      ->assertJson([
        'data'   => [
          'name' => 'AAIBUZZ'
        ]
      ])
      ->assertJsonStructureExact([
        'data'   => [
          'name',
          'phone',
          'email',
          'address',
          'updated_at',
          'created_at',
          'id',

        ]
      ]);
    // $this->company->saveDefaultDesignations();
    // $this->assertCount(1, $this->company->company_designations);
    // $this->company->saveDefaultCompanyLeaves();
    // $this->assertCount(12, $this->company->company_leaves);
  }

  /** @test */
  function list_of_companies()
  {
    $this->json('GET', '/api/companies', [], $this->headers)
      ->assertStatus(200)
      ->assertJsonStructure([
        'data' => [
          0 => [
            'name'
          ]
        ]
      ]);
    $this->assertCount(2, Company::all());
  }

  /** @test */
  function show_single_company()
  {
    $this->json('get', "/api/companies/2", [], $this->headers)
      ->assertStatus(200)
      ->assertJson([
        'data'  => [
          'name' => 'test',
          'code'=> 'code'
        ]
      ]);
  }

  /** @test */
  function update_single_company()
  {
    $payload = [
      'name'  =>  'AAIBUZZZ'
      
      
    ];

    $this->json('patch', '/api/companies/2', $payload, $this->headers)
      ->assertStatus(200)
      ->assertJson([
        'data'    => [
          'name'  =>  'AAIBUZZZ',
          'code'=> 'code',
        ]
      ])
      ->assertJsonStructureExact([
        'data'  => [
          'id',
          'name',
          'email',
          'phone',
          'address',
          'logo_path',
          'contact_person',
          'code',
          'created_at',
          'updated_at',
        ]
      ]);
  }
}
