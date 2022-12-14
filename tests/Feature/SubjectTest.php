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

        factory(\App\Subject::class)->create([
            'name' => "Subject 1",
            'is_active' => 1,
        ]);

        $this->payload = [
            'name' => "name",
            'is_active' => 1,
        ];
    }

    /** @test */
    function it_requires_subject_name()
    {
        $this->json('post', '/api/subjects', [], $this->headers)
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
        $this->json('post', '/api/subjects', $this->payload, $this->headers)
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
        $this->json('GET', '/api/subjects', [], $this->headers)
            ->assertStatus(200)
            ->assertJsonStructure([
                'data'  =>  [
                    0 =>  [
                        'name',
                        'is_active'
                    ]
                ]
            ]);
        $this->assertCount(1, Subject::all());
    }

    /** @test */
    function show_single_subject()
    {
        $this->disableEH();
        $this->json('get', "/api/subjects/1", [],  $this->headers)
            ->assertStatus(200)
            ->assertJson([
                'data'  => [
                    'name' => "Subject 1",
                    'is_active' => 1,
                ]
            ])
            ->assertJsonStructureExact([
                'data'    => [
                    'id',
                    'name',
                    'is_active',
                    'created_at',
                    'updated_at',
                    'imagepath_1',
                    'imagepath_2',
                    'imagepath_3',
                    'imagepath_4',
                    'imagepath_5',
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

        $this->json('patch', '/api/subjects/1', $payload, $this->headers)
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
                    'updated_at',
                    'imagepath_1',
                    'imagepath_2',
                    'imagepath_3',
                    'imagepath_4',
                    'imagepath_5',
                ]
            ]);
    }

    /** @test */
    function delete_subject()
    {
        $this->json('delete', '/api/subjects/1', [], $this->headers)
            ->assertStatus(204);

        $this->assertCount(0, Subject::all());
    }
}
