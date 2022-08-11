<?php

namespace Tests\Feature;

use App\ContentMedia;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ContentMediaTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp()
    {
        parent::setUp();

        factory(ContentMedia::class)->create([
            'content_id' => 1,
            'mediapath' => 'mediapath',
        ]);

        $this->payload = [
            'content_id' => 1,
            'mediapath' => 'mediapath',
        ];
    }

    /** @test */
    function it_requires_content_media_name()
    {
        $this->json('post', '/api/content_medias', [], $this->headers)
            ->assertStatus(422)
            ->assertExactJson([
                "errors"  =>  [
                    "content_id"  =>  ["The content id field is required."]
                ],
                "message" =>  "The given data was invalid."
            ]);
    }

    /** @test */
    function add_new_content_media()
    {
        $this->disableEH();
        $this->json('post', '/api/content_medias', $this->payload, $this->headers)
            ->assertStatus(201)
            ->assertJson([
                'data'  =>  [
                    'content_id' => 1,
                    'mediapath' => 'mediapath',
                ]
            ])
            ->assertJsonStructureExact([
                'data'  =>  [
                    'content_id',
                    'mediapath',
                    'updated_at',
                    'created_at',
                    'id'
                ]
            ]);
    }

    /** @test */
    function list_of_content_medias()
    {
        $this->disableEH();
        $this->json('GET', '/api/content_medias', [], $this->headers)
            ->assertStatus(200)
            ->assertJsonStructure([
                'data'  =>  [
                    0 =>  [
                        'content_id',
                        'mediapath',
                    ]
                ]
            ]);
        $this->assertCount(1, ContentMedia::all());
    }

    /** @test */
    function show_single_content_media()
    {
        $this->disableEH();
        $this->json('get', "/api/content_medias/1", [],  $this->headers)
            ->assertStatus(200)
            ->assertJson([
                'data'  => [
                    'content_id' => 1,
                    'mediapath' => 'mediapath',
                ]
            ])
            ->assertJsonStructureExact([
                'data'    => [
                    'id',
                    'content_id',
                    'mediapath',
                    'created_at',
                    'updated_at'
                ]
            ]);
    }

    /** @test */
    function update_single_content_media()
    {
        $this->disableEH();
        $payload = [
            'content_id' => 1,
            'mediapath' => 'mediapath',
        ];

        $this->json('patch', '/api/content_medias/1', $payload, $this->headers)
            ->assertStatus(200)
            ->assertJson([
                'data'    => [
                    'content_id' => 1,
                    'mediapath' => 'mediapath',
                ]
            ])
            ->assertJsonStructureExact([
                'data'    => [
                    'id',
                    'content_id',
                    'mediapath',
                    'created_at',
                    'updated_at'
                ]
            ]);
    }

    /** @test */
    function delete_content_media()
    {
        $this->json('delete', '/api/content_medias/1', [], $this->headers)
            ->assertStatus(204);

        $this->assertCount(0, ContentMedia::all());
    }
}
