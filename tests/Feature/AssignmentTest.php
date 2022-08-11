<?php

namespace Tests\Feature;

use App\Assignment;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AssignmentTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp()
    {
        parent::setUp();
        $this->company = factory(\App\Company::class)->create([
            'name' => 'test'
        ]);

        $this->headers['company-id'] = $this->company->id;

        factory(\App\Assignment::class)->create([
            'company_id' => $this->company->id,
        ]);

        $this->payload = [
            'assignment_type' => 'assignment_type',
            'created_by_id' => 1,
            'student_instructions' => 'student_instructions',
            'content_id' => 1,
            'duration' => 'duration',
            'documentpath' => 'documentpath',
            'maximum_marks' => false,
        ];
    }

    /** @test */
    function it_requires_assignment_name()
    {
        $this->json('post', '/api/assignments', [], $this->headers)
            ->assertStatus(422)
            ->assertExactJson([
                "errors"  =>  [
                    "assignment_type"  =>  ["The assignment type field is required."]
                ],
                "message" =>  "The given data was invalid."
            ]);
    }

    /** @test */
    function add_new_assignment()
    {
        $this->disableEH();
        $this->json('post', '/api/assignments', $this->payload, $this->headers)
            ->assertStatus(201)
            ->assertJson([
                'data'  =>  [
                    'assignment_type' => 'assignment_type',
                    'created_by_id' => 1,
                    'student_instructions' => 'student_instructions',
                    'content_id' => 1,
                    'duration' => 'duration',
                    'documentpath' => 'documentpath',
                    'maximum_marks' => false,
                ]
            ])
            ->assertJsonStructureExact([
                'data'  =>  [
                    'assignment_type',
                    'created_by_id',
                    'student_instructions',
                    'content_id',
                    'duration',
                    'documentpath',
                    'maximum_marks',
                    'company_id',
                    'updated_at',
                    'created_at',
                    'id'
                ]
            ]);
    }

    /** @test */
    function list_of_assignments()
    {
        $this->disableEH();
        $this->json('GET', '/api/assignments', [], $this->headers)
            ->assertStatus(200)
            ->assertJsonStructure([
                'data'  =>  [
                    0 =>  [
                        'assignment_type',
                        'created_by_id',
                        'student_instructions',
                        'content_id',
                        'duration',
                        'documentpath',
                        'maximum_marks',
                        'is_deleted',
                    ]
                ]
            ]);
        $this->assertCount(1, Assignment::all());
    }

    /** @test */
    function show_single_assignment()
    {
        $this->disableEH();
        $this->json('get', "/api/assignments/1", [],  $this->headers)
            ->assertStatus(200)
            ->assertJson([
                'data'  => [
                    'assignment_type' => 'assignment_type',
                    'created_by_id' => 1,
                    'student_instructions' => 'student_instructions',
                    'content_id' => 1,
                    'duration' => 'duration',
                    'documentpath' => 'documentpath',
                    'maximum_marks' => false,
                ]
            ])
            ->assertJsonStructureExact([
                'data'    => [
                    'id',
                    'company_id',
                    'assignment_type',
                    'created_by_id',
                    'student_instructions',
                    'content_id',
                    'duration',
                    'documentpath',
                    'maximum_marks',
                    'is_deleted',
                    'created_at',
                    'updated_at'
                ]
            ]);
    }

    /** @test */
    function update_single_assignment()
    {
        $this->disableEH();
        $payload = [
            'assignment_type' => 'assignment_type',
            'created_by_id' => 1,
            'student_instructions' => 'student_instructions',
            'content_id' => 1,
            'duration' => 'duration',
            'documentpath' => 'documentpath',
            'maximum_marks' => false,
        ];

        $this->json('patch', '/api/assignments/1', $payload, $this->headers)
            ->assertStatus(200)
            ->assertJson([
                'data'    => [
                    'assignment_type' => 'assignment_type',
                    'created_by_id' => 1,
                    'student_instructions' => 'student_instructions',
                    'content_id' => 1,
                    'duration' => 'duration',
                    'documentpath' => 'documentpath',
                    'maximum_marks' => false,
                ]
            ])
            ->assertJsonStructureExact([
                'data'    => [
                    'id',
                    'company_id',
                    'assignment_type',
                    'created_by_id',
                    'student_instructions',
                    'content_id',
                    'duration',
                    'documentpath',
                    'maximum_marks',
                    'is_deleted',
                    'created_at',
                    'updated_at'
                ]
            ]);
    }

    /** @test */
    function delete_assignment()
    {
        $this->json('delete', '/api/assignments/1', [], $this->headers)
            ->assertStatus(204);

        $this->assertCount(0, Assignment::all());
    }
}
