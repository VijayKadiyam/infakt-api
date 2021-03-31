<?php

namespace Tests\Feature;

use App\AssetStatus;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AssetStatusTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    use DatabaseTransactions;

    public function setUp()
    {
        parent::setUp();

        $this->company = factory(\App\Company::class)->create([
        'name' => 'test'
        ]);

        $this->user = factory(\App\User::class)->create();

        $this->user->assignCompany($this->company->id);
        $this->headers['company-id'] = $this->company->id;

        factory(\App\AssetStatus::class)->create([
        'company_id'   =>  $this->company->id,
        ]);

        $this->payload = [ 
        'user_id'            =>   $this->user->id,
        'asset_id'           =>   1,
        'status'             =>   'Status 1',
        'description'        =>   'Description 1',
        'date'               =>   'Date 1'
        ];
    }
    /**
     * A basic feature test example.
     *
     * @return void
     */
    /** @test */
  function list_of_asset_status()
  {
    $this->disableEH();
    $this->json('GET', '/api/asset_statuses',[], $this->headers)
      ->assertStatus(200)
      ->assertJsonStructure([
          'data' => [
            0=>[
              'user_id',
              'asset_id',
              'status',
              'description',
              'date'
            ] 
          ]
        ]);
      $this->assertCount(1, AssetStatus::all());
  }

  /** @test */
  function add_new_asset_status()
  {
    $this->disableEH();
    $this->json('post', '/api/asset_statuses', $this->payload, $this->headers)
      ->assertStatus(201)
      ->assertJson([
          'data'   =>[
            'asset_id'           =>   1,
            'status'             =>   'Status 1',
            'description'        =>   'Description 1',
            'date'               =>   'Date 1'
          ]
        ])
      ->assertJsonStructureExact([
          'data'   => [
            'user_id',
            'asset_id',
            'status',
            'description',
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
  function show_single_asset_status()
  {
    $this->disableEH();
    $this->json('get', "/api/asset_statuses/1", [], $this->headers)
      ->assertStatus(200)
      ->assertJson([
          'data'  => [
            'user_id'            =>   1,
            'asset_id'           =>   1,
            'status'             =>   'Status 1',
            'description'        =>   'Description 1',
            'date'               =>   'Date 1',
          ]
        ]);
  }

  /** @test */
  function update_single_asset_status()
  {
    $this->disableEH();
    $payload = [ 
        'user_id'            =>   $this->user->id,
        'asset_id'           =>   1,
        'status'             =>   'Status 2',
        'description'        =>   'Description 2',
        'date'               =>   'Date 2'
    ];

    $this->json('patch', '/api/asset_statuses/1', $payload, $this->headers)
      ->assertStatus(200)
      ->assertJson([
          'data'    => [
            'user_id'            =>   $this->user->id,
            'asset_id'           =>   1,
            'status'             =>   'Status 2',
            'description'        =>   'Description 2',
            'date'               =>   'Date 2'
          ]
       ])
      ->assertJsonStructureExact([
          'data'  => [
            'id',
            'company_id',
            'user_id',
            'asset_id',
            'status',
            'description',
            'date',
            'created_at',
            'updated_at',
          ],
          'success'
      ]);
  }

  /** @test */
  function delete_single_asset()
  {
    // $this.disableEH();
    $this->json('delete', '/api/asset_statuses/1', [], $this->headers)
      ->assertStatus(200);     
    $this->assertCount(0, AssetStatus::all()); 
  }
}
