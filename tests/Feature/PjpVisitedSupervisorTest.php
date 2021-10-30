<?php

namespace Tests\Feature;

use App\PjpSupervisor;
use App\PjpVisitedSupervisor;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PjpVisitedSupervisorTest extends TestCase
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

        factory(PjpVisitedSupervisor::class)->create([
            'company_id'  =>  $this->company->id
        ]);

        // $this->pjpSupervisor = factory(PjpSupervisor::class)->create([
        //     'company_id'  =>  $this->company->id
        // ]);

        $this->payload = [
            'pjp_supervisor_id' => 1,
            'visited_pjp_id' => 1,
            'visited_pjp_market_id' => 1,
            'remarks' => 'Remarks',
            'gps_address' => 'Gps Address',
        ];
    }

    /** @test */
    function it_requires_following_details()
    {
        $this->json('post', '/api/pjp_visited_supervisors/', [], $this->headers)
            ->assertStatus(422)
            ->assertExactJson([
                "errors"  =>  [
                    "pjp_supervisor_id"    =>  ["The pjp supervisor id field is required."],
                ],
                "message" =>  "The given data was invalid."
            ]);
    }

    /** @test */
    function add_new_pjp_market()
    {
        $this->disableEH();
        $this->json('post', '/api/pjp_visited_supervisors/', $this->payload, $this->headers)
            ->assertStatus(201)
            ->assertJson([
                'data'   => [
                    'pjp_supervisor_id' => 1,
                    'visited_pjp_id' => 1,
                    'visited_pjp_market_id' => 1,
                    'remarks' => 'Remarks',
                    'gps_address' => 'Gps Address',
                ]
            ])
            ->assertJsonStructureExact([
                'data'   => [
                    'pjp_supervisor_id',
                    'visited_pjp_id',
                    'visited_pjp_market_id',
                    'remarks',
                    'gps_address',
                    'company_id',
                    'updated_at',
                    'created_at',
                    'id'
                ]
            ]);
    }

    /** @test */
    function list_of_pjp_visited_supervisors()
    {
        // dd(1);
        $this->disableEH();
        $this->json('GET', '/api/pjp_visited_supervisors', [], $this->headers)
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    0 => [
                        'pjp_supervisor_id',
                    ]
                ]
            ]);
        $this->assertCount(1, PjpVisitedSupervisor::all());
    }

    /** @test */
    function show_single_visited_supervisor()
    {
        // dd(1);
        $this->json('get', '/api/pjp_visited_supervisors/1', [], $this->headers)
            ->assertStatus(200)
            ->assertJson([
                'data'  => [
                    'pjp_supervisor_id' => 1,
                    'visited_pjp_id' => 1,
                    'visited_pjp_market_id' => 1,
                    'remarks' => 'Remarks',
                    'gps_address' => 'Gps Address',
                ]
            ]);
    }

    /** @test */
    function update_single_visited_supervisor()
    {
        $payload = [
            'id'                =>  1,
            'pjp_supervisor_id' => 2,
            'visited_pjp_id' => 2,
            'visited_pjp_market_id' => 2,
            'remarks' => 'Remarks Updated',
            'gps_address' => 'Gps Address Updated',
        ];
        $this->disableEH();
        $this->json('post', '/api/pjp_visited_supervisors', $payload, $this->headers)
            ->assertStatus(201)
            ->assertJson([
                'data'    => [
                    'pjp_supervisor_id' => 2,
                    'visited_pjp_id' => 2,
                    'visited_pjp_market_id' => 2,
                    'remarks' => 'Remarks Updated',
                    'gps_address' => 'Gps Address Updated',
                ]
            ])
            ->assertJsonStructureExact([
                'data'  => [
                    'id',
                    'company_id',
                    'pjp_supervisor_id',
                    'visited_pjp_id',
                    'visited_pjp_market_id',
                    'remarks',
                    'gps_address',
                    'created_at',
                    'updated_at',
                    'is_visited'
                ]
            ]);
    }
}
