<?php

namespace Tests\Feature;

use App\AssignmentExtension;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AssignmentExtensionTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp()
    {
        parent::setUp();
        $this->company = factory(\App\Company::class)->create([
            'name' => 'test'
        ]);

        $this->headers['company-id'] = $this->company->id;

        factory(\App\AssignmentExtension::class)->create([
            'company_id' => $this->company->id,
        ]);

        $this->payload = [
            'assignment_id' => 1,
            'user_id' => 1,
            'extension_reason' => 'extension_reason',
            'expected_extension_date' => 'expected_extension_date',
            'approved_extension_date' => 'approved_extension_date',
            'is_approved' => false,
        ];
    }

    /** @test */
    function it_requires_assignment_extension_name()
    {
        $this->json('post', '/api/assignment_extensions', [], $this->headers)
            ->assertStatus(422)
            ->assertExactJson([
                "errors"  =>  [
                    "assignment_id"  =>  ["The assignment id field is required."],
                    "user_id"  =>  ["The user id field is required."]
                ],
                "message" =>  "The given data was invalid."
            ]);
    }

    /** @test */
    function add_new_assignment_extension()
    {
        $this->disableEH();
        $this->json('post', '/api/assignment_extensions', $this->payload, $this->headers)
            ->assertStatus(201)
            ->assertJson([
                'data'  =>  [
                    'assignment_id' => 1,
                    'user_id' => 1,
                    'extension_reason' => 'extension_reason',
                    'expected_extension_date' => 'expected_extension_date',
                    'approved_extension_date' => 'approved_extension_date',
                    'is_approved' => false,
                ]
            ])
            ->assertJsonStructureExact([
                'data'  =>  [
                    'assignment_id',
                    'user_id',
                    'extension_reason',
                    'expected_extension_date',
                    'approved_extension_date',
                    'is_approved',
                    'company_id',
                    'updated_at',
                    'created_at',
                    'id'
                ]
            ]);
    }

    /** @test */
    function list_of_assignment_extensions()
    {
        $this->disableEH();
        $this->json('GET', '/api/assignment_extensions', [], $this->headers)
            ->assertStatus(200)
            ->assertJsonStructure([
                'data'  =>  [
                    0 =>  [
                        'assignment_id',
                        'user_id',
                        'extension_reason',
                        'expected_extension_date',
                        'approved_extension_date',
                        'is_approved',
                        'is_deleted',
                    ]
                ]
            ]);
        $this->assertCount(1, AssignmentExtension::all());
    }

    /** @test */
    function show_single_assignment_extension()
    {
        $this->disableEH();
        $this->json('get', "/api/assignment_extensions/1", [],  $this->headers)
            ->assertStatus(200)
            ->assertJson([
                'data'  => [
                    'assignment_id' => 1,
                    'user_id' => 1,
                    'extension_reason' => 'extension_reason',
                    'expected_extension_date' => 'expected_extension_date',
                    'approved_extension_date' => 'approved_extension_date',
                    'is_approved' => false,
                ]
            ])
            ->assertJsonStructureExact([
                'data'    => [
                    'id',
                    'company_id',
                    'assignment_id',
                    'user_id',
                    'extension_reason',
                    'expected_extension_date',
                    'approved_extension_date',
                    'is_approved',
                    'is_deleted',
                    'created_at',
                    'updated_at'
                ]
            ]);
    }

    /** @test */
    function update_single_assignment_extension()
    {
        $this->disableEH();
        $payload = [
            'assignment_id' => 1,
            'user_id' => 1,
            'extension_reason' => 'extension_reason',
            'expected_extension_date' => 'expected_extension_date',
            'approved_extension_date' => 'approved_extension_date',
            'is_approved' => false,
        ];

        $this->json('patch', '/api/assignment_extensions/1', $payload, $this->headers)
            ->assertStatus(200)
            ->assertJson([
                'data'    => [
                    'assignment_id' => 1,
                    'user_id' => 1,
                    'extension_reason' => 'extension_reason',
                    'expected_extension_date' => 'expected_extension_date',
                    'approved_extension_date' => 'approved_extension_date',
                    'is_approved' => false,
                ]
            ])
            ->assertJsonStructureExact([
                'data'    => [
                    'id',
                    'company_id',
                    'assignment_id',
                    'user_id',
                    'extension_reason',
                    'expected_extension_date',
                    'approved_extension_date',
                    'is_approved',
                    'is_deleted',
                    'created_at',
                    'updated_at'
                ]
            ]);
    }

    /** @test */
    function delete_assignment_extension()
    {
        $this->json('delete', '/api/assignment_extensions/1', [], $this->headers)
            ->assertStatus(204);

        $this->assertCount(0, AssignmentExtension::all());
    }
}
