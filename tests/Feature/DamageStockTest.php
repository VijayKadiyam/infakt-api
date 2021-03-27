<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\DamageStock;
use Carbon\Carbon;

class DamageStockTest extends TestCase
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

        factory(\App\DamageStock::class)->create([
        'company_id'  =>  $this->company->id 
        ]);

        $this->payload = [ 
          'company_id'             => $this->company->id,
          'qty'                    => 1.0,
          'mrp'                    => 100.0,
          'manufacturing_date'     => 'Date 1',
          'sku_id'                 => 1,
        ];
    }
    /**
     * A basic feature test example.
     *
     * @return void
     */

  /** @test */
  function list_of_damage_stocks()
  {
    $this->disableEH();
    $this->json('GET', '/api/damage_stocks',[], $this->headers)
      ->assertStatus(200)
      ->assertJsonStructure([
          'data' => [
            0=>[
              'qty',
              'mrp',
              'manufacturing_date',
              'sku_id',
            ] 
          ]
        ]);
      $this->assertCount(1, DamageStock::all());
  }

  /** @test */
  function list_of_damage_stocks_by_date()
  {
    // $this->disableEH();
    $date = Carbon::now()->format('Y-m-d');
    $this->json('GET', '/api/damage_stocks/?search='. $date,[], $this->headers)
      ->assertStatus(200)
      ->assertJsonStructure([
          'data' => [
            0=>[
              'qty',
              'mrp',
              'manufacturing_date'
            ] 
          ]
        ]);
      $this->assertCount(1, DamageStock::all());
  }

  /** @test */
  function add_new_damage_stock()
  {
    $this->disableEH();
    $this->json('post', '/api/damage_stocks', $this->payload, $this->headers)
      ->assertStatus(201)
      ->assertJson([
          'data'   =>[
            'qty'                   => 1.0,
            'mrp'                   => 100.0,
            'manufacturing_date'    => 'Date 1',
            'sku_id'                => 1,
          ]
        ])
      ->assertJsonStructureExact([
          'data'   => [
            'qty',
            'mrp',
            'manufacturing_date',
            'sku_id',
            'company_id',
            'updated_at' ,
            'created_at',
            'id'
          ],
          'success'
        ]);
  }

  /** @test */
  function show_single_damage_stock()
  {
    $this->disableEH();
    $this->json('get', "/api/damage_stocks/1", [], $this->headers)
      ->assertStatus(200)
      ->assertJson([
          'data'  => [
            'qty'                => 1.0,
            'mrp'                => 100.0,
            'manufacturing_date' => 'Date 1',
            'sku_id'             => 1,
          ]
        ]);
  }

  /** @test */
  function update_single_damage_stock()
  {
    $this->disableEH();
    $payload = [ 
        'qty'                    =>   11.0,
        'mrp'                    =>   101.0,
        'manufacturing_date'     =>  'Date 2',
        'sku_id'                 =>   2,
    ];

    $this->json('patch', '/api/damage_stocks/1', $payload, $this->headers)
      ->assertStatus(200)
      ->assertJson([
          'data'    => [
            'qty'                    =>   11.0,
            'mrp'                    =>   101.0,
            'manufacturing_date'     =>  'Date 2',
            'sku_id'                 =>   2,
          ]
       ])
      ->assertJsonStructureExact([
          'data'  => [
            'id',
            'company_id',
            'qty',
            'mrp',
            'manufacturing_date',
            'created_at',
            'updated_at',
            'sku_id',
            'reference_plan_id',
            'retailer_id'
          ],
          'success'
      ]);
  }

  /** @test */
  function delete_single_damage_stock()
  {
    //   $this.disableEH();
    $this->json('delete', '/api/damage_stocks/1', [], $this->headers)
      ->assertStatus(200);     
    $this->assertCount(0, DamageStock::all());
  }
}