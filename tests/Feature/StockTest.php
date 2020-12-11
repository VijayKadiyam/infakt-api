<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Sku;
use App\SkuType;
use App\Stock;
use App\OfferType;
use App\Offer;
use App\Product;

class StockTest extends TestCase
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

    $this->skuType = factory(SkuType::class)->create([
      'company_id'  =>  $this->company->id 
    ]);

    $this->product = factory(Product::class)->create([
      'company_id'  =>  $this->company->id,
    ]);

    $this->sku = factory(Sku::class)->create([
      'product_id'  =>  $this->product->id,
    ]);

    $this->offerType = factory(OfferType::class)->create([
      'company_id'  =>  $this->company->id 
    ]);

    $this->offer = factory(Offer::class)->create([
      'company_id'  =>  $this->company->id,
      'offer_type_id' =>  $this->offerType->id
    ]);

    factory(Stock::class)->create([
      'sku_id'  =>  $this->sku->id,
      'sku_type_id' =>  $this->skuType->id,
      'offer_id'    =>  $this->offer->id,
      'price'       =>  '200'
    ]);

    $this->payload = [ 
      'sku_id'  =>  $this->sku->id,
      'sku_type_id' =>  $this->skuType->id,
      'offer_id'    =>  $this->offer->id,
      'price'       =>  '201',
      'qty'     =>  20,
      'invoice_no'  =>  'no'
    ];
  }

  /** @test */
  function user_must_be_logged_in_before_accessing_the_controller()
  {
    $this->json('post', '/api/skus/' . $this->sku->id . '/stocks')
      ->assertStatus(401); 
  }

  /** @test */
  function it_requires_following_details()
  {
    $this->json('post', '/api/skus/' . $this->sku->id . '/stocks', [], $this->headers)
      ->assertStatus(422)
      ->assertExactJson([
          "errors"  =>  [
            "qty"    =>  ["The qty field is required."],
            'sku_type_id' =>  ["The sku type id field is required."],
            'offer_id'  =>  ["The offer id field is required."],
            'price' =>  ["The price field is required."]
          ],
          "message" =>  "The given data was invalid."
        ]);
  }

  /** @test */
  function add_new_stock()
  {
    $this->disableEH();
    $this->json('post', '/api/skus/' . $this->sku->id . '/stocks', $this->payload, $this->headers)
      ->assertStatus(201)
      ->assertJson([
          'data'   =>[
            'qty' => 20,
          ]
        ])
      ->assertJsonStructureExact([
          'data'   => [
            'sku_type_id',
            'offer_id',
            'price',
            'qty',
            'invoice_no',
            'sku_id',
            'updated_at',
            'created_at',
            'id'
          ]
        ]);
  }

  /** @test */
  function list_of_skus()
  {
    $this->json('GET', '/api/skus/' . $this->sku->id . '/stocks',[], $this->headers)
      ->assertStatus(200)
      ->assertJsonStructure([
          'data' => [
            0=>[
              'qty'
            ] 
          ]
        ]);
      $this->assertCount(1, Stock::all());
  }

  /** @test */
  function show_single_sku()
  {
    $this->json('get', '/api/skus/' . $this->sku->id . '/stocks/1', [], $this->headers)
      ->assertStatus(200)
      ->assertJson([
          'data'  => [
            'qty'=> '20',
          ]
        ]);
  }

  /** @test */
  function update_single_stock()
  {
    $payload = [ 
      'qty'  =>  '21'
    ];

    $this->json('patch', '/api/skus/' . $this->sku->id . '/stocks/1', $payload, $this->headers)
      ->assertStatus(200)
      ->assertJson([
          'data'    => [
            'qty'  =>  '21',
          ]
       ])
      ->assertJsonStructureExact([
          'data'  => [
            'id',
            'sku_id',
            'sku_type_id',
            'qty',
            'offer_id',
            'price',
            'created_at',
            'updated_at',
            'invoice_no',
            'unit_id',
            'total',
            'distributor_id',
            'date'
          ]
      ]);
  }
}
