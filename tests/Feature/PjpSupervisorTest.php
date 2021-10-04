<?php

namespace Tests\Feature;

use App\PjpSupervisor;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PjpSupervisorTest extends TestCase
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

        factory(PjpSupervisor::class)->create([
            'company_id'  =>  $this->company->id
        ]);

        $this->payload = [
            'user_id' => 1,
            'date' => 'date',
            'actual_pjp_id' => 1,
            'actual_pjp_market_id' => 1,
            'visited_pjp_id' => 1,
            'visited_pjp_market_id' => 1,
            'gps_address' => 'gps_address',
            'remarks' => 'remarks',
        ];
    }

    /** @test */
    function it_requires_following_details()
    {
        $this->json('post', '/api/pjp_supervisors', [], $this->headers)
            ->assertStatus(422)
            ->assertExactJson([
                "errors"  =>  [
                    "user_id"    =>  ["The user id field is required."]
                ],
                "message" =>  "The given data was invalid."
            ]);
    }

    /** @test */
    function add_new_pjp_supervisor()
    {
        $this->json('post', '/api/pjp_supervisors', $this->payload, $this->headers)
            ->assertStatus(201)
            ->assertJson([
                'data'   => [
                    'user_id' => 1,
                    'date' => 'date',
                    'actual_pjp_id' => 1,
                    'actual_pjp_market_id' => 1,
                    'visited_pjp_id' => 1,
                    'visited_pjp_market_id' => 1,
                    'gps_address' => 'gps_address',
                    'remarks' => 'remarks',
                ]
            ])
            ->assertJsonStructureExact([
                'data'   => [
                    'user_id',
                    'date',
                    'actual_pjp_id',
                    'actual_pjp_market_id',
                    'visited_pjp_id',
                    'visited_pjp_market_id',
                    'gps_address',
                    'remarks',
                    'company_id',
                    'updated_at',
                    'created_at',
                    'id'
                ]
            ]);
    }

    /** @test */
    function list_of_pjp_supervisors()
    {
        $this->json('GET', '/api/pjp_supervisors', [], $this->headers)
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    0 => [
                        'user_id'
                    ]
                ]
            ]);
        $this->assertCount(1, PjpSupervisor::all());
    }

    /** @test */
    function show_single_channel_filter()
    {
        $this->json('get', "/api/pjp_supervisors/1", [], $this->headers)
            ->assertStatus(200)
            ->assertJson([
                'data'  => [
                    'user_id' => 1,
                    'date' => 'date',
                    'actual_pjp_id' => 1,
                    'actual_pjp_market_id' => 1,
                    'visited_pjp_id' => 1,
                    'visited_pjp_market_id' => 1,
                    'gps_address' => 'gps_address',
                    'remarks' => 'remarks',
                ]
            ]);
    }

    /** @test */
    function update_single_channel_filter()
    {
        $payload = [
            'user_id' => 1,
            'date' => 'date',
            'actual_pjp_id' => 1,
            'actual_pjp_market_id' => 1,
            'visited_pjp_id' => 1,
            'visited_pjp_market_id' => 1,
            'gps_address' => 'gps_address',
            'remarks' => 'remarks',
        ];

        $this->json('patch', '/api/pjp_supervisors/1', $payload, $this->headers)
            ->assertStatus(200)
            ->assertJson([
                'data'    => [
                    'user_id' => 1,
                    'date' => 'date',
                    'actual_pjp_id' => 1,
                    'actual_pjp_market_id' => 1,
                    'visited_pjp_id' => 1,
                    'visited_pjp_market_id' => 1,
                    'gps_address' => 'gps_address',
                    'remarks' => 'remarks',
                ]
            ])
            ->assertJsonStructureExact([
                'data'  => [
                    'id',
                    'company_id',
                    'user_id',
                    'date',
                    'actual_pjp_id',
                    'actual_pjp_market_id',
                    'visited_pjp_id',
                    'visited_pjp_market_id',
                    'gps_address',
                    'remarks',
                    'created_at',
                    'updated_at'
                ]
            ]);
    }
}
