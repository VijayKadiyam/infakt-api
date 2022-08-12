<?php

namespace Tests\Feature;

use App\Bookmark;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BookmarkTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp()
    {
        parent::setUp();

        $this->company = factory(\App\Company::class)->create([
            'name' => 'test'
        ]);

        $this->headers['company-id'] = $this->company->id;

        factory(Bookmark::class)->create([
            'company_id' =>  $this->company->id,
            'user_id' => 1,
            'content_id' => 1,
            'is_deleted' => 0,
        ]);

        $this->payload = [
            'user_id' => 1,
            'content_id' => 1,
            'is_deleted' => 0,
        ];
    }

    /** @test */
    function it_requires_following_details()
    {
        $this->json('post', '/api/bookmarks', [], $this->headers)
            ->assertStatus(422)
            ->assertExactJson([
                "errors"  =>  [
                    "user_id"        =>  ["The user id field is required."],
                ],
                "message" =>  "The given data was invalid."
            ]);
    }

    /** @test */
    function add_new_bookmark()
    {
        $this->disableEH();

        $this->json('post', '/api/bookmarks', $this->payload, $this->headers)
            ->assertStatus(201)
            ->assertJson([
                'data'   => [
                    'user_id' => 1,
                    'content_id' => 1,
                    'is_deleted' => 0,
                ]
            ])
            ->assertJsonStructureExact([
                'data'   => [
                    'user_id',
                    'content_id',
                    'is_deleted',
                    'company_id',
                    'updated_at',
                    'created_at',
                    'id'
                ]
            ]);
    }

    /** @test */
    function list_of_bookmarks()
    {
        $this->disableEH();
        $this->json('GET', '/api/bookmarks', [], $this->headers)
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    0 =>  [
                        'user_id',
                        'content_id',
                        'is_deleted',
                    ]
                ]
            ]);
        $this->assertCount(1, Bookmark::all());
    }

    /** @test */
    function show_single_bookmark()
    {

        $this->json('get', "/api/bookmarks/1", [], $this->headers)
            ->assertStatus(200)
            ->assertJson([
                'data'  => [
                    'user_id' => 1,
                    'content_id' => 1,
                    'is_deleted' => 0,
                ]
            ]);
    }

    /** @test */
    function update_single_bookmark()
    {
        $payload = [
            'user_id' => 1,
            'content_id' => 1,
            'is_deleted' => 0,
        ];

        $this->json('patch', '/api/bookmarks/1', $payload, $this->headers)
            ->assertStatus(200)
            ->assertJson([
                'data'    => [
                    'user_id' => 1,
                    'content_id' => 1,
                    'is_deleted' => 0,
                ]
            ])
            ->assertJsonStructureExact([
                'data'  => [
                    'id',
                    'company_id',
                    'user_id',
                    'content_id',
                    'is_deleted',
                    'created_at',
                    'updated_at',
                ]
            ]);
    }

    /** @test */
    function delete_bookmark()
    {
        $this->json('delete', '/api/bookmarks/1', [], $this->headers)
            ->assertStatus(204);

        $this->assertCount(0, Bookmark::all());
    }
}
