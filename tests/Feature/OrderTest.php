<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Order;
use App\OrderDetail;

class OrderTest extends TestCase
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

    factory(Order::class)->create([
      'company_id'  =>  $this->company->id 
    ]);

    $this->payload = [ 
      'distributor_id'  =>  1,
      'user_id'         =>  2,
      'retailer_id'     =>  3,
      'status'          =>  0,
      'order_details' =>  [
        0 =>  [
          'sku_id'        =>  1,
          'qty'           =>  1,
          'value'         =>  100,
          'qty_delivered' =>  1
        ]
      ]
    ];
  }

  /** @test */
  function user_must_be_logged_in_before_accessing_the_controller()
  {
    $this->json('post', '/api/orders')
      ->assertStatus(401); 
  }

  /** @test */
  function it_requires_following_details()
  {
    $payload = [
      'order_details' =>  [
        0 =>  []
      ]
    ];

    $this->json('post', '/api/orders', $payload, $this->headers)
      ->assertStatus(422)
      ->assertExactJson([
          "errors"  =>  [
            "distributor_id"  =>  ["The distributor id field is required."],
            "user_id"         =>  ["The user id field is required."],
            "retailer_id"     =>  ["The retailer id field is required."],
            "status"          =>  ["The status field is required."],
            "order_details.0.sku_id"     =>  ["The order_details.0.sku_id field is required."],
            "order_details.0.qty"     =>  ["The order_details.0.qty field is required."],
            "order_details.0.value"     =>  ["The order_details.0.value field is required."],
          ],
          "message" =>  "The given data was invalid."
        ]);
  }

  /** @test */
  function add_new_order()
  {
    $this->disableEH();
    $this->json('post', '/api/orders', $this->payload, $this->headers)
      ->assertStatus(201)
      ->assertJson([
          'data'   =>[
            'distributor_id'  =>  1,
            'user_id'         =>  2,
            'retailer_id'     =>  3,
            'status'          =>  0,
            'order_details' =>  [
              0 =>  [
                'sku_id'        =>  1,
                'qty'           =>  1,
                'value'         =>  100,
                'qty_delivered' =>  1
              ]
            ]
          ]
        ])
      ->assertJsonStructureExact([
          'data'   => [
            'distributor_id',
            'user_id',
            'retailer_id',
            'status',
            'company_id',
            'updated_at',
            'created_at',
            'id',
            'order_details'
          ],
          'success'
        ]);
  }

  /** @test */
  function list_of_orders()
  {
    $this->disableEH();
    $this->json('GET', '/api/orders',[], $this->headers)
      ->assertStatus(200)
      ->assertJsonStructure([
          'data' => [
            0 =>  [
              'distributor_id'
            ]
          ],
          'success'
        ]);
      $this->assertCount(1, Order::all());
  }

  /** @test */
  function list_of_orders_of_a_user_and_date()
  {
    $this->disableEH();
    $now = \Carbon\Carbon::now()->format('Y-m-d');
    $this->json('GET', '/api/orders?userId=2&date=' . $now ,[], $this->headers)
      ->assertStatus(200)
      ->assertJsonStructure([
          'data' => [
            0 =>  [
              'distributor_id'
            ]
          ],
          'success'
        ]);
      $this->assertCount(1, Order::all());
  }

  /** @test */
  public function list_of_orders_of_search()
  {
    $order = factory(Order::class)->create([
      'company_id'  =>  $this->company->id
    ]);

    $this->json('get', '/api/orders?search=' . $order->company_name, [], $this->headers)
      ->assertStatus(200)
      ->assertJsonStructure([
          'data' => [
            0 =>  [
              'distributor_id'
            ]
          ]
        ]);
  }

  /** @test */
  function show_single_order()
  {
    $this->json('get', "/api/orders/1", [], $this->headers)
      ->assertStatus(200)
      ->assertJson([
          'data'  => [
            'distributor_id'  =>  1,
          ],
          'success' =>  true
        ]);
  }

  /** @test */
  function update_single_order()
  {
    $payload = [ 
      'distributor_id'  =>  1,
    ];

    $order = factory(Order::class)->create([
      'company_id'  =>  $this->company->id 
    ]);
    $orderDetail = factory(OrderDetail::class)->create([
      'order_id'  =>  $order->id 
    ]);

    // Old Edit + No Delete + 1 New
    $payload = [ 
      'id'              =>  $order->id,
      'distributor_id'  =>  2,
      'user_id'         =>  2,
      'retailer_id'     =>  3,
      'status'          =>  0,
      'order_details' =>  [
        0 =>  [
          'id'            =>  $orderDetail->id,
          'sku_id'        =>  2,
          'qty'           =>  1,
          'value'         =>  100,
          'qty_delivered' =>  1
        ]
      ]
    ];


    $this->json('post', '/api/orders', $payload, $this->headers)
      ->assertStatus(201)
      ->assertJson([
          'data'    => [
            'id'              =>  $order->id,
            'distributor_id'  =>  2,
            'user_id'         =>  2,
            'retailer_id'     =>  3,
            'status'          =>  0,
            'order_details' =>  [
              0 =>  [
                'id'            =>  $orderDetail->id,
                'sku_id'        =>  2,
                'qty'           =>  1,
                'value'         =>  100,
                'qty_delivered' =>  1
              ]
            ]
          ]
       ])
      ->assertJsonStructureExact([
          'data'  => [
            'id',
            'company_id',
            'distributor_id', 'user_id', 'retailer_id', 'status',
            'total',
            'created_at',
            'updated_at',
            'order_details'
          ],
          'success'
      ]);
  }
}
