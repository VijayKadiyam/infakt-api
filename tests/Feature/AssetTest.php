<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Asset;

class AssetTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp()
    {
        parent::setUp();

        $this->company = factory(\App\Company::class)->create([
        'name' => 'test'
        ]);

        $this->retailer = factory(\App\Retailer::class)->create();
        // dd($this->retailer->id);

        $this->user->assignCompany($this->company->id);
        $this->headers['company-id'] = $this->company->id;

        factory(\App\Asset::class)->create([
        'company_id'   =>  $this->company->id,
        'retailer_id'  => $this->retailer->id 
        ]);

        $this->payload = [ 
        'company_id'             =>   $this->company->id,
        'retailer_id'            =>   $this->retailer->id,
        'asset_name'             =>   'Asset1',
        'status'                 =>   'Status1',
        'description'            =>   'Description1',
        'reference_plan_id'      =>   1
        ];
    }
    /**
     * A basic feature test example.
     *
     * @return void
     */
    /** @test */
  function list_of_asset()
  {
    $this->disableEH();
    $this->json('GET', '/api/assets',[], $this->headers)
      ->assertStatus(200)
      ->assertJsonStructure([
          'data' => [
            0=>[
              'asset_name',
              'status',
              'description',
              'retailer_id',
              'reference_plan_id'
            ] 
          ]
        ]);
      $this->assertCount(1, Asset::all());
  }

  /** @test */
  function add_new_asset()
  {
    // $this->disableEH();
    $this->json('post', '/api/assets', $this->payload, $this->headers)
      ->assertStatus(201)
      ->assertJson([
          'data'   =>[
            'retailer_id'        => $this->retailer->id,
            'asset_name'         => 'Asset1',
            'status'             => 'Status1',
            'description'        => 'Description1',
            'reference_plan_id'  => 1
          ]
        ])
      ->assertJsonStructureExact([
          'data'   => [
            'company_id',
            'retailer_id',
            'asset_name',
            'status',
            'description',
            'reference_plan_id',
            'updated_at',
            'created_at',
            'id'
          ],
          'success'
        ]);
  }

  /** @test */
  function show_single_asset()
  {
    $this->disableEH();
    $this->json('get', "/api/assets/1", [], $this->headers)
      ->assertStatus(200)
      ->assertJson([
          'data'  => [
            'retailer_id'        => $this->retailer->id,
            'asset_name'         => 'Asset1',
            'status'             => 'Status1',
            'description'        => 'Description1',
            'reference_plan_id'  => 1
          ]
        ]);
  }

  /** @test */
  function update_single_asset()
  {
    $this->disableEH();
    $payload = [ 
        'retailer_id'        => $this->retailer->id,
        'asset_name'         => 'Asset2',
        'status'             => 'Status2',
        'description'        => 'Description2',
        'reference_plan_id'  => 2
    ];

    $this->json('patch', '/api/assets/1', $payload, $this->headers)
      ->assertStatus(200)
      ->assertJson([
          'data'    => [
            'retailer_id'        => $this->retailer->id,
            'asset_name'         => 'Asset2',
            'status'             => 'Status2',
            'description'        => 'Description2',
            'reference_plan_id'  => 2
          ]
       ])
      ->assertJsonStructureExact([
          'data'  => [
            'id',
            'company_id',
            'retailer_id',
            'asset_name',
            'status',
            'description',
            'created_at',
            'updated_at',
            'reference_plan_id',
            'unique_id',
            'size',
            'manufacturer_id',
          ],
          'success'
      ]);
  }

  /** @test */
  function delete_single_asset()
  {
    //   $this.disableEH();
    $this->json('delete', '/api/assets/1', [], $this->headers)
      ->assertStatus(200);     
    $this->assertCount(0, Asset::all()); 
  }
}
