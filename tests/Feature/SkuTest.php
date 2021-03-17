<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Sku;
use App\SkuType;
use App\Product;

class SkuTest extends TestCase
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

    $this->product = factory(Product::class)->create([
      'company_id'  =>  $this->company->id,
    ]);

    factory(Sku::class)->create([
      'product_id'  =>  $this->product->id,
    ]);

    $this->payload = [ 
      'name'     =>  'Dove',
      'company_id'  =>  $this->company->id
    ];
  }

  /** @test */
  function user_must_be_logged_in_before_accessing_the_controller()
  {
    $this->json('post', '/api/products/' . $this->product->id . '/skus')
      ->assertStatus(401); 
  }

  /** @test */
  function it_requires_following_details()
  {
    $this->json('post', '/api/products/' . $this->product->id . '/skus', [], $this->headers)
      ->assertStatus(422)
      ->assertExactJson([
          "errors"  =>  [
            "name"        =>  ["The name field is required."],
            "company_id"  =>  ["The company id field is required."]
          ],
          "message" =>  "The given data was invalid."
        ]);
  }

  /** @test */
  function add_new_sku()
  {
    $this->json('post', '/api/products/' . $this->product->id . '/skus', $this->payload, $this->headers)
      ->assertStatus(201)
      ->assertJson([
          'data'   =>[
            'name' => 'Dove',
          ]
        ])
      ->assertJsonStructureExact([
          'data'   => [
            'name',
            'company_id',
            'product_id',
            'updated_at',
            'created_at',
            'id'
          ]
        ]);
  }

  // /** @test */
  // function list_of_skus()
  // {
  //   $this->json('GET', '/api/products/' . $this->product->id . '/skus',[], $this->headers)
  //     ->assertStatus(200)
  //     ->assertJsonStructure([
  //         'data' => [
  //           0=>[
  //             'name'
  //           ] 
  //         ]
  //       ]);
  //     $this->assertCount(1, Sku::all());
  // }

  /** @test */
  function show_single_sku()
  {
    $this->json('get', '/api/products/' . $this->product->id . '/skus/1', [], $this->headers)
      ->assertStatus(200)
      ->assertJson([
          'data'  => [
            'name'=> 'Santoor',
          ]
        ]);
  }

  /** @test */
  function update_single_sku()
  {
    $payload = [ 
      'name'  =>  'Santoor 1'
    ];

    $this->json('patch', '/api/products/' . $this->product->id . '/skus/1', $payload, $this->headers)
      ->assertStatus(200)
      ->assertJson([
          'data'    => [
            'name'  =>  'Santoor 1',
          ]
       ])
      ->assertJsonStructureExact([
          'data'  => [
            'id',
            'name',
            'created_at',
            'updated_at',
            'product_id',
            'company_id',
            'offer_id',
            'hsn_code',
            'gst_percent'
          ]
      ]);
  }
}
