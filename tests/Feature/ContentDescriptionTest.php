<?php

namespace Tests\Feature;

use App\ContentDescription;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ContentDescriptionTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp()
    {
        parent::setUp();

        factory(ContentDescription::class)->create([
            'content_id' => 1,
            'level' => 'level',
            'title' => 'title',
            'description' => 'description',
        ]);

        $this->payload = [
            'content_id' => 1,
            'level' => 'level',
            'title' => 'title',
            'description' => 'description',
        ];
    }

    /** @test */
    function it_requires_following_details()
    {
        $this->json('post', '/api/content_descriptions', [], $this->headers)
            ->assertStatus(422)
            ->assertExactJson([
                "errors"  =>  [
                    "level"        =>  ["The level field is required."],
                ],
                "message" =>  "The given data was invalid."
            ]);
    }

    /** @test */
    function add_new_content_description()
    {
        $this->disableEH();

        $this->json('post', '/api/content_descriptions', $this->payload, $this->headers)
            ->assertStatus(201)
            ->assertJson([
                'data'   => [
                    'content_id' => 1,
                    'level' => 'level',
                    'title' => 'title',
                    'description' => 'description',
                ]
            ])
            ->assertJsonStructureExact([
                'data'   => [
                    'content_id',
                    'level',
                    'title',
                    'description',
                    'updated_at',
                    'created_at',
                    'id'
                ]
            ]);
    }

    /** @test */
    function list_of_content_descriptions()
    {
        $this->disableEH();
        $this->json('GET', '/api/content_descriptions', [], $this->headers)
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    0 =>  [
                        'content_id',
                        'level',
                        'title',
                        'description',
                    ]
                ]
            ]);
        $this->assertCount(1, ContentDescription::all());
    }

    /** @test */
    function show_single_content_description()
    {

        $this->json('get', "/api/content_descriptions/1", [], $this->headers)
            ->assertStatus(200)
            ->assertJson([
                'data'  => [
                    'content_id' => 1,
                    'level' => 'level',
                    'title' => 'title',
                    'description' => 'description',
                ]
            ]);
    }

    /** @test */
    function update_single_content_description()
    {
        $payload = [
            'content_id' => 1,
            'level' => 'level',
            'title' => 'title',
            'description' => 'description',
        ];

        $this->json('patch', '/api/content_descriptions/1', $payload, $this->headers)
            ->assertStatus(200)
            ->assertJson([
                'data'    => [
                    'content_id' => 1,
                    'level' => 'level',
                    'title' => 'title',
                    'description' => 'description',
                ]
            ])
            ->assertJsonStructureExact([
                'data'  => [
                    'id',
                    'content_id',
                    'level',
                    'title',
                    'description',
                    'created_at',
                    'updated_at',
                ]
            ]);
    }

    /** @test */
    function delete_content_description()
    {
        $this->json('delete', '/api/content_descriptions/1', [], $this->headers)
            ->assertStatus(204);

        $this->assertCount(0, ContentDescription::all());
    }
}
