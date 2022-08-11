<?php

namespace Tests\Feature;

use App\ContentSubject;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ContentSujectTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp()
    {
        parent::setUp();

        factory(ContentSubject::class)->create([
            'content_id' => 1,
            'subject_id' => 1,
        ]);

        $this->payload = [
            'content_id' => 1,
            'subject_id' => 1,
        ];
    }

    /** @test */
    function it_requires_content_subject_name()
    {
        $this->json('post', '/api/content_subjects', [], $this->headers)
            ->assertStatus(422)
            ->assertExactJson([
                "errors"  =>  [
                    "content_id"  =>  ["The content id field is required."]
                ],
                "message" =>  "The given data was invalid."
            ]);
    }

    /** @test */
    function add_new_content_subject()
    {
        $this->disableEH();
        $this->json('post', '/api/content_subjects', $this->payload, $this->headers)
            ->assertStatus(201)
            ->assertJson([
                'data'  =>  [
                    'content_id' => 1,
                    'subject_id' => 1,
                ]
            ])
            ->assertJsonStructureExact([
                'data'  =>  [
                    'content_id',
                    'subject_id',
                    'updated_at',
                    'created_at',
                    'id'
                ]
            ]);
    }

    /** @test */
    function list_of_content_subjects()
    {
        $this->disableEH();
        $this->json('GET', '/api/content_subjects', [], $this->headers)
            ->assertStatus(200)
            ->assertJsonStructure([
                'data'  =>  [
                    0 =>  [
                        'content_id',
                        'subject_id',
                    ]
                ]
            ]);
        $this->assertCount(1, ContentSubject::all());
    }

    /** @test */
    function show_single_content_subject()
    {
        $this->disableEH();
        $this->json('get', "/api/content_subjects/1", [],  $this->headers)
            ->assertStatus(200)
            ->assertJson([
                'data'  => [
                    'content_id' => 1,
                    'subject_id' => 1,
                ]
            ])
            ->assertJsonStructureExact([
                'data'    => [
                    'id',
                    'content_id',
                    'subject_id',
                    'created_at',
                    'updated_at'
                ]
            ]);
    }

    /** @test */
    function update_single_content_subject()
    {
        $this->disableEH();
        $payload = [
            'content_id' => 1,
            'subject_id' => 1,
        ];

        $this->json('patch', '/api/content_subjects/1', $payload, $this->headers)
            ->assertStatus(200)
            ->assertJson([
                'data'    => [
                    'content_id' => 1,
                    'subject_id' => 1,
                ]
            ])
            ->assertJsonStructureExact([
                'data'    => [
                    'id',
                    'content_id',
                    'subject_id',
                    'created_at',
                    'updated_at'
                ]
            ]);
    }

    /** @test */
    function delete_content_subject()
    {
        $this->json('delete', '/api/content_subjects/1', [], $this->headers)
            ->assertStatus(204);

        $this->assertCount(0, ContentSubject::all());
    }
}
