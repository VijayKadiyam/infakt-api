<?php

namespace Tests\Feature;

use App\Section;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SectionTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp()
    {
        parent::setUp();
        $this->company = factory(\App\Company::class)->create([
            'name' => 'test'
        ]);

        $this->headers['company-id'] = $this->company->id;

        factory(\App\Section::class)->create([
            'company_id' => $this->company->id,
        ]);

        $this->payload = [
            'name'             => 'name',
            'standard_id'      => 1,
            'is_active'        => true,
        ];
    }

    /** @test */
    function it_requires_section_name()
    {
        $this->json('post', '/api/sections', [], $this->headers)
            ->assertStatus(422)
            ->assertExactJson([
                "errors"  =>  [
                    "name"  =>  ["The name field is required."],
                    "standard_id"  =>  ["The standard id field is required."]
                ],
                "message" =>  "The given data was invalid."
            ]);
    }

    /** @test */
    function add_new_section()
    {
        $this->disableEH();
        $this->json('post', '/api/sections', $this->payload, $this->headers)
            ->assertStatus(201)
            ->assertJson([
                'data'  =>  [
                    'name'             => 'name',
                    'standard_id'       => 1,
                    'is_active'        => true,
                ]
            ])
            ->assertJsonStructureExact([
                'data'  =>  [
                    'name',
                    'standard_id',
                    'is_active',
                    'company_id',
                    'updated_at',
                    // 'is_deleted',
                    'created_at',
                    'id'
                ]
            ]);
    }

    /** @test */
    function list_of_sections()
    {
        $this->disableEH();
        $this->json('GET', '/api/sections', [], $this->headers)
            ->assertStatus(200)
            ->assertJsonStructure([
                'data'  =>  [
                    0 =>  [
                        'name',
                        'standard_id',
                        'is_active',
                        'is_deleted',
                    ]
                ]
            ]);
        $this->assertCount(1, Section::all());
    }

    /** @test */
    function show_single_section()
    {
        $this->disableEH();
        $this->json('get', "/api/sections/1", [],  $this->headers)
            ->assertStatus(200)
            ->assertJson([
                'data'  => [
                    'name'             => 'name',
                    'standard_id'      => 1,
                    'is_active'        => true,
                ]
            ])
            ->assertJsonStructureExact([
                'data'    => [
                    'id',
                    'company_id',
                    'standard_id',
                    'name',
                    'is_active',
                    'is_deleted',
                    'created_at',
                    'updated_at'
                ]
            ]);
    }

    /** @test */
    function update_single_section()
    {
        $this->disableEH();
        $payload = [
            'name'             => 'name',
            'standard_id'      => 1,
            'is_active'        => true,
        ];

        $this->json('patch', '/api/sections/1', $payload, $this->headers)
            ->assertStatus(200)
            ->assertJson([
                'data'    => [
                    'name'             => 'name',
                    'standard_id'      => 1,
                    'is_active'        => true,
                ]
            ])
            ->assertJsonStructureExact([
                'data'    => [
                    'id',
                    'company_id',
                    'standard_id',
                    'name',
                    'is_active',
                    'is_deleted',
                    'created_at',
                    'updated_at'
                ]
            ]);
    }

    /** @test */
    function delete_section()
    {
        $this->json('delete', '/api/sections/1', [], $this->headers)
            ->assertStatus(204);

        $this->assertCount(0, Section::all());
    }
}
