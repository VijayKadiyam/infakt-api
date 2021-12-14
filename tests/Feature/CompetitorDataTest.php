<?php

namespace Tests\Feature;

use App\CompetitorData;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CompetitorDataTest extends TestCase
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

        factory(CompetitorData::class)->create([
            'company_id'  =>  $this->company->id
        ]);

        $this->payload = [
            'user_id' => 1,
            'competitor' => 'competitor',
            'amount' => 'amount',
            'month' => 'month',
            'year' => 'year',
        ];
    }

    /** @test */
    function it_requires_following_details()
    {
        $this->json('post', '/api/competitor_datas', [], $this->headers)
            ->assertStatus(422)
            ->assertExactJson([
                "errors"  =>  [
                    "user_id"    =>  ["The user id field is required."]
                ],
                "message" =>  "The given data was invalid."
            ]);
    }

    /** @test */
    function add_new_competitor_data()
    {
        $this->json('post', '/api/competitor_datas', $this->payload, $this->headers)
            ->assertStatus(201)
            ->assertJson([
                'data'   => [
                    'user_id' => 1,
                    'competitor' => 'competitor',
                    'amount' => 'amount',
                    'month' => 'month',
                    'year' => 'year',
                ]
            ])
            ->assertJsonStructureExact([
                'data'   => [
                    'user_id',
                    'competitor',
                    'amount',
                    'month',
                    'year',
                    'company_id',
                    'updated_at',
                    'created_at',
                    'id'
                ]
            ]);
    }

    /** @test */
    function list_of_competitor_datas()
    {
        $this->json('GET', '/api/competitor_datas', [], $this->headers)
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    0 => [
                        'user_id'
                    ]
                ]
            ]);
        $this->assertCount(1, CompetitorData::all());
    }

    /** @test */
    function show_single_competitor_data()
    {
        $this->json('get', "/api/competitor_datas/1", [], $this->headers)
            ->assertStatus(200)
            ->assertJson([
                'data'  => [
                    'user_id' => 1,
                    'competitor' => 'competitor',
                    'amount' => 'amount',
                    'month' => 'month',
                    'year' => 'year',
                ]
            ]);
    }

    /** @test */
    function update_single_competitor_data()
    {
        $payload = [
            'user_id' => 1,
            'competitor' => 'competitor',
            'amount' => 'amount',
            'month' => 'month',
            'year' => 'year',
        ];

        $this->json('patch', '/api/competitor_datas/1', $payload, $this->headers)
            ->assertStatus(200)
            ->assertJson([
                'data'    => [
                    'user_id' => 1,
                    'competitor' => 'competitor',
                    'amount' => 'amount',
                    'month' => 'month',
                    'year' => 'year',
                ]
            ])
            ->assertJsonStructureExact([
                'data'  => [
                    'id',
                    'company_id',
                    'user_id',
                    'competitor',
                    'amount',
                    'month',
                    'year',
                    'created_at',
                    'updated_at',
                ]
            ]);
    }
}
