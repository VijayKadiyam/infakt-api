<?php

namespace Tests\Feature;

use App\Pjp;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PjpTest extends TestCase
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

        factory(Pjp::class)->create([
            'company_id'  =>  $this->company->id
        ]);

        $this->payload = [
            'location' => 'location',
            'region' => 'region',
        ];
    }

    /** @test */
    function it_requires_following_details()
    {
        $this->json('post', '/api/pjps', [], $this->headers)
            ->assertStatus(422)
            ->assertExactJson([
                "errors"  =>  [
                    "location"    =>  ["The location field is required."]
                ],
                "message" =>  "The given data was invalid."
            ]);
    }

    /** @test */
    function add_new_channel_filter()
    {
        $this->json('post', '/api/pjps', $this->payload, $this->headers)
            ->assertStatus(201)
            ->assertJson([
                'data'   => [
                    'location' => 'location',
                    'region' => 'region',
                ]
            ])
            ->assertJsonStructureExact([
                'data'   => [
                    'location',
                    'region',
                    'company_id',
                    'updated_at',
                    'created_at',
                    'id'
                ]
            ]);
    }

    /** @test */
    function list_of_pjps()
    {
        $this->json('GET', '/api/pjps', [], $this->headers)
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    0 => [
                        'location'
                    ]
                ]
            ]);
        $this->assertCount(1, Pjp::all());
    }

    /** @test */
    function show_single_channel_filter()
    {
        $this->json('get', "/api/pjps/1", [], $this->headers)
            ->assertStatus(200)
            ->assertJson([
                'data'  => [
                    'location' => 'location',
                    'region' => 'region',
                ]
            ]);
    }

    /** @test */
    function update_single_channel_filter()
    {
        $payload = [
            'location' => 'location',
            'region' => 'region',
        ];

        $this->json('patch', '/api/pjps/1', $payload, $this->headers)
            ->assertStatus(200)
            ->assertJson([
                'data'    => [
                    'location' => 'location',
                    'region' => 'region',
                ]
            ])
            ->assertJsonStructureExact([
                'data'  => [
                    'id',
                    'company_id',
                    'location',
                    'region',
                    'created_at',
                    'updated_at',
                    'remarks',
                ]
            ]);
    }
}
