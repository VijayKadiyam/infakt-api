<?php

namespace Tests\Feature;

use App\CollectionContent;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CollectionContentTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp()
    {
        parent::setUp();

        factory(CollectionContent::class)->create([
            'collection_id' => 1,
            'content_id' => 1,
            'is_deleted' => 0,
        ]);

        $this->payload = [
            'collection_id' => 1,
            'content_id' => 1,
            'is_deleted' => 0,
        ];
    }

    /** @test */
    function it_requires_following_details()
    {
        $this->json('post', '/api/collection_contents', [], $this->headers)
            ->assertStatus(422)
            ->assertExactJson([
                "errors"  =>  [
                    "collection_id"        =>  ["The collection id field is required."],
                ],
                "message" =>  "The given data was invalid."
            ]);
    }

    /** @test */
    function add_new_collection_content()
    {
        $this->disableEH();

        $this->json('post', '/api/collection_contents', $this->payload, $this->headers)
            ->assertStatus(201)
            ->assertJson([
                'data'   => [
                    'collection_id' => 1,
                    'content_id' => 1,
                    'is_deleted' => 0,
                ]
            ])
            ->assertJsonStructureExact([
                'data'   => [
                    'collection_id',
                    'content_id',
                    'is_deleted',
                    'updated_at',
                    'created_at',
                    'id'
                ]
            ]);
    }

    /** @test */
    function list_of_collection_contents()
    {
        $this->disableEH();
        $this->json('GET', '/api/collection_contents', [], $this->headers)
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    0 =>  [
                        'collection_id',
                        'content_id',
                        'is_deleted',
                    ]
                ]
            ]);
        $this->assertCount(1, CollectionContent::all());
    }

    /** @test */
    function show_single_collection_content()
    {

        $this->json('get', "/api/collection_contents/1", [], $this->headers)
            ->assertStatus(200)
            ->assertJson([
                'data'  => [
                    'collection_id' => 1,
                    'content_id' => 1,
                    'is_deleted' => 0,
                ]
            ]);
    }

    /** @test */
    function update_single_collection_content()
    {
        $payload = [
            'collection_id' => 1,
            'content_id' => 1,
            'is_deleted' => 0,
        ];

        $this->json('patch', '/api/collection_contents/1', $payload, $this->headers)
            ->assertStatus(200)
            ->assertJson([
                'data'    => [
                    'collection_id' => 1,
                    'content_id' => 1,
                    'is_deleted' => 0,
                ]
            ])
            ->assertJsonStructureExact([
                'data'  => [
                    'id',
                    'collection_id',
                    'content_id',
                    'is_deleted',
                    'created_at',
                    'updated_at',
                ]
            ]);
    }

    /** @test */
    function delete_collection_content()
    {
        $this->json('delete', '/api/collection_contents/1', [], $this->headers)
            ->assertStatus(204);

        $this->assertCount(0, CollectionContent::all());
    }
}
