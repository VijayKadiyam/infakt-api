<?php

namespace Tests\Feature;

use App\Subject;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SubjectTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp()
    {
        parent::setUp();

        $this->payload = [
            'name' => "name",
            'is_active' => 1,
        ];
    }

    /** @test */
    function it_requires_subject_name()
    {
        $this->json('post', '/api/subjects', [])
            ->assertStatus(422)
            ->assertExactJson([
                "errors"  =>  [
                    "name"  =>  ["The name field is required."]
                ],
                "message" =>  "The given data was invalid."
            ]);
    }

    /** @test */
    function add_new_subject()
    {
        $this->disableEH();
        $this->json('post', '/api/subjects', $this->payload)
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
                    'updated_at',
                    'created_at',
                    'id'
                ]
            ]);
    }

    /** @test */
    function list_of_subjects()
    {
        $this->disableEH();
        $this->json('GET', '/api/subjects', [])
            ->assertStatus(200)
            ->assertJsonStructure([
                'data'  =>  [
                    0 =>  [
                        'name',
                        'is_active'
                    ]
                ]
            ]);
        $this->assertCount(0, Subject::all());
    }

    /** @test */
    function show_single_subject()
    {
        $this->disableEH();
        $this->json('get', "/api/subjects/1", [])
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
                    'name',
                    'created_at',
                    'updated_at'
                ]
            ]);
    }

    /** @test */
    function update_single_subject()
    {
        $this->disableEH();
        $payload = [
            'name' => "name",
            'is_active' => 1,
        ];

        $this->json('patch', '/api/subjects/1', $payload)
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
                    'name',
                    'is_active',
                    'created_at',
                    'updated_at'
                ]
            ]);
    }
}
