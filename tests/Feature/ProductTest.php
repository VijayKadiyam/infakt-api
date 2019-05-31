<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Product;

class ProductTest extends TestCase
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

    factory(Product::class)->create([
      'company_id'  =>  $this->company->id,
    ]);

    $this->payload = [ 
      'name'     =>  'Product 2'
    ];
  }

  /** @test */
  function user_must_be_logged_in_before_accessing_the_controller()
  {
    $this->json('post', '/api/products')
      ->assertStatus(401); 
  }

  /** @test */
  function it_requires_following_details()
  {
    $this->json('post', '/api/products', [], $this->headers)
      ->assertStatus(422)
      ->assertExactJson([
          "errors"  =>  [
            "name"    =>  ["The name field is required."]
          ],
          "message" =>  "The given data was invalid."
        ]);
  }

  /** @test */
  function add_new_product()
  {
    $this->disableEH();
    $this->json('post', '/api/products', $this->payload, $this->headers)
      ->assertStatus(201)
      ->assertJson([
          'data'   =>[
            'name' => 'Product 2'
          ]
        ])
      ->assertJsonStructureExact([
          'data'   => [
            'name',
            'company_id',
            'updated_at',
            'created_at',
            'id'
          ]
        ]);
  }

  /** @test */
  function list_of_products()
  {
    $this->json('GET', '/api/products',[], $this->headers)
      ->assertStatus(200)
      ->assertJsonStructure([
          'data' => [
            0=>[
              'name'
            ] 
          ]
        ]);
      $this->assertCount(1, Product::all());
  }

  /** @test */
  function show_single_product()
  {
    $this->disableEH();
    $this->json('get', "/api/products/1", [], $this->headers)
      ->assertStatus(200)
      ->assertJson([
          'data'  => [
            'name'=> 'Product 1',
          ]
        ]);
  }

  /** @test */
  function update_single_product()
  {
    $payload = [ 
      'name'  =>  'Product 1 Updated'
    ];

    $this->json('patch', '/api/products/1', $payload, $this->headers)
      ->assertStatus(200)
      ->assertJson([
          'data'    => [
            'name'  =>  'Product 1 Updated',
          ]
       ])
      ->assertJsonStructureExact([
          'data'  => [
            'id',
            'company_id',
            'name',
            'created_at',
            'updated_at'
          ]
      ]);
  }
}
