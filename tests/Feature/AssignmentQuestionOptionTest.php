<?php

namespace Tests\Feature;

use App\AssignmentQuestionOption;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AssignmentQuestionOptionTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp()
    {
        parent::setUp();
        $this->company = factory(\App\Company::class)->create([
            'name' => 'test'
        ]);

        $this->headers['company-id'] = $this->company->id;

        factory(\App\AssignmentQuestionOption::class)->create([
            'company_id' => $this->company->id,
        ]);

        $this->payload = [
            'assignment_question_id' => 1,
            'option1' => 'option1',
            'option2' => 'option2',
            'option3' => 'option3',
            'option4' => 'option4',
        ];
    }

    /** @test */
    function it_requires_assignment_question_option_name()
    {
        $this->json('post', '/api/assignment_question_options', [], $this->headers)
            ->assertStatus(422)
            ->assertExactJson([
                "errors"  =>  [
                    "assignment_question_id"  =>  ["The assignment question id field is required."]
                ],
                "message" =>  "The given data was invalid."
            ]);
    }

    /** @test */
    function add_new_assignment_question_option()
    {
        $this->disableEH();
        $this->json('post', '/api/assignment_question_options', $this->payload, $this->headers)
            ->assertStatus(201)
            ->assertJson([
                'data'  =>  [
                    'assignment_question_id' => 1,
                    'option1' => 'option1',
                    'option2' => 'option2',
                    'option3' => 'option3',
                    'option4' => 'option4',
                ]
            ])
            ->assertJsonStructureExact([
                'data'  =>  [
                    'assignment_question_id',
                    'option1',
                    'option2',
                    'option3',
                    'option4',
                    'company_id',
                    'updated_at',
                    'created_at',
                    'id'
                ]
            ]);
    }

    /** @test */
    function list_of_assignment_question_options()
    {
        $this->disableEH();
        $this->json('GET', '/api/assignment_question_options', [], $this->headers)
            ->assertStatus(200)
            ->assertJsonStructure([
                'data'  =>  [
                    0 =>  [
                        'assignment_question_id',
                        'option1',
                        'option2',
                        'option3',
                        'option4',
                        'is_deleted',
                    ]
                ]
            ]);
        $this->assertCount(1, AssignmentQuestionOption::all());
    }

    /** @test */
    function show_single_assignment_question_option()
    {
        $this->disableEH();
        $this->json('get', "/api/assignment_question_options/1", [],  $this->headers)
            ->assertStatus(200)
            ->assertJson([
                'data'  => [
                    'assignment_question_id' => 1,
                    'option1' => 'option1',
                    'option2' => 'option2',
                    'option3' => 'option3',
                    'option4' => 'option4',
                ]
            ])
            ->assertJsonStructureExact([
                'data'    => [
                    'id',
                    'company_id',
                    'assignment_question_id',
                    'option1',
                    'option2',
                    'option3',
                    'option4',
                    'is_deleted',
                    'created_at',
                    'updated_at',
                    'option5',
                    'option6',

                ]
            ]);
    }

    /** @test */
    function update_single_assignment_question_option()
    {
        $this->disableEH();
        $payload = [
            'assignment_question_id' => 1,
            'option1' => 'option1',
            'option2' => 'option2',
            'option3' => 'option3',
            'option4' => 'option4',
        ];

        $this->json('patch', '/api/assignment_question_options/1', $payload, $this->headers)
            ->assertStatus(200)
            ->assertJson([
                'data'    => [
                    'assignment_question_id' => 1,
                    'option1' => 'option1',
                    'option2' => 'option2',
                    'option3' => 'option3',
                    'option4' => 'option4',
                ]
            ])
            ->assertJsonStructureExact([
                'data'    => [
                    'id',
                    'company_id',
                    'assignment_question_id',
                    'option1',
                    'option2',
                    'option3',
                    'option4',
                    'is_deleted',
                    'created_at',
                    'updated_at',
                    'option5',
                    'option6',
                ]
            ]);
    }

    /** @test */
    function delete_assignment_question_option()
    {
        $this->json('delete', '/api/assignment_question_options/1', [], $this->headers)
            ->assertStatus(204);

        $this->assertCount(0, AssignmentQuestionOption::all());
    }
}
