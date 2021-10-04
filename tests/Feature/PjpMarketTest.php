<?php

namespace Tests\Feature;

use App\PjpMarket;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PjpMarketTest extends TestCase
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

        factory(PjpMarket::class)->create([
            'company_id'  =>  $this->company->id
        ]);

        $this->payload = [
            'pjp_id' => 1,
            'market_name' => 'Market Name',
            'gps_address' => 'Gps Address',
        ];
    }

    /** @test */
    function it_requires_following_details()
    {
        $this->json('post', '/api/pjp_markets', [], $this->headers)
            ->assertStatus(422)
            ->assertExactJson([
                "errors"  =>  [
                    "pjp_id"    =>  ["The pjp id field is required."],
                    "market_name"    =>  ["The market name field is required."]
                ],
                "message" =>  "The given data was invalid."
            ]);
    }

    /** @test */
    function add_new_pjp_market()
    {
        $this->json('post', '/api/pjp_markets', $this->payload, $this->headers)
            ->assertStatus(201)
            ->assertJson([
                'data'   => [
                    'pjp_id' => 1,
                    'market_name' => 'Market Name',
                    'gps_address' => 'Gps Address',
                ]
            ])
            ->assertJsonStructureExact([
                'data'   => [
                    'pjp_id',
                    'market_name',
                    'gps_address',
                    'company_id',
                    'updated_at',
                    'created_at',
                    'id'
                ]
            ]);
    }

    /** @test */
    function list_of_pjp_markets()
    {
        $this->json('GET', '/api/pjp_markets', [], $this->headers)
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    0 => [
                        'pjp_id'
                    ]
                ]
            ]);
        $this->assertCount(1, PjpMarket::all());
    }

    /** @test */
    function show_single_channel_filter()
    {
        $this->json('get', "/api/pjp_markets/1", [], $this->headers)
            ->assertStatus(200)
            ->assertJson([
                'data'  => [
                    'pjp_id' => 1,
                    'market_name' => 'Market Name',
                    'gps_address' => 'Gps Address',
                ]
            ]);
    }

    /** @test */
    function update_single_channel_filter()
    {
        $payload = [
            'pjp_id' => 2,
            'market_name' => 'Market Name Updated',
            'gps_address' => 'Gps Address Updated',
        ];

        $this->json('patch', '/api/pjp_markets/1', $payload, $this->headers)
            ->assertStatus(200)
            ->assertJson([
                'data'    => [
                    'pjp_id' => 2,
                    'market_name' => 'Market Name Updated',
                    'gps_address' => 'Gps Address Updated',
                ]
            ])
            ->assertJsonStructureExact([
                'data'  => [
                    'id',
                    'company_id',
                    'pjp_id',
                    'market_name',
                    'gps_address',
                    'created_at',
                    'updated_at'
                ]
            ]);
    }
}
