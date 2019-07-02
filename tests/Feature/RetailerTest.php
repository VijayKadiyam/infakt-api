<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Retailer;

class RetailerTest extends TestCase
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

    $referencePlan = factory(\App\ReferencePlan::class)->create([
      'company_id'  =>  $this->company->id 
    ]);

    factory(\App\Retailer::class)->create([
      'reference_plan_id'  =>  $referencePlan->id 
    ]);

    $this->payload = [ 
      'name'     => 'Name',
      'adress'   => 'Address'
    ];
  }

  /** @test */
  function user_must_be_logged_in_before_accessing_the_controller()
  {
    $this->json('get', '/api/un_approved_retailers')
      ->assertStatus(401); 
  }

  /** @test */
  function to_get_thie_list_un_approved_retailers()
  {
    $this->disableEH();
    $this->json('GET', '/api/un_approved_retailers',[], $this->headers)
      ->assertStatus(200)
      ->assertJsonStructure([
          'data' => [
            0 => [
              'name'
            ] 
          ]
        ]);
      // $this->assertCount(1, ReferencePlan::all());
  }

  /** @test */
  function to_get_approve_retailer()
  {
    $this->json('GET', '/api/approve_retailer/1', [], $this->headers)
      ->assertStatus(200)
      ->assertJsonStructure([
          'data' => [
            'name'
          ]
        ]);
  }

  /** @test */
  function to_approve_retailer()
  {
    $payload = [
      'retailer_id' =>  '1',
      'approved'     =>  0
    ];
    $this->json('GET', '/api/approve_retailer', $payload, $this->headers)
      ->assertStatus(200)
      ->assertJsonStructure([
          'data' => [
            'name'
          ]
        ]);
  }
}
