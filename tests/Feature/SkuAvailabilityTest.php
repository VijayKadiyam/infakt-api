<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\SkuAvailability;
use App\Sku;
use App\Product;

class SkuAvailabilityTest extends TestCase
{
  use DatabaseTransactions;

    public function setUp()
    {
        parent::setUp();

        $this->company = factory(\App\Company::class)->create([
            'name' => 'test'
        ]);
        $this->retailer = factory(\App\Retailer::class)->create();
        $this->referencePlan = factory(\App\ReferencePlan::class)->create([
            'company_id'    =>  $this->company->id
        ]);

        $this->user->assignCompany($this->company->id);
        $this->headers['company-id'] = $this->company->id;

        $this->product = factory(Product::class)->create([
          'company_id'  =>  $this->company->id,
        ]);

        $this->sku = factory(Sku::class)->create([
          'product_id'  =>  $this->product->id,
        ]);

        factory(\App\SkuAvailability::class)->create([
            'company_id'         =>  $this->company->id,
            'reference_plan_id'  => $this->referencePlan->id,
            'retailer_id'        => $this->retailer->id,
            'sku_id'             => $this->sku->id,
        ]);

        $this->payload = [
          'reference_plan_id'       =>   $this->referencePlan->id,
          'retailer_id'             =>   $this->retailer->id,
          'sku_id'                  => $this->sku->id,
          'date'                    =>   'Date 1',
        ];
    }
    /**
     * A basic feature test example.
     *
     * @return void
     */

    /** @test */
    function add_new_sku_availabilities()
    {
        $this->disableEH();
        $this->json('post', '/api/sku_availabilities', $this->payload, $this->headers)
            ->assertStatus(201)
            ->assertJson([
                'data'   => [
                    'reference_plan_id'       =>   $this->referencePlan->id,
                    'retailer_id'             =>   $this->retailer->id,
                    'sku_id'                  => $this->sku->id,
                    'date'                    =>   'Date 1',
                ]
            ])
            ->assertJsonStructureExact([
                'data'   => [
                    'reference_plan_id',
                    'retailer_id',
                    'sku_id',
                    'date',
                    'company_id',
                    'updated_at',
                    'created_at',
                    'id'
                ],
                'success'
            ]);
    }

    /** @test */
    function list_of_sku_availabilities()
    {
        $this->disableEH();
        $this->json('GET', '/api/sku_availabilities', [], $this->headers)
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    0 => [
                        'reference_plan_id',
                        'retailer_id',
                    ]
                ]
            ]);
        $this->assertCount(1, SkuAvailability::all());
    }

    /** @test */
    function show_single_sku_availabilities()
    {
        $this->disableEH();
        $this->json('get', "/api/sku_availabilities/1", [], $this->headers)
            ->assertStatus(200)
            ->assertJson([
                'data'  => [
                    'reference_plan_id'      =>   $this->referencePlan->id,
                    'retailer_id'            =>   $this->retailer->id,
                ]
            ]);
    }

    /** @test */
    function update_single_sku_availabilities()
    {
        $this->disableEH();
        $payload = [
            'reference_plan_id'      =>   $this->referencePlan->id,
            'retailer_id'            =>   $this->retailer->id,
            'date'                   =>   'Date 2'
        ];

        $this->json('patch', '/api/sku_availabilities/1', $payload, $this->headers)
            ->assertStatus(200)
            ->assertJson([
                'data'    => [
                    'reference_plan_id'      =>   $this->referencePlan->id,
                    'retailer_id'            =>   $this->retailer->id,
                    'date'                   =>   'Date 2'
                ]
            ])
            ->assertJsonStructureExact([
                'data'  => [
                    'id',
                    'company_id',
                    'reference_plan_id',
                    'retailer_id',
                    'sku_id',
                    'is_available',
                    'date',
                    'created_at',
                    'updated_at',
                ],
                'success'
            ]);
    }

    /** @test */
  function delete_single_sku_availabilities()
  {
    //   $this.disableEH();
    $this->json('delete', '/api/sku_availabilities/1', [], $this->headers)
      ->assertStatus(200);     
    $this->assertCount(0, SkuAvailability::all()); 
  }
}
