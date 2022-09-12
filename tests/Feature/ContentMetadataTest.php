<?php

namespace Tests\Feature;

use App\ContentMetadata;
use App\ContentMetadataClasscode;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ContentMetadataTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp()
    {
        parent::setUp();

        factory(\App\ContentMetadata::class)->create([
            'content_id' => 1,
            'metadata_type' => 'metadata_type',
            'color_class' => 'color_class',
            'selected_text' => 'selected_text',
        ]);

        $this->payload = [
            'content_id' => 1,
            'metadata_type' => 'metadata_type',
            'color_class' => 'color_class',
            'selected_text' => 'selected_text',
            'content_metadata_classcodes' =>  [
                0 =>  [
                    'classcode_id' =>  1,
                ]
            ],
        ];
    }

    /** @test */
    function it_requires_content_metadata_name()
    {
        $this->json('post', '/api/content_metadatas', [], $this->headers)
            ->assertStatus(422)
            ->assertExactJson([
                "errors"  =>  [
                    "content_id"          =>  ["The content id field is required."],
                ],
                "message" =>  "The given data was invalid."
            ]);
    }

    /** @test */
    function add_new_content_metadata()
    {
        $this->disableEH();
        $this->json('post', '/api/content_metadatas', $this->payload, $this->headers)
            ->assertStatus(201)
            ->assertJson([
                'data'  =>  [
                    'content_id' => 1,
                    'metadata_type' => 'metadata_type',
                    'color_class' => 'color_class',
                    'selected_text' => 'selected_text',
                    'content_metadata_classcodes' =>  [
                        0 =>  [
                            'classcode_id' =>  1,
                        ]
                    ],
                ]
            ])
            ->assertJsonStructureExact([
                'data'  =>  [
                    'content_id',
                    'metadata_type',
                    'color_class',
                    'selected_text',
                    'updated_at',
                    'created_at',
                    'id',
                    'content_metadata_classcodes'
                ]
            ]);
    }

    /** @test */
    function list_of_content_metadatas()
    {
        $this->disableEH();
        $this->json('GET', '/api/content_metadatas', [], $this->headers)
            ->assertStatus(200)
            ->assertJsonStructure([
                'data'  =>  [
                    0 =>  [
                        'content_id',
                        'metadata_type',
                        'color_class',
                        'selected_text',
                        'user_id',
                    ]
                ]
            ]);
        $this->assertCount(1, ContentMetadata::all());
    }

    /** @test */
    function show_single_content_metadata()
    {
        $this->disableEH();
        $this->json('get', "/api/content_metadatas/1", [],  $this->headers)
            ->assertStatus(200)
            ->assertJson([
                'data'  => [
                    'content_id' => 1,
                    'metadata_type' => 'metadata_type',
                    'color_class' => 'color_class',
                    'selected_text' => 'selected_text',
                ]
            ])
            ->assertJsonStructureExact([
                'data'    => [
                    'id',
                    'content_id',
                    'metadata_type',
                    'color_class',
                    'selected_text',
                    'created_at',
                    'updated_at',
                    'annotation',
                    'user_id',
                    // 'content_metadata_classcodes'
                ]
            ]);
    }

    /** @test */
    function update_single_content_metadata()
    {
        $this->disableEH();
        $payload = [
            'content_id' => 1,
            'metadata_type' => 'metadata_type',
            'color_class' => 'color_class',
            'selected_text' => 'selected_text',
            // 'content_metadata_classcodes' =>  [
            //     0 =>  [
            //         'classcode_id' =>  1,
            //     ]
            // ],
        ];

        $this->json('patch', '/api/content_metadatas/1', $payload, $this->headers)
            ->assertStatus(200)
            ->assertJson([
                'data'    => [
                    'content_id' => 1,
                    'metadata_type' => 'metadata_type',
                    'color_class' => 'color_class',
                    'selected_text' => 'selected_text',
                    // 'content_metadata_classcodes' =>  [
                    //     0 =>  [
                    //         'classcode_id' =>  1,
                    //     ]
                    // ],
                ]
            ])
            ->assertJsonStructureExact([
                'data'    => [
                    'id',
                    'content_id',
                    'metadata_type',
                    'color_class',
                    'selected_text',
                    'created_at',
                    'updated_at',
                    'annotation',
                    'user_id',
                ]
            ]);
    }

    /** @test */
    function update_single_content_metadata_nested()
    {
        $this->disableEH();

        $content_metadata = factory(ContentMetadata::class)->create([
            'content_id' => 1,
            'metadata_type' => 'metadata_type',
            'color_class' => 'color_class',
            'selected_text' => 'selected_text',
        ]);
        $contentMetadataClasscode = factory(ContentMetadataClasscode::class)->create([
            'content_metadata_id' =>  $content_metadata->id
        ]);

        // Old Edit + No Delete + 1 New
        $payload = [
            'id'          =>  $content_metadata->id,
            'content_id' => 1,
            'metadata_type' => 'metadata_type',
            'color_class' => 'color_class',
            'selected_text' => 'selected_text',
            'content_metadata_classcodes' =>  [
                0 =>  [
                    'classcode_id' =>  1,
                ]
            ],
        ];

        $this->json('post', '/api/content_metadatas', $payload, $this->headers)
            ->assertStatus(201)
            ->assertJson([
                'data'    => [
                    'id'          =>  $content_metadata->id,
                    'content_id' => 1,
                    'metadata_type' => 'metadata_type',
                    'color_class' => 'color_class',
                    'selected_text' => 'selected_text',
                    'content_metadata_classcodes' =>  [
                        0 =>  [
                            'classcode_id' =>  1,
                        ]
                    ],
                ]
            ])
            ->assertJsonStructureExact([
                'data'  => [
                    'id',
                    'content_id',
                    'metadata_type',
                    'color_class',
                    'selected_text',
                    'created_at',
                    'updated_at',
                    'annotation',
                    'user_id',
                    'content_metadata_classcodes'
                ]
            ]);

        // 1 Delete + 1 New
        $payload = [
            'id'          =>  $content_metadata->id,
            'content_id' => 1,
            'metadata_type' => 'metadata_type',
            'color_class' => 'color_class',
            'selected_text' => 'selected_text',
            'content_metadata_classcodes' =>  [
                0 =>  [
                    'classcode_id' =>  1,
                ]
            ],
        ];

        $this->json('post', '/api/content_metadatas', $payload, $this->headers)
            ->assertStatus(201)
            ->assertJson([
                'data'    => [
                    'id'          =>  $content_metadata->id,
                    'content_id' => 1,
                    'metadata_type' => 'metadata_type',
                    'color_class' => 'color_class',
                    'selected_text' => 'selected_text',
                    'content_metadata_classcodes' =>  [
                        0 =>  [
                            'classcode_id' =>  1,
                        ]
                    ],
                ]
            ])
            ->assertJsonStructureExact([
                'data'  => [
                    'id',
                    'content_id',
                    'metadata_type',
                    'color_class',
                    'selected_text',
                    'created_at',
                    'updated_at',
                    'annotation',
                    'user_id',
                    'content_metadata_classcodes'
                ]
            ]);
    }

    /** @test */
    function delete_content_metadata()
    {
        $this->json('delete', '/api/content_metadatas/1', [], $this->headers)
            ->assertStatus(204);

        $this->assertCount(0, ContentMetadata::all());
    }
}
