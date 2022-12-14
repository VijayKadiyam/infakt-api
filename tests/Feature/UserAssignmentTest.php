<?php

namespace Tests\Feature;

use App\UserAssignment;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserAssignmentTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp()
    {
        parent::setUp();

        $this->company = factory(\App\Company::class)->create([
            'name' => 'test'
        ]);

        $this->headers['company-id'] = $this->company->id;

        factory(UserAssignment::class)->create([
            'company_id' =>  $this->company->id,
            "user_id" => 1,
            "assignment_id" => 1,
            "submission_date" => "submission_date",
            "score" => 1,
            "documentpath" => "documentpath",
            'is_deleted' => 0,
        ]);

        $this->payload = [
            "user_id" => 1,
            "assignment_id" => 1,
            "submission_date" => "submission_date",
            "score" => 1,
            "documentpath" => "documentpath",
            'is_deleted' => 0,
            'user_assignment_selected_answers'  =>  [
                0   =>  [
                    "user_id" => 1,
                    "company_id"    => 1,
                    "assignment_id" => 1,
                    "assignment_question_id" => 1,
                    "selected_option_sr_no" => 1,
                    "is_correct" => false,
                    "marks_obtained" => 0,
                    "documentpath" => "documentpath",
                    "description" => "description",
                    "is_deleted" => false,
                ]
            ]
        ];
    }

    /** @test */
    function it_requires_following_details()
    {
        $this->json('post', '/api/user_assignments', [], $this->headers)
            ->assertStatus(422)
            ->assertExactJson([
                "errors"  =>  [
                    "user_id"        =>  ["The user id field is required."],
                ],
                "message" =>  "The given data was invalid."
            ]);
    }

    /** @test */
    function add_new_user_assignment()
    {
        $this->disableEH();

        $this->json('post', '/api/user_assignments', $this->payload, $this->headers)
            ->assertStatus(201)
            ->assertJson([
                'data'   => [
                    "user_id" => 1,
                    "assignment_id" => 1,
                    "submission_date" => "submission_date",
                    "score" => 1,
                    "documentpath" => "documentpath",
                    'is_deleted' => 0,
                    'user_assignment_selected_answers'  =>  [
                        0   =>  [
                            "user_id" => 1,
                            "company_id"    => 1,
                            "assignment_id" => 1,
                            "assignment_question_id" => 1,
                            "selected_option_sr_no" => 1,
                            "is_correct" => false,
                            "marks_obtained" => 0,
                            "documentpath" => "documentpath",
                            "description" => "description",
                            "is_deleted" => false,
                        ]
                    ]
                ]
            ])
            ->assertJsonStructureExact([
                'data'   => [
                    "user_id",
                    "assignment_id",
                    "submission_date",
                    "score",
                    "documentpath",
                    'is_deleted',
                    'company_id',
                    'updated_at',
                    'created_at',
                    'id',
                    'user',
                    'assignment',
                    "user_assignment_selected_answers"
                ]
            ]);
    }

    /** @test */
    function list_of_user_assignments()
    {
        $this->disableEH();
        $this->json('GET', '/api/user_assignments', [], $this->headers)
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    0 =>  [
                        "user_id",
                        "assignment_id",
                        "submission_date",
                        "score",
                        "documentpath",
                        'is_deleted',
                    ]
                ]
            ]);
        $this->assertCount(1, UserAssignment::all());
    }

    /** @test */
    function show_single_user_assignment()
    {

        $this->json('get', "/api/user_assignments/1", [], $this->headers)
            ->assertStatus(200)
            ->assertJson([
                'data'  => [
                    "user_id" => 1,
                    "assignment_id" => 1,
                    "submission_date" => "submission_date",
                    "score" => 1,
                    "documentpath" => "documentpath",
                    'is_deleted' => 0,
                ]
            ]);
    }

    /** @test */
    function update_single_user_assignment()
    {
        $payload = [
            "user_id" => 1,
            "assignment_id" => 1,
            "submission_date" => "submission_date",
            "score" => 1,
            "documentpath" => "documentpath",
            'is_deleted' => 0,
        ];

        $this->json('patch', '/api/user_assignments/1', $payload, $this->headers)
            ->assertStatus(200)
            ->assertJson([
                'data'    => [
                    "user_id" => 1,
                    "assignment_id" => 1,
                    "submission_date" => "submission_date",
                    "score" => 1,
                    "documentpath" => "documentpath",
                    'is_deleted' => 0,
                ]
            ])
            ->assertJsonStructureExact([
                'data'  => [
                    'id',
                    'company_id',
                    "user_id",
                    "assignment_id",
                    "submission_date",
                    "score",
                    "documentpath",
                    'is_deleted',
                    'created_at',
                    'updated_at',
                    'time_taken',
                    'total_questions',
                    'answered_questions',
                    'right_questions',
                    'wrong_questions',
                    'result_declared',
                    'feedback',
                    'start_time',
                    'time_data',
                ]
            ]);
    }

    /** @test */
    function delete_user_assignment()
    {
        $this->json('delete', '/api/user_assignments/1', [], $this->headers)
            ->assertStatus(204);

        $this->assertCount(0, UserAssignment::all());
    }
}
