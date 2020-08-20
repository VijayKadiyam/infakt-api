<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\CompanyStateHoliday;

class CompanyStateHolidayTest extends TestCase
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

    $this->companyState = factory(\App\CompanyState::class)->create([
      'company_id'  =>  $this->company->id 
    ]);

    factory(\App\CompanyStateHoliday::class)->create([
      'company_state_id'  =>  $this->companyState->id
    ]);

    $this->payload = [ 
      'holiday_id'     =>  1,
    ];
  }

  /** @test */
  function user_must_be_logged_in_before_accessing_the_controller()
  {
    $this->json('post', "/api/company_states/$this->companyState->id/company_state_holidays")
      ->assertStatus(401); 
  }

  /** @test */
  function it_requires_following_details()
  {
    $this->json('post', "/api/company_states/" .$this->companyState->id. "/company_state_holidays", [], $this->headers)
      ->assertStatus(422)
      ->assertExactJson([
          "errors"  =>  [
            "holiday_id"    =>  ["The holiday id field is required."]
          ],
          "message" =>  "The given data was invalid."
        ]);
  }

  /** @test */
  function add_new_company_state_holiday()
  {
    $this->disableEH();
    $this->json('post', "/api/company_states/" . $this->companyState->id . "/company_state_holidays", $this->payload, $this->headers)
      ->assertStatus(201)
      ->assertJson([
          'data'   =>[
            'holiday_id' => 1
          ]
        ])
      ->assertJsonStructureExact([
          'data'   => [
            'holiday_id',
            'company_state_id',
            'updated_at',
            'created_at',
            'id'
          ]
        ]);
  }

  /** @test */
  function list_of_company_state_holidays()
  {
    $this->json('GET', "/api/company_states/" . $this->companyState->id . "/company_state_holidays",[], $this->headers)
      ->assertStatus(200)
      ->assertJsonStructure([
          'data' => [
            0 =>  [
              'holiday_id'
            ] 
          ]
        ]);
      $this->assertCount(1, CompanyStateHoliday::all());
  }

  /** @test */
  function show_single_company_state_holiday()
  {
    $this->json('get', "/api/company_states/" . $this->companyState->id . "/company_state_holidays/1", [], $this->headers)
      ->assertStatus(200)
      ->assertJson([
          'data'  => [
            'holiday_id' => 1
          ]
        ]);
  }

  /** @test */
  function update_single_company_state_holiday()
  {
    $payload = [ 
      'holiday_id'  =>  '2'
    ];

    $this->json('patch', "/api/company_states/" . $this->companyState->id . "/company_state_holidays/1", $payload, $this->headers)
      ->assertStatus(200)
      ->assertJson([
          'data'    => [
            'holiday_id'  =>  '2'
          ]
       ])
      ->assertJsonStructureExact([
          'data'  => [
            'id',
            'company_state_id',
            'holiday_id',
            'created_at',
            'updated_at'
          ]
      ]);
  }

  /** @test */
  public function delete_single_company_state_holiday()
  {
    $holiday = factory(\App\CompanyStateHoliday::class)->create([
      'company_state_id'  =>  $this->companyState->id
    ]);
    $this->assertCount(2, CompanyStateHoliday::all());

    $this->json('delete', "/api/company_states/" . $this->companyState->id . "/company_state_holidays/2", [], $this->headers)
      ->assertStatus(200);

    $this->assertCount(1, CompanyStateHoliday::all());
  }
}
