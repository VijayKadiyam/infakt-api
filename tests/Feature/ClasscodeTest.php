<?php

namespace Tests\Feature;

use App\Classcode;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ClasscodeTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp()
    {
        parent::setUp();
        $this->company = factory(\App\Company::class)->create([
            'name' => 'test'
        ]);

        $this->headers['company-id'] = $this->company->id;

        factory(\App\Classcode::class)->create([
            'company_id' => $this->company->id,
        ]);

        $this->payload = [
            'standard_id' => 1,
            'section_id' => 1,
            'subject_name' => 'subject_name',
            'classcode' => 'classcode',
            'is_active' => true,
            'is_optional' => false,
        ];
    }

    /** @test */
    function it_requires_classcode_name()
    {
        $this->json('post', '/api/classcodes', [], $this->headers)
            ->assertStatus(422)
            ->assertExactJson([
                "errors"  =>  [
                    "standard_id"  =>  ["The standard id field is required."],
                    "section_id"  =>  ["The section id field is required."],
                    "classcode"  =>  ["The classcode field is required."]
                ],
                "message" =>  "The given data was invalid."
            ]);
    }

    /** @test */
    function add_new_classcode()
    {
        $this->disableEH();
        $this->json('post', '/api/classcodes', $this->payload, $this->headers)
            ->assertStatus(201)
            ->assertJson([
                'data'  =>  [
                    'standard_id' => 1,
                    'section_id' => 1,
                    'subject_name' => 'subject_name',
                    'classcode' => 'classcode',
                    'is_active' => true,
                    'is_optional' => false,
                ]
            ])
            ->assertJsonStructureExact([
                'data'  =>  [
                    'standard_id',
                    'section_id',
                    'subject_name',
                    'classcode',
                    'is_active',
                    'is_optional',
                    'company_id',
                    'updated_at',
                    'created_at',
                    'id'
                ]
            ]);
    }

    /** @test */
    function list_of_classcodes()
    {
        $this->disableEH();
        $this->json('GET', '/api/classcodes', [], $this->headers)
            ->assertStatus(200)
            ->assertJsonStructure([
                'data'  =>  [
                    0 =>  [
                        'id',
                        'company_id',
                        'standard_id',
                        'section_id',
                        'subject_name',
                        'classcode',
                        'is_deleted',
                        'is_active',
                        'is_optional',
                        'created_at',
                        'updated_at'
                    ]
                ]
            ]);
        $this->assertCount(1, Classcode::all());
    }

    /** @test */
    function show_single_classcode()
    {
        $this->disableEH();
        $this->json('get', "/api/classcodes/1", [],  $this->headers)
            ->assertStatus(200)
            ->assertJson([
                'data'  => [
                    'standard_id' => 1,
                    'section_id' => 1,
                    'subject_name' => 'subject_name',
                    'classcode' => 'classcode',
                    'is_active' => true,
                    'is_optional' => false,
                ]
            ])
            ->assertJsonStructureExact([
                'data'    => [
                    'id',
                    'company_id',
                    'standard_id',
                    'section_id',
                    'subject_name',
                    'classcode',
                    'is_deleted',
                    'is_active',
                    'is_optional',
                    'created_at',
                    'updated_at'
                ]
            ]);
    }

    /** @test */
    function update_single_classcode()
    {
        $this->disableEH();
        $payload = [
            'standard_id' => 1,
            'section_id' => 1,
            'subject_name' => 'subject_name',
            'classcode' => 'classcode',
            'is_active' => true,
            'is_optional' => false,
        ];

        $this->json('patch', '/api/classcodes/1', $payload, $this->headers)
            ->assertStatus(200)
            ->assertJson([
                'data'    => [
                    'standard_id' => 1,
                    'section_id' => 1,
                    'subject_name' => 'subject_name',
                    'classcode' => 'classcode',
                    'is_active' => true,
                    'is_optional' => false,
                ]
            ])
            ->assertJsonStructureExact([
                'data'    => [
                    'id',
                    'company_id',
                    'standard_id',
                    'section_id',
                    'subject_name',
                    'classcode',
                    'is_deleted',
                    'is_active',
                    'is_optional',
                    'created_at',
                    'updated_at'
                ]
            ]);
    }

    /** @test */
    function delete_classcode()
    {
        $this->json('delete', '/api/classcodes/1', [], $this->headers)
            ->assertStatus(204);

        $this->assertCount(0, Classcode::all());
    }
}
