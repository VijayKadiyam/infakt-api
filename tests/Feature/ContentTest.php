<?php

namespace Tests\Feature;

use App\Content;
use App\ContentAssignToRead;
use App\ContentBoard;
use App\ContentGrade;
use App\ContentHiddenClasscode;
use App\ContentInfoBoard;
use App\ContentLockClasscode;
use App\ContentMedia;
use App\ContentSchool;
use App\ContentSubject;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ContentTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp()
    {
        parent::setUp();

        factory(Content::class)->create([
            'content_name'     => 'content_name',
            'content_type'     => 'content_type',
            'written_by_name'    => 1,
            'reading_time'     => 'reading_time',
            'content_metadata' => 'content_metadata',
            'easy_content'     => 'easy_content',
            'med_content'      => 'med_content',
            'original_content'     => 'original_content',
        ]);

        $this->payload = [
            'content_name'     => 'content_name',
            'content_type'     => 'content_type',
            'written_by_name'    => 1,
            'reading_time'     => 'reading_time',
            'content_metadata' => 'content_metadata',
            'easy_content'     => 'easy_content',
            'med_content'      => 'med_content',
            'original_content'     => 'original_content',
            'content_subjects' =>  [
                0 =>  [
                    'subject_id' =>  1,
                ]
            ],
            'content_medias' =>  [
                0 =>  [
                    'mediapath' =>  'mediapath',
                ]
            ],
            'content_hidden_classcodes' =>  [
                0 =>  [
                    'classcode_id' =>  1,
                ]
            ],
            'content_lock_classcodes' =>  [
                0 =>  [
                    'classcode_id' =>  1,
                ]
            ],
            'content_assign_to_reads' =>  [
                0 =>  [
                    'classcode_id' =>  1,
                ]
            ],
            'content_grades' =>  [
                0 =>  [
                    'grade_id' =>  1,
                ]
            ],
            'content_boards' =>  [
                0 =>  [
                    'board_id' =>  1,
                ]
            ],
            'content_info_boards' =>  [
                0 =>  [
                    'board_id' =>  1,
                ]
            ],
            'content_schools' =>  [
                0 =>  [
                    'company_id' =>  1,
                ]
            ],
        ];
    }

    /** @test */
    function it_requires_content_name()
    {
        $this->json('post', '/api/contents', [], $this->headers)
            ->assertStatus(422)
            ->assertExactJson([
                "errors"  =>  [
                    "content_name"  =>  ["The content name field is required."]
                ],
                "message" =>  "The given data was invalid."
            ]);
    }

    /** @test */
    function add_new_content()
    {
        $this->disableEH();
        $this->json('post', '/api/contents', $this->payload, $this->headers)
            ->assertStatus(201)
            ->assertJson([
                'data'  =>  [
                    'content_name'     => 'content_name',
                    'content_type'     => 'content_type',
                    'written_by_name'    => 1,
                    'reading_time'     => 'reading_time',
                    'content_metadata' => 'content_metadata',
                    'easy_content'     => 'easy_content',
                    'med_content'      => 'med_content',
                    'original_content'     => 'original_content',
                    'content_subjects' =>  [
                        0 =>  [
                            'subject_id' =>  '1',
                        ]
                    ],
                    'content_medias' =>  [
                        0 =>  [
                            'mediapath' =>  'mediapath',
                        ]
                    ],
                    'content_hidden_classcodes' =>  [
                        0 =>  [
                            'classcode_id' =>  '1',
                        ]
                    ],
                    'content_lock_classcodes' =>  [
                        0 =>  [
                            'classcode_id' =>  '1',
                        ]
                    ],
                    'content_assign_to_reads' =>  [
                        0 =>  [
                            'classcode_id' =>  '1',
                        ]
                    ],
                    'content_grades' =>  [
                        0 =>  [
                            'grade_id' =>  '1',
                        ]
                    ],
                    'content_boards' =>  [
                        0 =>  [
                            'board_id' =>  '1',
                        ]
                    ],
                    'content_info_boards' =>  [
                        0 =>  [
                            'board_id' =>  '1',
                        ]
                    ],
                    'content_schools' =>  [
                        0 =>  [
                            'company_id' =>  '1',
                        ]
                    ],
                ]
            ])
            ->assertJsonStructureExact([
                'data'  =>  [
                    'content_name',
                    'content_type',
                    'written_by_name',
                    'reading_time',
                    'content_metadata',
                    'easy_content',
                    'med_content',
                    'original_content',
                    'updated_at',
                    'created_at',
                    'id',
                    'content_subjects',
                    'content_medias',
                    'content_descriptions',
                    'content_hidden_classcodes',
                    'content_lock_classcodes',
                    'content_assign_to_reads',
                    'content_grades',
                    'content_boards',
                    'content_info_boards',
                    'content_schools',
                ]
            ]);
    }

    /** @test */
    function list_of_contents()
    {
        $this->disableEH();
        $this->json('GET', '/api/contents', [], $this->headers)
            ->assertStatus(200)
            ->assertJsonStructure([
                'data'  =>  [
                    0 =>  [
                        'content_name',
                        'content_type',
                        'written_by_name',
                        'reading_time',
                        'content_metadata',
                        'easy_content',
                        'med_content',
                        'original_content',
                    ]
                ]
            ]);
        $this->assertCount(1, Content::all());
    }

    /** @test */
    function show_single_content()
    {
        $this->disableEH();
        $this->json('get', "/api/contents/1", [],  $this->headers)
            ->assertStatus(200)
            ->assertJson([
                'data'  => [
                    'content_name'     => 'content_name',
                    'content_type'     => 'content_type',
                    'written_by_name'    => 1,
                    'reading_time'     => 'reading_time',
                    'content_metadata' => 'content_metadata',
                    'easy_content'     => 'easy_content',
                    'med_content'      => 'med_content',
                    'original_content'     => 'original_content',
                ]
            ])
            ->assertJsonStructureExact([
                'data'    => [
                    'id',
                    'content_name',
                    'content_type',
                    'reading_time',
                    'content_metadata',
                    'easy_content',
                    'med_content',
                    'created_at',
                    'updated_at',
                    'learning_outcome',
                    'for_school_type',
                    'specific_to',
                    'company_id',
                    'original_content',
                    'written_by_name',
                    'grade_id',
                    'board_id',
                    'info_board_id',
                    'publication',
                    'adapted_by',
                    'content_subjects',
                    'content_medias',
                    'content_metadatas',
                    'content_descriptions',
                ]
            ]);
    }

    /** @test */
    function update_single_content()
    {
        $this->disableEH();
        $payload = [
            'content_name'     => 'content_name',
            'content_type'     => 'content_type',
            'written_by_name'    => 1,
            'reading_time'     => 'reading_time',
            'content_metadata' => 'content_metadata',
            'easy_content'     => 'easy_content',
            'med_content'      => 'med_content',
            'original_content'     => 'original_content',
            'content_subjects' =>  [
                0 =>  [
                    'subject_id' =>  1,
                ]
            ],
            'content_medias' =>  [
                0 =>  [
                    'mediapath' =>  1,
                ]
            ],
            'content_hidden_classcodes' =>  [
                0 =>  [
                    'classcode_id' =>  1,
                ]
            ],
            'content_lock_classcodes' =>  [
                0 =>  [
                    'classcode_id' =>  1,
                ]
            ],
            'content_assign_to_reads' =>  [
                0 =>  [
                    'classcode_id' =>  1,
                ]
            ],
            'content_grades' =>  [
                0 =>  [
                    'grade_id' =>  1,
                ]
            ],
        ];

        $this->json('patch', '/api/contents/1', $payload, $this->headers)
            ->assertStatus(200)
            ->assertJson([
                'data'    => [
                    'content_name'     => 'content_name',
                    'content_type'     => 'content_type',
                    'written_by_name'    => 1,
                    'reading_time'     => 'reading_time',
                    'content_metadata' => 'content_metadata',
                    'easy_content'     => 'easy_content',
                    'med_content'      => 'med_content',
                    'original_content'     => 'original_content',
                ]
            ])
            ->assertJsonStructureExact([
                'data'    => [
                    'id',
                    'content_name',
                    'content_type',
                    'reading_time',
                    'content_metadata',
                    'easy_content',
                    'med_content',
                    'created_at',
                    'updated_at',
                    'learning_outcome',
                    'for_school_type',
                    'specific_to',
                    'school_id',
                    'original_content',
                    'written_by_name',
                    'grade_id',
                    'board_id',
                    'info_board_id',
                    'publication',
                    'adapted_by',
                    // 'content_subjects',
                    // 'content_medias',
                    // 'content_metadatas',
                    // 'content_descriptions',
                ]
            ]);
    }

    /** @test */
    function update_single_content_nested()
    {
        $this->disableEH();

        $content = factory(Content::class)->create([
            'content_name'     => 'content_name',
            'content_type'     => 'content_type',
            'written_by_name'    => 1,
            'reading_time'     => 'reading_time',
            'content_metadata' => 'content_metadata',
            'easy_content'     => 'easy_content',
            'med_content'      => 'med_content',
            'original_content'     => 'original_content',
        ]);
        $contentSubject = factory(ContentSubject::class)->create([
            'content_id' =>  $content->id
        ]);
        $contentMedia = factory(ContentMedia::class)->create([
            'content_id' =>  $content->id
        ]);
        $contentHiddenClasscode = factory(ContentHiddenClasscode::class)->create([
            'content_id' =>  $content->id
        ]);
        $contentLockClasscode = factory(ContentLockClasscode::class)->create([
            'content_id' =>  $content->id
        ]);
        $contentAssignToRead = factory(ContentAssignToRead::class)->create([
            'content_id' =>  $content->id
        ]);
        $contentGrade = factory(ContentGrade::class)->create([
            'content_id' =>  $content->id
        ]);
        $contentBoard = factory(ContentBoard::class)->create([
            'content_id' =>  $content->id
        ]);
        $contentInfoBoard = factory(ContentInfoBoard::class)->create([
            'content_id' =>  $content->id
        ]);
        $contentSchool = factory(ContentSchool::class)->create([
            'content_id' =>  $content->id
        ]);

        // Old Edit + No Delete + 1 New
        $payload = [
            'id'          =>  $content->id,
            'content_name'     => 'content_name',
            'content_type'     => 'content_type',
            'written_by_name'    => 1,
            'reading_time'     => 'reading_time',
            'content_metadata' => 'content_metadata',
            'easy_content'     => 'easy_content',
            'med_content'      => 'med_content',
            'original_content'     => 'original_content',
            'content_subjects' =>  [
                0 =>  [
                    'id'        =>  $contentSubject->id,
                    'subject_id' =>  '1'
                ],
                1 =>  [
                    'subject_id' =>  '2'
                ]
            ],
            'content_medias' =>  [
                0 =>  [
                    'id'          =>  $contentMedia->id,
                    'mediapath'  =>  'mediapath'
                ],
                1 =>  [
                    'mediapath'  =>  'mediapath'
                ]
            ],
            'content_hidden_classcodes' =>  [
                0 =>  [
                    'id'          =>  $contentHiddenClasscode->id,
                    'classcode_id'  =>  1
                ],
                1 =>  [
                    'classcode_id'  =>  1
                ]
            ],
            'content_lock_classcodes' =>  [
                0 =>  [
                    'id'          =>  $contentLockClasscode->id,
                    'classcode_id'  =>  1
                ],
                1 =>  [
                    'classcode_id'  =>  1
                ]
            ],
            'content_assign_to_reads' =>  [
                0 =>  [
                    'id'          =>  $contentAssignToRead->id,
                    'classcode_id' =>  1,
                ],
                1 =>  [
                    'classcode_id'  =>  1
                ]
            ],
            'content_grades' =>  [
                0 =>  [
                    'id'          =>  $contentGrade->id,
                    'grade_id' =>  1,
                ],
                1 =>  [
                    'grade_id'  =>  1
                ]
            ],
            'content_boards' =>  [
                0 =>  [
                    'id'          =>  $contentBoard->id,
                    'board_id' =>  1,
                ],
                1 =>  [
                    'board_id'  =>  1
                ]
            ],
            'content_info_boards' =>  [
                0 =>  [
                    'id'          =>  $contentInfoBoard->id,
                    'board_id' =>  1,
                ],
                1 =>  [
                    'board_id'  =>  1
                ]
            ],
            'content_schools' =>  [
                0 =>  [
                    'id'          =>  $contentSchool->id,
                    'company_id' =>  1,
                ],
                1 =>  [
                    'company_id'  =>  1
                ]
            ],
        ];

        $this->json('post', '/api/contents', $payload, $this->headers)
            ->assertStatus(201)
            ->assertJson([
                'data'    => [
                    'id'          =>  $content->id,
                    'content_name'     => 'content_name',
                    'content_type'     => 'content_type',
                    'written_by_name'    => 1,
                    'reading_time'     => 'reading_time',
                    'content_metadata' => 'content_metadata',
                    'easy_content'     => 'easy_content',
                    'med_content'      => 'med_content',
                    'original_content'     => 'original_content',
                    'content_subjects' =>  [
                        0 =>  [
                            'id'        =>  $contentSubject->id,
                            'subject_id' =>  '1'
                        ],
                        1 =>  [
                            'subject_id' =>  '2'
                        ]
                    ],
                    'content_medias' =>  [
                        0 =>  [
                            'id'          =>  $contentMedia->id,
                            'mediapath'  =>  'mediapath'
                        ],
                        1 =>  [
                            'mediapath'  =>  'mediapath'
                        ]
                    ],
                    'content_hidden_classcodes' =>  [
                        0 =>  [
                            'id'          =>  $contentHiddenClasscode->id,
                            'classcode_id'  =>  1
                        ],
                        1 =>  [
                            'classcode_id'  =>  1
                        ]
                    ],
                    'content_lock_classcodes' =>  [
                        0 =>  [
                            'id'          =>  $contentLockClasscode->id,
                            'classcode_id'  =>  1
                        ],
                        1 =>  [
                            'classcode_id'  =>  1
                        ]
                    ],
                    'content_assign_to_reads' =>  [
                        0 =>  [
                            'id'          =>  $contentAssignToRead->id,
                            'classcode_id' =>  1,
                        ],
                        1 =>  [
                            'classcode_id'  =>  1
                        ]
                    ],
                    'content_grades' =>  [
                        0 =>  [
                            'id'          =>  $contentGrade->id,
                            'grade_id' =>  1,
                        ],
                        1 =>  [
                            'grade_id'  =>  1
                        ]
                    ],
                    'content_boards' =>  [
                        0 =>  [
                            'id'          =>  $contentBoard->id,
                            'board_id' =>  1,
                        ],
                        1 =>  [
                            'board_id'  =>  1
                        ]
                    ],
                    'content_info_boards' =>  [
                        0 =>  [
                            'id'          =>  $contentInfoBoard->id,
                            'board_id' =>  1,
                        ],
                        1 =>  [
                            'board_id'  =>  1
                        ]
                    ],
                    'content_schools' =>  [
                        0 =>  [
                            'id'          =>  $contentSchool->id,
                            'company_id' =>  '1',
                        ],
                        1 =>  [
                            'company_id'  =>  '1'
                        ]
                    ],
                ]
            ])
            ->assertJsonStructureExact([
                'data'  => [
                    'id',
                    'content_name',
                    'content_type',
                    'reading_time',
                    'content_metadata',
                    'easy_content',
                    'med_content',
                    'created_at',
                    'updated_at',
                    'learning_outcome',
                    'for_school_type',
                    'specific_to',
                    'school_id',
                    'original_content',
                    'written_by_name',
                    'grade_id',
                    'board_id',
                    'info_board_id',
                    'publication',
                    'adapted_by',
                    'content_subjects',
                    'content_medias',

                    'content_descriptions',
                    'content_hidden_classcodes',
                    'content_lock_classcodes',
                    'content_assign_to_reads',
                    'content_grades',
                    'content_boards',
                    'content_info_boards',
                    'content_schools',
                ]
            ]);

        // 1 Delete + 1 New
        $payload = [
            'id'          =>  $content->id,
            'content_name'     => 'content_name',
            'content_type'     => 'content_type',
            'written_by_name'    => 1,
            'reading_time'     => 'reading_time',
            'content_metadata' => 'content_metadata',
            'easy_content'     => 'easy_content',
            'med_content'      => 'med_content',
            'original_content'     => 'original_content',
            'content_subjects' =>  [
                0 =>  [
                    'id'        =>  $contentSubject->id,
                    'subject_id' =>  '1'
                ]
            ],
            'content_medias' => [
                0 => [
                    'id' => $contentMedia->id,
                    'mediapath' => 'mediapath',
                ]
            ],
            'content_hidden_classcodes' =>  [
                0 =>  [
                    'id' => $contentHiddenClasscode->id,
                    'classcode_id' =>  1,
                ]
            ],
            'content_lock_classcodes' =>  [
                0 =>  [
                    'id' => $contentLockClasscode->id,
                    'classcode_id' =>  1,
                ]
            ],
            'content_assign_to_reads' =>  [
                0 =>  [
                    'id' => $contentAssignToRead->id,
                    'classcode_id' =>  1,
                ]
            ],
            'content_grades' =>  [
                0 =>  [
                    'id' => $contentGrade->id,
                    'grade_id' =>  1,
                ]
            ],
            'content_boards' =>  [
                0 =>  [
                    'id'          =>  $contentBoard->id,
                    'board_id' =>  1,
                ],
            ],
            'content_info_boards' =>  [
                0 =>  [
                    'id'          =>  $contentInfoBoard->id,
                    'board_id' =>  1,
                ],
            ],
            'content_schools' =>  [
                0 =>  [
                    'id'          =>  $contentBoard->id,
                    'company_id' =>  1,
                ],
            ],
        ];

        $this->json('post', '/api/contents', $payload, $this->headers)
            ->assertStatus(201)
            ->assertJson([
                'data'    => [
                    'id'          =>  $content->id,
                    'content_name'     => 'content_name',
                    'content_type'     => 'content_type',
                    'written_by_name'    => 1,
                    'reading_time'     => 'reading_time',
                    'content_metadata' => 'content_metadata',
                    'easy_content'     => 'easy_content',
                    'med_content'      => 'med_content',
                    'original_content'     => 'original_content',
                    'content_subjects' =>  [
                        0 =>  [
                            'id'        =>  $contentSubject->id,
                            'subject_id' =>  '1'
                        ]
                    ],
                    'content_medias' => [
                        0 => [
                            'id' => $contentMedia->id,
                            'mediapath' => 'mediapath',
                        ]
                    ],
                    'content_hidden_classcodes' =>  [
                        0 =>  [
                            'id' => $contentHiddenClasscode->id,
                            'classcode_id' =>  '1',
                        ]
                    ],
                    'content_lock_classcodes' =>  [
                        0 =>  [
                            'id' => $contentLockClasscode->id,
                            'classcode_id' =>  '1',
                        ]
                    ],
                    'content_assign_to_reads' =>  [
                        0 =>  [
                            'id' => $contentAssignToRead->id,
                            'classcode_id' =>  '1',
                        ]
                    ],
                    'content_grades' =>  [
                        0 =>  [
                            'id' => $contentGrade->id,
                            'grade_id' =>  '1',
                        ]
                    ],
                    'content_boards' =>  [
                        0 =>  [
                            'id'          =>  $contentBoard->id,
                            'board_id' =>  1,
                        ],
                    ],
                    'content_info_boards' =>  [
                        0 =>  [
                            'id'          =>  $contentInfoBoard->id,
                            'board_id' =>  1,
                        ],
                    ],
                    'content_schools' =>  [
                        0 =>  [
                            'id'          =>  $contentBoard->id,
                            'company_id' =>  1,
                        ],
                    ],
                ]
            ])
            ->assertJsonStructureExact([
                'data'  => [
                    'id',
                    'content_name',
                    'content_type',
                    'reading_time',
                    'content_metadata',
                    'easy_content',
                    'med_content',
                    'created_at',
                    'updated_at',
                    'learning_outcome',
                    'for_school_type',
                    'specific_to',
                    'school_id',
                    'original_content',
                    'written_by_name',
                    'grade_id',
                    'board_id',
                    'info_board_id',
                    'publication',
                    'adapted_by',
                    'content_subjects',
                    'content_medias',
                    'content_descriptions',
                    'content_hidden_classcodes',
                    'content_lock_classcodes',
                    'content_assign_to_reads',
                    'content_grades',
                    'content_boards',
                    'content_info_boards',
                    'content_schools',
                ]
            ]);
    }
    /** @test */
    function delete_content()
    {
        $this->json('delete', '/api/contents/1', [], $this->headers)
            ->assertStatus(204);

        $this->assertCount(0, Content::all());
    }
}
