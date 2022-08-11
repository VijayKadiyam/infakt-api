<?php

namespace Tests\Feature;

use App\AssignmentClasscode;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AssignmentClasscodeTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp()
    {
        parent::setUp();
        $this->company = factory(\App\Company::class)->create([
            'name' => 'test'
        ]);

        $this->headers['company-id'] = $this->company->id;

        factory(\App\AssignmentClasscode::class)->create([
            'company_id' => $this->company->id,
        ]);

        $this->payload = [
            'assignment_id' => 1,
            'classcode_id' => 1,
            'start_date' => 'start_date',
            'end_date' => 'end_date',
        ];
    }

    /** @test */
    function it_requires_assignment_classcode_name()
    {
        $this->json('post', '/api/assignment_classcodes', [], $this->headers)
            ->assertStatus(422)
            ->assertExactJson([
                "errors"  =>  [
                    "classcode_id"  =>  ["The classcode id field is required."]
                ],
                "message" =>  "The given data was invalid."
            ]);
    }

    /** @test */
    function add_new_assignment_classcode()
    {
        $this->disableEH();
        $this->json('post', '/api/assignment_classcodes', $this->payload, $this->headers)
            ->assertStatus(201)
            ->assertJson([
                'data'  =>  [
                    'assignment_id' => 1,
                    'classcode_id' => 1,
                    'start_date' => 'start_date',
                    'end_date' => 'end_date',
                ]
            ])
            ->assertJsonStructureExact([
                'data'  =>  [
                    'assignment_id',
                    'classcode_id',
                    'start_date',
                    'end_date',
                    'company_id',
                    'updated_at',
                    'created_at',
                    'id'
                ]
            ]);
    }

    /** @test */
    function list_of_assignment_classcodes()
    {
        $this->disableEH();
        $this->json('GET', '/api/assignment_classcodes', [], $this->headers)
            ->assertStatus(200)
            ->assertJsonStructure([
                'data'  =>  [
                    0 =>  [
                        'assignment_id',
                        'classcode_id',
                        'start_date',
                        'end_date',
                        'is_deleted',
                    ]
                ]
            ]);
        $this->assertCount(1, AssignmentClasscode::all());
    }

    /** @test */
    function show_single_assignment_classcode()
    {
        $this->disableEH();
        $this->json('get', "/api/assignment_classcodes/1", [],  $this->headers)
            ->assertStatus(200)
            ->assertJson([
                'data'  => [
                    'assignment_id' => 1,
                    'classcode_id' => 1,
                    'start_date' => 'start_date',
                    'end_date' => 'end_date',
                ]
            ])
            ->assertJsonStructureExact([
                'data'    => [
                    'id',
                    'company_id',
                    'assignment_id',
                    'classcode_id',
                    'start_date',
                    'end_date',
                    'is_deleted',
                    'created_at',
                    'updated_at'
                ]
            ]);
    }

    /** @test */
    function update_single_assignment_classcode()
    {
        $this->disableEH();
        $payload = [
            'assignment_id' => 1,
            'classcode_id' => 1,
            'start_date' => 'start_date',
            'end_date' => 'end_date',
        ];

        $this->json('patch', '/api/assignment_classcodes/1', $payload, $this->headers)
            ->assertStatus(200)
            ->assertJson([
                'data'    => [
                    'assignment_id' => 1,
                    'classcode_id' => 1,
                    'start_date' => 'start_date',
                    'end_date' => 'end_date',
                ]
            ])
            ->assertJsonStructureExact([
                'data'    => [
                    'id',
                    'company_id',
                    'assignment_id',
                    'classcode_id',
                    'start_date',
                    'end_date',
                    'is_deleted',
                    'created_at',
                    'updated_at'
                ]
            ]);
    }

    /** @test */
    function delete_assignment_classcode()
    {
        $this->json('delete', '/api/assignment_classcodes/1', [], $this->headers)
            ->assertStatus(204);

        $this->assertCount(0, AssignmentClasscode::all());
    }
}
