<?php

namespace Tests\Feature;

use App\Assignment;
use App\AssignmentClasscode;
use App\AssignmentExtension;
use App\AssignmentQuestion;
use App\AssignmentQuestionOption;
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
            'maximum_marks' => 1,
            'collection_id' => 1,
            'assignment_classcodes' => [
                0 => [
                    'start_date' => 'start_date'
                ]
            ],
            'assignment_questions' =>  [
                0 =>  [
                    'description' =>  'description',
                    'assignment_question_options' => [
                        0 => [
                            'option1' => 'option1',
                        ]
                    ]
                ]
            ],
            'assignment_extensions' => [
                0 => [
                    'extension_reason' => 'extension_reason'
                ]
            ],
        ];
        // dd($this->payload);
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
        // dd($this->payload);
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
                    'maximum_marks' => 1,
                    'collection_id' => 1,
                    'assignment_classcodes' => [
                        0 => [
                            'start_date' => 'start_date'
                        ]
                    ],
                    'assignment_questions' =>  [
                        0 =>  [
                            'description' =>  'description',
                            'assignment_question_options' => [
                                0 => [
                                    'option1' => 'option1',
                                ]
                            ]
                        ]
                    ],
                    'assignment_extensions' => [
                        0 => [
                            'extension_reason' => 'extension_reason'
                        ]
                    ],
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
                    'collection_id',
                    'company_id',
                    'updated_at',
                    'created_at',
                    'id',
                    'content',
                    'assignment_classcodes',
                    'assignment_questions',
                    'assignment_extensions',
                    'user_assignments',


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
                        'collection_id',
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
                    'maximum_marks' => 1,
                    'collection_id' => 1,
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
                    'is_deleted',
                    'created_at',
                    'updated_at',
                    'maximum_marks',
                    'assignment_title',
                    'is_draft',
                    'collection_id',
                    'content',
                    'assignment_classcodes',
                    'assignment_questions',
                    'assignment_extensions',
                    'user_assignments',
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
            'maximum_marks' => 1,
            'collection_id' => 1,
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
                    'maximum_marks' => 1,
                    'collection_id' => 1,
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
                    'is_deleted',
                    'created_at',
                    'updated_at',
                    'maximum_marks',
                    'assignment_title',
                    'is_draft',
                    'collection_id',
                ]
            ]);
    }

    function update_single_assignment_nested()
    {
        $this->disableEH();

        $assignment = factory(Assignment::class)->create([
            'company_id' =>  $this->company->id
        ]);

        $assignment_classcode = factory(AssignmentClasscode::class)->create([
            'assignment_id' => $assignment->id
        ]);

        $assignment_question = factory(AssignmentQuestion::class)->create([
            'assignment_id' => $assignment->id,
        ]);
        $assignment_question_option = factory(AssignmentQuestionOption::class)->create([
            'assignment_question_id' => $assignment_question->id,
        ]);
        $assignment_extension = factory(AssignmentExtension::class)->create([
            'assignment_id' => $assignment->id,
        ]);


        $payload = [
            'assignment_type' => 'assignment_type',
            'created_by_id' => 1,
            'student_instructions' => 'student_instructions',
            'content_id' => 1,
            'duration' => 'duration',
            'documentpath' => 'documentpath',
            'maximum_marks' => 1,
            'collection_id' => 1,
            'assignment_classcodes' =>  [
                0 =>  [
                    'id'          =>  2,
                    'start_date'  =>  'start_date',


                ],
                1 =>  [
                    'start_date'  =>  'start_date',

                ]
            ],
            'assignment_questions' =>  [
                0 =>  [
                    'id'          =>  2,
                    'description'  =>  'description',
                    'assignment_question_options' => [
                        0 => [
                            'id'          =>  2,
                            'option1' => 'option1',
                        ]
                    ]

                ],
                1 =>  [
                    'description'  =>  'description',
                    'assignment_question_options' => [
                        0 => [
                            'option1' => 'option1',
                        ]
                    ]
                ]
            ],
            'assignment_extensions' =>  [
                0 =>  [
                    'id'          =>  2,
                    'extension_reason'  =>  'extension_reason',


                ],
                1 =>  [
                    'extension_reason'  =>  'extension_reason',

                ]
            ],
        ];

        $this->json('post', '/api/assignments', $payload, $this->headers)
            ->assertStatus(200)
            ->assertJson([
                'data'    => [
                    'assignment_type' => 'assignment_type',
                    'created_by_id' => 1,
                    'student_instructions' => 'student_instructions',
                    'content_id' => 1,
                    'duration' => 'duration',
                    'documentpath' => 'documentpath',
                    'maximum_marks' => 1,
                    'collection_id' => 1,
                    'assignment_classcodes' =>  [
                        0 =>  [
                            'id'          =>  2,
                            'start_date'  =>  'start_date',


                        ],
                    ],
                    'assignment_questions' =>  [
                        0 =>  [
                            'id'          =>  2,
                            'description'  =>  'description',
                            'assignment_question_options' => [
                                0 => [
                                    'id'          =>  2,
                                    'option1' => 'option1',
                                ]
                            ]

                        ],
                    ],
                    'assignment_extensions' =>  [
                        0 =>  [
                            'id'          =>  2,
                            'extension_reason'  =>  'extension_reason',


                        ],
                    ],

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
                    'updated_at',
                    'collection_id',
                    'assignment_classcodes',
                    'assignment_questions',
                    'assignment_extensions'
                ]
            ]);

        // 1 Delete + 1 New
        $payload = [
            'assignment_classcodes' =>  [
                0 =>  [
                    'id'          =>  2,
                    'start_date'  =>  'start_date',


                ],
            ],
            'assignment_questions' =>  [
                0 =>  [
                    'id'          =>  2,
                    'description'  =>  'description',
                    'assignment_question_options' => [
                        0 => [
                            'id'          =>  2,
                            'option1' => 'option1',
                        ]
                    ]

                ],
            ],
            'assignment_extensions' =>  [
                0 =>  [
                    'id'          =>  2,
                    'extension_reason'  =>  'extension_reason',


                ],
            ],
        ];

        $this->json('post', '/api/assignments', $payload, $this->headers)
            ->assertStatus(200)
            ->assertJson([
                'data'    => [
                    'assignment_type' => 'assignment_type',
                    'created_by_id' => 1,
                    'student_instructions' => 'student_instructions',
                    'content_id' => 1,
                    'duration' => 'duration',
                    'documentpath' => 'documentpath',
                    'maximum_marks' => 1,
                    'collection_id' => 1,
                    'assignment_classcodes' =>  [
                        0 =>  [
                            'id'          =>  2,
                            'start_date'  =>  'start_date',


                        ],
                    ],
                    'assignment_questions' =>  [
                        0 =>  [
                            'id'          =>  2,
                            'description'  =>  'description',
                            'assignment_question_options' => [
                                0 => [
                                    'id'          =>  2,
                                    'option1' => 'option1',
                                ]
                            ]

                        ],
                    ],
                    'assignment_extensions' =>  [
                        0 =>  [
                            'id'          =>  2,
                            'extension_reason'  =>  'extension_reason',


                        ],
                    ],

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
                    'updated_at',
                    'collection_id',
                    'assignment_classcodes',
                    'assignment_questions',
                    'assignment_extensions'
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
