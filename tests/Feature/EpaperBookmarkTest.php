<?php

namespace Tests\Feature;

use App\EpaperBookmark;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EpaperBookmarkTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp()
    {
        parent::setUp();

        $this->company = factory(\App\Company::class)->create([
            'name' => 'test'
        ]);

        $this->headers['company-id'] = $this->company->id;

        factory(EpaperBookmark::class)->create([
            'company_id' =>  $this->company->id,
            'user_id' => 1,
            // 'toi_article_id'  => 1,
            'et_article_id'   => 1,
            'is_deleted' => 0,
        ]);

        $this->payload = [
            'user_id' => 1,
            // 'toi_article_id'  => 1,
            'et_article_id'   => 1,
            'is_deleted' => 0,
        ];
    }

    /** @test */
    function it_requires_following_details()
    {
        $this->json('post', '/api/epaper_bookmarks', [], $this->headers)
            ->assertStatus(422)
            ->assertExactJson([
                "errors"  =>  [
                    "user_id"        =>  ["The user id field is required."],
                ],
                "message" =>  "The given data was invalid."
            ]);
    }

    /** @test */
    function add_new_et_epaper_bookmark()
    {
        $this->disableEH();

        $this->json('post', '/api/epaper_bookmarks/et_store', $this->payload, $this->headers)
            ->assertStatus(201)
            ->assertJson([
                'data'   => [
                    'user_id' => 1,
                    // 'toi_article_id'  => 1,
                    'et_article_id'   => 1,
                    'is_deleted' => false,
                ]
            ])
            ->assertJsonStructureExact([
                'data'   => [
                    'user_id',
                    // 'toi_article_id',
                    'et_article_id',
                    'is_deleted',
                    'company_id',
                    'updated_at',
                    'created_at',
                    'id'
                ],
                'msg' => 'ET Epaper Bookmark already exist.'
            ]);
    }
    /** @test */
    function add_new_toi_epaper_bookmark()
    {
        $this->disableEH();

        $this->json('post', '/api/epaper_bookmarks/toi_store', $this->payload, $this->headers)
            ->assertStatus(201)
            ->assertJson([
                'data'   => [
                    'user_id' => 1,
                    'toi_article_id'  => 1,
                    // 'et_article_id'   => 1,
                    'is_deleted' => false,
                ]
            ])
            ->assertJsonStructureExact([
                'data'   => [
                    'user_id',
                    'toi_article_id',
                    // 'et_article_id',
                    'is_deleted',
                    'company_id',
                    'updated_at',
                    'created_at',
                    'id'
                ],
                'msg' => 'TOI Epaper Bookmark already exist.'
            ]);
    }

    /** @test */
    function list_of_epaper_bookmarks()
    {
        $this->disableEH();
        $this->json('GET', '/api/epaper_bookmarks', [], $this->headers)
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    0 =>  [
                        'user_id',
                        // 'toi_article_id',
                        'et_article_id',
                        'is_deleted',
                    ]
                ]
            ]);
        $this->assertCount(1, EpaperBookmark::all());
    }

    /** @test */
    function show_single_epaper_bookmark()
    {

        $this->json('get', "/api/epaper_bookmarks/1", [], $this->headers)
            ->assertStatus(200)
            ->assertJson([
                'data'  => [
                    'user_id' => 1,
                    // 'toi_article_id'  => 1,
                    'et_article_id'   => 1,
                    'is_deleted' => 0,
                ]
            ]);
    }

    /** @test */
    function update_single_epaper_bookmark()
    {
        $payload = [
            'user_id' => 1,
            'toi_article_id'  => 1,
            'et_article_id'   => 1,
            'is_deleted' => 0,
        ];

        $this->json('patch', '/api/epaper_bookmarks/1', $payload, $this->headers)
            ->assertStatus(200)
            ->assertJson([
                'data'    => [
                    'user_id' => 1,
                    'toi_article_id'  => 1,
                    'et_article_id'   => 1,
                    'is_deleted' => 0,
                ]
            ])
            ->assertJsonStructureExact([
                'data'  => [
                    'id',
                    'company_id',
                    'user_id',
                    'toi_article_id',
                    'et_article_id',
                    'is_deleted',
                    'created_at',
                    'updated_at',
                ]
            ]);
    }

    /** @test */
    function delete_epaper_bookmark()
    {
        $this->json('delete', '/api/epaper_bookmarks/1', [], $this->headers)
            ->assertStatus(204);

        $this->assertCount(0, EpaperBookmark::all());
    }
}
