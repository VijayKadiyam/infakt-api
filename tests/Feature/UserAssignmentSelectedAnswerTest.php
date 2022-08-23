<?php

namespace Tests\Feature;

use App\UserAssignmentSelectedAnswer;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserAssignmentSelectedAnswerTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp()
    {
        parent::setUp();

        $this->company = factory(\App\Company::class)->create([
            'name' => 'test'
        ]);

        $this->headers['company-id'] = $this->company->id;

        factory(UserAssignmentSelectedAnswer::class)->create([
            'company_id' =>  $this->company->id,
            "user_id" => 1,
            "assignment_id" => 1,
            "assignment_question_id" => 1,
            "selected_option_sr_no" => 1,
            "is_correct" => false,
            "marks_obtained" => 0,
            "documentpath" => "documentpath",
            "description" => "description",
            "is_deleted" => false,
        ]);

        $this->payload = [
            "user_id" => 1,
            "assignment_id" => 1,
            "assignment_question_id" => 1,
            "selected_option_sr_no" => 1,
            "is_correct" => false,
            "marks_obtained" => 0,
            "documentpath" => "documentpath",
            "description" => "description",
            "is_deleted" => false,
        ];
    }

    /** @test */
    function it_requires_following_details()
    {
        $this->json('post', '/api/user_assignment_selected_answers', [], $this->headers)
            ->assertStatus(422)
            ->assertExactJson([
                "errors"  =>  [
                    "user_id"        =>  ["The user id field is required."],
                ],
                "message" =>  "The given data was invalid."
            ]);
    }

    /** @test */
    function add_new_user_assignment_selected_answer()
    {
        $this->disableEH();

        $this->json('post', '/api/user_assignment_selected_answers', $this->payload, $this->headers)
            ->assertStatus(201)
            ->assertJson([
                'data'   => [
                    "user_id" => 1,
                    "assignment_id" => 1,
                    "assignment_question_id" => 1,
                    "selected_option_sr_no" => 1,
                    "is_correct" => false,
                    "marks_obtained" => 0,
                    "documentpath" => "documentpath",
                    "description" => "description",
                    "is_deleted" => false,
                ]
            ])
            ->assertJsonStructureExact([
                'data'   => [
                    "user_id",
                    "assignment_id",
                    "assignment_question_id",
                    "selected_option_sr_no",
                    "is_correct",
                    "marks_obtained",
                    "documentpath",
                    "description",
                    "is_deleted",
                    'company_id',
                    'updated_at',
                    'created_at',
                    'id'
                ]
            ]);
    }

    /** @test */
    function list_of_user_assignment_selected_answers()
    {
        $this->disableEH();
        $this->json('GET', '/api/user_assignment_selected_answers', [], $this->headers)
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    0 =>  [
                        "user_id",
                        "assignment_id",
                        "assignment_question_id",
                        "selected_option_sr_no",
                        "is_correct",
                        "marks_obtained",
                        "documentpath",
                        "description",
                        "is_deleted",
                    ]
                ]
            ]);
        $this->assertCount(1, UserAssignmentSelectedAnswer::all());
    }

    /** @test */
    function show_single_user_assignment_selected_answer()
    {

        $this->json('get', "/api/user_assignment_selected_answers/1", [], $this->headers)
            ->assertStatus(200)
            ->assertJson([
                'data'  => [
                    "user_id" => 1,
                    "assignment_id" => 1,
                    "assignment_question_id" => 1,
                    "selected_option_sr_no" => 1,
                    "is_correct" => false,
                    "marks_obtained" => 0,
                    "documentpath" => "documentpath",
                    "description" => "description",
                    "is_deleted" => false,
                ]
            ]);
    }

    /** @test */
    function update_single_user_assignment_selected_answer()
    {
        $payload = [
            "user_id" => 1,
            "assignment_id" => 1,
            "assignment_question_id" => 1,
            "selected_option_sr_no" => 1,
            "is_correct" => false,
            "marks_obtained" => 0,
            "documentpath" => "documentpath",
            "description" => "description",
            "is_deleted" => false,
        ];

        $this->json('patch', '/api/user_assignment_selected_answers/1', $payload, $this->headers)
            ->assertStatus(200)
            ->assertJson([
                'data'    => [
                    "user_id" => 1,
                    "assignment_id" => 1,
                    "assignment_question_id" => 1,
                    "selected_option_sr_no" => 1,
                    "is_correct" => false,
                    "marks_obtained" => 0,
                    "documentpath" => "documentpath",
                    "description" => "description",
                    "is_deleted" => false,
                ]
            ])
            ->assertJsonStructureExact([
                'data'  => [
                    'id',
                    'company_id',
                    "user_id",
                    "assignment_id",
                    "assignment_question_id",
                    "selected_option_sr_no",
                    "is_correct",
                    "marks_obtained",
                    "documentpath",
                    "description",
                    "is_deleted",
                    'created_at',
                    'updated_at',
                    'user_assignment_id',
                    'question',
                    'option1',
                    'option2',
                    'option3',
                    'option4',
                    'marks',
                    'correct_option_sr_no',
                    'feedback',
                ]
            ]);
    }

    /** @test */
    function delete_user_assignment_selected_answer()
    {
        $this->json('delete', '/api/user_assignment_selected_answers/1', [], $this->headers)
            ->assertStatus(204);

        $this->assertCount(0, UserAssignmentSelectedAnswer::all());
    }
}
