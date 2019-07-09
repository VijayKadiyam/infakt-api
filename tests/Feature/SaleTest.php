<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Sku;
use App\SkuType;
use App\Stock;
use App\Sale;
use App\OfferType;
use App\Offer;
use App\Retailer;
use App\Product;

class SaleTest extends TestCase
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

    $this->sku = factory(Sku::class)->create([
      'product_id'  =>  $this->product->id,
    ]);

    $this->skuType = factory(SkuType::class)->create([
      'company_id'  =>  $this->company->id 
    ]);

    $this->offerType = factory(OfferType::class)->create([
      'company_id'  =>  $this->company->id 
    ]);

    $this->offer = factory(Offer::class)->create([
      'company_id'  =>  $this->company->id,
      'offer_type_id' =>  $this->offerType->id
    ]);

    $this->stock = factory(Stock::class)->create([
      'sku_id'  =>  $this->sku->id,
      'sku_type_id' =>  $this->skuType->id,
     'offer_id'    =>  $this->offer->id,
      'price'       =>  '200'   ]);

    $this->referencePlan = factory(\App\ReferencePlan::class)->create([
      'company_id'  =>  $this->company->id 
    ]);

    $this->retailer = factory(Retailer::class)->create([
      'reference_plan_id'  =>  $this->referencePlan->id 
    ]);

    factory(Sale::class)->create([
      'sku_id'  =>  $this->sku->id,
      'qty' =>  20,
      'retailer_id' =>  $this->retailer->id
    ]);

    $this->payload = [ 
      'sku_id'  =>  $this->sku->id,
      'qty'     =>  20,
      'retailer_id' =>  $this->retailer->id
    ];
  }

  /** @test */
  function user_must_be_logged_in_before_accessing_the_controller()
  {
    $this->json('post', '/api/skus/' . $this->sku->id . '/sales')
      ->assertStatus(401); 
  }

  /** @test */
  function it_requires_following_details()
  {
    $this->json('post', '/api/skus/' . $this->sku->id . '/sales', [], $this->headers)
      ->assertStatus(422)
      ->assertExactJson([
          "errors"  =>  [
            "qty"    =>  ["The qty field is required."],
            'sku_id' =>  ["The sku id field is required."],
            'retailer_id' =>  ["The retailer id field is required."]
          ],
          "message" =>  "The given data was invalid."
        ]);
  }

  /** @test */
  function add_new_sale()
  {
    $this->disableEH();
    $this->json('post', '/api/skus/' . $this->sku->id . '/sales', $this->payload, $this->headers)
      ->assertStatus(201)
      ->assertJson([
          'data'   =>[
            'qty' => 20,
          ]
        ])
      ->assertJsonStructureExact([
          'data'   => [
            'qty',
            'retailer_id',
            'sku_id',
            'updated_at',
            'created_at',
            'id'
          ],
          'success'
        ]);
  }

  /** @test */
  function list_of_sales()
  {
    $this->disableEH();
    $this->json('GET', '/api/skus/' . $this->sku->id . '/sales',[], $this->headers)
      ->assertStatus(200)
      ->assertJsonStructure([
          'data' => [
            0=>[
              'qty'
            ] 
          ]
        ]);

      $this->assertCount(1, Sale::all());
  }

  /** @test */
  function list_of_sales_of_a_month()
  {
    $this->disableEH();
    $this->json('GET', '/api/skus/' . $this->sku->id . '/sales',[], $this->headers)
      ->assertStatus(200)
      ->assertJsonStructure([
          'data' => [
            0=>[
              'qty'
            ] 
          ]
        ]);

      $this->assertCount(1, Sale::all());
  }

  /** @test */
  function show_single_sale()
  {
    $this->json('get', '/api/skus/' . $this->sku->id . '/sales/1', [], $this->headers)
      ->assertStatus(200)
      ->assertJson([
          'data'  => [
            'qty'=> '20',
          ]
        ]);
  }

  /** @test */
  function update_single_sale()
  {
    $payload = [ 
      'qty'  =>  '21'
    ];

    $this->json('patch', '/api/skus/' . $this->sku->id . '/sales/1', $payload, $this->headers)
      ->assertStatus(200)
      ->assertJson([
          'data'    => [
            'qty'  =>  '21',
          ]
       ])
      ->assertJsonStructureExact([
          'data'  => [
            'id',
            'qty',
            'retailer_id',
            'created_at',
            'updated_at',
            'sku_id',
          ],
          'success'
      ]);
  }
}
