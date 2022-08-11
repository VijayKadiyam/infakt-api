<?php

namespace Tests\Feature;

use App\AssignmentQuestion;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AssignmentQuestionTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp()
    {
        parent::setUp();
        $this->company = factory(\App\Company::class)->create([
            'name' => 'test'
        ]);

        $this->headers['company-id'] = $this->company->id;

        factory(\App\AssignmentQuestion::class)->create([
            'company_id' => $this->company->id,
        ]);

        $this->payload = [
            'assignment_id' => 1,
            'description' => 'description',
            'correct_option_sr_no' =>  'correct_option_sr_no',
            'marks' =>  1,
            'negative_marks' => 1,
        ];
    }

    /** @test */
    function it_requires_assignment_question_name()
    {
        $this->json('post', '/api/assignment_questions', [], $this->headers)
            ->assertStatus(422)
            ->assertExactJson([
                "errors"  =>  [
                    "assignment_id"  =>  ["The assignment id field is required."]
                ],
                "message" =>  "The given data was invalid."
            ]);
    }

    /** @test */
    function add_new_assignment_question()
    {
        $this->disableEH();
        $this->json('post', '/api/assignment_questions', $this->payload, $this->headers)
            ->assertStatus(201)
            ->assertJson([
                'data'  =>  [
                    'assignment_id' => 1,
                    'description' => 'description',
                    'correct_option_sr_no' =>  'correct_option_sr_no',
                    'marks' =>  1,
                    'negative_marks' => 1,
                ]
            ])
            ->assertJsonStructureExact([
                'data'  =>  [
                    'assignment_id',
                    'description',
                    'correct_option_sr_no',
                    'marks',
                    'negative_marks',
                    'company_id',
                    'updated_at',
                    'created_at',
                    'id'
                ]
            ]);
    }

    /** @test */
    function list_of_assignment_questions()
    {
        $this->disableEH();
        $this->json('GET', '/api/assignment_questions', [], $this->headers)
            ->assertStatus(200)
            ->assertJsonStructure([
                'data'  =>  [
                    0 =>  [
                        'assignment_id',
                        'description',
                        'correct_option_sr_no',
                        'marks',
                        'negative_marks',
                        'is_deleted',
                    ]
                ]
            ]);
        $this->assertCount(1, AssignmentQuestion::all());
    }

    /** @test */
    function show_single_assignment_question()
    {
        $this->disableEH();
        $this->json('get', "/api/assignment_questions/1", [],  $this->headers)
            ->assertStatus(200)
            ->assertJson([
                'data'  => [
                    'assignment_id' => 1,
                    'description' => 'description',
                    'correct_option_sr_no' =>  'correct_option_sr_no',
                    'marks' =>  1,
                    'negative_marks' => 1,
                ]
            ])
            ->assertJsonStructureExact([
                'data'    => [
                    'id',
                    'company_id',
                    'assignment_id',
                    'description',
                    'correct_option_sr_no',
                    'marks',
                    'negative_marks',
                    'is_deleted',
                    'created_at',
                    'updated_at'
                ]
            ]);
    }

    /** @test */
    function update_single_assignment_question()
    {
        $this->disableEH();
        $payload = [
            'assignment_id' => 1,
            'description' => 'description',
            'correct_option_sr_no' =>  'correct_option_sr_no',
            'marks' =>  1,
            'negative_marks' => 1,
        ];

        $this->json('patch', '/api/assignment_questions/1', $payload, $this->headers)
            ->assertStatus(200)
            ->assertJson([
                'data'    => [
                    'assignment_id' => 1,
                    'description' => 'description',
                    'correct_option_sr_no' =>  'correct_option_sr_no',
                    'marks' =>  1,
                    'negative_marks' => 1,
                ]
            ])
            ->assertJsonStructureExact([
                'data'    => [
                    'id',
                    'company_id',
                    'assignment_id',
                    'description',
                    'correct_option_sr_no',
                    'marks',
                    'negative_marks',
                    'is_deleted',
                    'created_at',
                    'updated_at'
                ]
            ]);
    }

    /** @test */
    function delete_assignment_question()
    {
        $this->json('delete', '/api/assignment_questions/1', [], $this->headers)
            ->assertStatus(204);

        $this->assertCount(0, AssignmentQuestion::all());
    }
}
