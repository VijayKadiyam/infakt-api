<?php

namespace Tests\Feature;

use App\ContentCategory;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ContentCategoryTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp()
    {
        parent::setUp();

        factory(ContentCategory::class)->create([
            'content_id' => 1,
            'category_id' => 1,
        ]);

        $this->payload = [
            'content_id' => 1,
            'category_id' => 1,
        ];
    }

    /** @test */
    function it_requires_following_details()
    {
        $this->json('post', '/api/content_categories', [], $this->headers)
            ->assertStatus(422)
            ->assertExactJson([
                "errors"  =>  [
                    "content_id"        =>  ["The content id field is required."],
                ],
                "message" =>  "The given data was invalid."
            ]);
    }

    /** @test */
    function add_new_content_category()
    {
        $this->disableEH();

        $this->json('post', '/api/content_categories', $this->payload, $this->headers)
            ->assertStatus(201)
            ->assertJson([
                'data'   => [
                    'content_id' => 1,
                    'category_id' => 1
                ]
            ])
            ->assertJsonStructureExact([
                'data'   => [
                    'content_id',
                    'category_id',
                    'updated_at',
                    'created_at',
                    'id'
                ]
            ]);
    }

    /** @test */
    function list_of_content_categories()
    {
        $this->disableEH();
        $this->json('GET', '/api/content_categories', [], $this->headers)
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    0 =>  [
                        'content_id',
                        'category_id',
                    ]
                ]
            ]);
        $this->assertCount(1, ContentCategory::all());
    }

    /** @test */
    function show_single_content_category()
    {

        $this->json('get', "/api/content_categories/1", [], $this->headers)
            ->assertStatus(200)
            ->assertJson([
                'data'  => [
                    'content_id' => 1,
                    'category_id' => 1,
                ]
            ]);
    }

    /** @test */
    function update_single_content_category()
    {
        $payload = [
            'content_id' => 1,
            'category_id' => 1,
        ];

        $this->json('patch', '/api/content_categories/1', $payload, $this->headers)
            ->assertStatus(200)
            ->assertJson([
                'data'    => [
                    'content_id' => 1,
                    'category_id' => 1,
                ]
            ])
            ->assertJsonStructureExact([
                'data'  => [
                    'id',
                    'content_id',
                    'category_id',
                    'created_at',
                    'updated_at',
                ]
            ]);
    }

    /** @test */
    function delete_content_category()
    {
        $this->json('delete', '/api/content_categories/1', [], $this->headers)
            ->assertStatus(204);

        $this->assertCount(0, ContentCategory::all());
    }
}
