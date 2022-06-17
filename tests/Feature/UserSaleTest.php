<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\UserSale;

class UserSaleTest extends TestCase
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

    factory(\App\UserSale::class)->create([
      'user_id'  =>  $this->user->id 
    ]);

    $this->date = (\Carbon\Carbon::now()->format('Y-m-d'));
    $this->payload = [ 
      'date'          =>  $this->date,
      'amount'        =>  '50',
      'customer_name' =>  'Ajay',
      'phone_no'      =>  '5678767656'
    ];
  }

  /** @test */
  function user_must_be_logged_in_before_accessing_the_controller()
  {
    $this->json('post', '/api/user_sales')
      ->assertStatus(401); 
  }

  /** @test */
  function it_requires_following_details()
  {
    $this->json('post', '/api/user_sales', [], $this->headers)
      ->assertStatus(422)
      ->assertExactJson([
          "errors"  =>  [
            "date"          =>  ["The date field is required."],
            "amount"        =>  ["The amount field is required."],
            "customer_name" =>  ["The customer name field is required."],
            "phone_no"      =>  ["The phone no field is required."],
          ],
          "message" =>  "The given data was invalid."
        ]);
  }

  /** @test */
  function add_new_user_attendance()
  {
    $this->disableEH();
    $this->json('post', '/api/user_sales', $this->payload, $this->headers)
      ->assertStatus(201)
      ->assertJson([
          'data'   =>[
            'date'        =>  $this->date,
            'amount'      =>  '50' 
          ]
        ])
      ->assertJsonStructureExact([
          'data'   => [
            'date',
            'amount',
            'customer_name',
            'phone_no',
            'user_id',
            'updated_at',
            'created_at',
            'id'
          ]
        ]);
  }

  /** @test */
  function list_of_user_sales()
  {
    $this->json('GET', '/api/user_sales',[], $this->headers)
      ->assertStatus(200)
      ->assertJsonStructure([
          'data' => [
            0 =>  [
              'date',
              'amount'
            ] 
          ]
        ]);
    $this->assertCount(1, UserSale::all());
  }

  /** @test */
  function list_of_user_sales_of_specific_month()
  {
    $this->disableEH();
    $this->json('GET', '/api/user_sales?month='  . \Carbon\Carbon::now()->format('m') . '&user_id=',[], $this->headers)
      ->assertStatus(200)
      ->assertJsonStructure([
          'data' => [
            0 =>  [
              'date',
              'amount'
            ] 
          ]
        ]);
    $this->assertCount(1, UserSale::all());
  }

  /** @test */
  function list_of_user_sales_of_specific_month_and_specific_user()
  {
    $this->disableEH();
    $this->json('GET', '/api/user_sales?month='  . \Carbon\Carbon::now()->format('m') . '&user_id=' . $this->user->id,[], $this->headers)
      ->assertStatus(200)
      ->assertJsonStructure([
          'data' => [
            0 =>  [
              'date',
              'amount'
            ] 
          ]
        ]);
    $this->assertCount(1, UserSale::all());
  }

  /** @test */
  function show_single_user_sale()
  {
    $this->json('get', "/api/user_sales/1", [], $this->headers)
      ->assertStatus(200)
      ->assertJson([
          'data'  => [
            'date'        =>  $this->date,
            'amount'      =>  '20'          ]
        ]);
  }

  /** @test */
  function update_single_user_attendance()
  {
    $payload = [ 
      'date'        =>  '02-01-2019',
      'amount'      =>  '30' 
    ];

    $this->json('patch', '/api/user_sales/1', $payload, $this->headers)
      ->assertStatus(200)
      ->assertJson([
          'data'    => [
            'date'        =>  '02-01-2019',
            'amount'      =>  '30' 
          ]
       ])
      ->assertJsonStructureExact([
          'data'  => [
            'id',
            'user_id',
            'date',
            'amount',
            'created_at',
            'updated_at',
            'customer_name',
            'phone_no'
          ]
      ]);
  }
}
