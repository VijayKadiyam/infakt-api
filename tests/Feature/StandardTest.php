<?php

namespace Tests\Feature;

use App\Standard;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class StandardTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp()
    {
        parent::setUp();
        $this->company = factory(\App\Company::class)->create([
            'name' => 'test'
        ]);

        $this->headers['company-id'] = $this->company->id;

        factory(\App\Standard::class)->create([
            'company_id' => $this->company->id,
        ]);

        $this->payload = [
            'name' => "name",
            'is_active' => 1,
        ];
    }

    /** @test */
    function it_requires_standard_name()
    {
        $this->json('post', '/api/standards', [], $this->headers)
            ->assertStatus(422)
            ->assertExactJson([
                "errors"  =>  [
                    "name"  =>  ["The name field is required."]
                ],
                "message" =>  "The given data was invalid."
            ]);
    }

    /** @test */
    function add_new_standard()
    {
        $this->disableEH();
        $this->json('post', '/api/standards', $this->payload, $this->headers)
            ->assertStatus(201)
            ->assertJson([
                'data'  =>  [
                    'name' => "name",
                    'is_active' => 1,
                ]
            ])
            ->assertJsonStructureExact([
                'data'  =>  [
                    'name',
                    'is_active',
                    'company_id',
                    'updated_at',
                    'created_at',
                    'id'
                ]
            ]);
    }

    /** @test */
    function list_of_standards()
    {
        $this->disableEH();
        $this->json('GET', '/api/standards', [], $this->headers)
            ->assertStatus(200)
            ->assertJsonStructure([
                'data'  =>  [
                    0 =>  [
                        'name',
                        'is_active'
                    ]
                ]
            ]);
        $this->assertCount(1, Standard::all());
    }

    /** @test */
    function show_single_standard()
    {
        $this->disableEH();
        $this->json('get', "/api/standards/1", [],  $this->headers)
            ->assertStatus(200)
            ->assertJson([
                'data'  => [
                    'name' => "name",
                    'is_active' => 1,
                ]
            ])
            ->assertJsonStructureExact([
                'data'    => [
                    'id',
                    'company_id',
                    'name',
                    'is_active',
                    'is_deleted',
                    'created_at',
                    'updated_at'
                ]
            ]);
    }

    /** @test */
    function update_single_standard()
    {
        $this->disableEH();
        $payload = [
            'name' => "name",
            'is_active' => 1,
        ];

        $this->json('patch', '/api/standards/1', $payload, $this->headers)
            ->assertStatus(200)
            ->assertJson([
                'data'    => [
                    'name' => "name",
                    'is_active' => 1,
                ]
            ])
            ->assertJsonStructureExact([
                'data'    => [
                    'id',
                    'company_id',
                    'name',
                    'is_active',
                    'is_deleted',
                    'created_at',
                    'updated_at'
                ]
            ]);
    }

    /** @test */
    function delete_standard()
    {
        $this->json('delete', '/api/standards/1', [], $this->headers)
            ->assertStatus(204);

        $this->assertCount(0, Standard::all());
    }
}
