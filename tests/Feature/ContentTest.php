<?php

namespace Tests\Feature;

use App\Content;
use App\ContentMedia;
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
            'written_by_id'    => 1,
            'reading_time'     => 'reading_time',
            'content_metadata' => 'content_metadata',
            'easy_content'     => 'easy_content',
            'med_content'      => 'med_content',
            'hard_content'     => 'hard_content',
        ]);

        $this->payload = [
            'content_name'     => 'content_name',
            'content_type'     => 'content_type',
            'written_by_id'    => 1,
            'reading_time'     => 'reading_time',
            'content_metadata' => 'content_metadata',
            'easy_content'     => 'easy_content',
            'med_content'      => 'med_content',
            'hard_content'     => 'hard_content',
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
                    'written_by_id'    => 1,
                    'reading_time'     => 'reading_time',
                    'content_metadata' => 'content_metadata',
                    'easy_content'     => 'easy_content',
                    'med_content'      => 'med_content',
                    'hard_content'     => 'hard_content',
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
                ]
            ])
            ->assertJsonStructureExact([
                'data'  =>  [
                    'content_name',
                    'content_type',
                    'written_by_id',
                    'reading_time',
                    'content_metadata',
                    'easy_content',
                    'med_content',
                    'hard_content',
                    'updated_at',
                    'created_at',
                    'id',
                    'content_subjects',
                    'content_medias',
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
                        'written_by_id',
                        'reading_time',
                        'content_metadata',
                        'easy_content',
                        'med_content',
                        'hard_content',
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
                    'written_by_id'    => 1,
                    'reading_time'     => 'reading_time',
                    'content_metadata' => 'content_metadata',
                    'easy_content'     => 'easy_content',
                    'med_content'      => 'med_content',
                    'hard_content'     => 'hard_content',
                ]
            ])
            ->assertJsonStructureExact([
                'data'    => [
                    'id',
                    'content_name',
                    'content_type',
                    'written_by_id',
                    'reading_time',
                    'content_metadata',
                    'easy_content',
                    'med_content',
                    'hard_content',
                    'created_at',
                    'updated_at'
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
            'written_by_id'    => 1,
            'reading_time'     => 'reading_time',
            'content_metadata' => 'content_metadata',
            'easy_content'     => 'easy_content',
            'med_content'      => 'med_content',
            'hard_content'     => 'hard_content',
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
        ];

        $this->json('patch', '/api/contents/1', $payload, $this->headers)
            ->assertStatus(200)
            ->assertJson([
                'data'    => [
                    'content_name'     => 'content_name',
                    'content_type'     => 'content_type',
                    'written_by_id'    => 1,
                    'reading_time'     => 'reading_time',
                    'content_metadata' => 'content_metadata',
                    'easy_content'     => 'easy_content',
                    'med_content'      => 'med_content',
                    'hard_content'     => 'hard_content',
                ]
            ])
            ->assertJsonStructureExact([
                'data'    => [
                    'id',
                    'content_name',
                    'content_type',
                    'written_by_id',
                    'reading_time',
                    'content_metadata',
                    'easy_content',
                    'med_content',
                    'hard_content',
                    'created_at',
                    'updated_at'
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
            'written_by_id'    => 1,
            'reading_time'     => 'reading_time',
            'content_metadata' => 'content_metadata',
            'easy_content'     => 'easy_content',
            'med_content'      => 'med_content',
            'hard_content'     => 'hard_content',
        ]);
        $contentSubject = factory(ContentSubject::class)->create([
            'content_id' =>  $content->id
        ]);
        $contentMedia = factory(ContentMedia::class)->create([
            'content_id' =>  $content->id
        ]);

        // Old Edit + No Delete + 1 New
        $payload = [
            'id'          =>  $content->id,
            'content_name'     => 'content_name',
            'content_type'     => 'content_type',
            'written_by_id'    => 1,
            'reading_time'     => 'reading_time',
            'content_metadata' => 'content_metadata',
            'easy_content'     => 'easy_content',
            'med_content'      => 'med_content',
            'hard_content'     => 'hard_content',
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
        ];

        $this->json('post', '/api/contents', $payload, $this->headers)
            ->assertStatus(201)
            ->assertJson([
                'data'    => [
                    'id'          =>  $content->id,
                    'content_name'     => 'content_name',
                    'content_type'     => 'content_type',
                    'written_by_id'    => 1,
                    'reading_time'     => 'reading_time',
                    'content_metadata' => 'content_metadata',
                    'easy_content'     => 'easy_content',
                    'med_content'      => 'med_content',
                    'hard_content'     => 'hard_content',
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
                ]
            ])
            ->assertJsonStructureExact([
                'data'  => [
                    'id',
                    'content_name',
                    'content_type',
                    'written_by_id',
                    'reading_time',
                    'content_metadata',
                    'easy_content',
                    'med_content',
                    'hard_content',
                    'created_at',
                    'updated_at',
                    'content_subjects',
                    'content_medias',
                ]
            ]);

        // 1 Delete + 1 New
        $payload = [
            'id'          =>  $content->id,
            'content_name'     => 'content_name',
            'content_type'     => 'content_type',
            'written_by_id'    => 1,
            'reading_time'     => 'reading_time',
            'content_metadata' => 'content_metadata',
            'easy_content'     => 'easy_content',
            'med_content'      => 'med_content',
            'hard_content'     => 'hard_content',
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
            ]
        ];

        $this->json('post', '/api/contents', $payload, $this->headers)
            ->assertStatus(201)
            ->assertJson([
                'data'    => [
                    'id'          =>  $content->id,
                    'content_name'     => 'content_name',
                    'content_type'     => 'content_type',
                    'written_by_id'    => 1,
                    'reading_time'     => 'reading_time',
                    'content_metadata' => 'content_metadata',
                    'easy_content'     => 'easy_content',
                    'med_content'      => 'med_content',
                    'hard_content'     => 'hard_content',
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
                    ]
                ]
            ])
            ->assertJsonStructureExact([
                'data'  => [
                    'id',
                    'content_name',
                    'content_type',
                    'written_by_id',
                    'reading_time',
                    'content_metadata',
                    'easy_content',
                    'med_content',
                    'hard_content',
                    'created_at',
                    'updated_at',
                    'content_subjects',
                    'content_medias',
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
