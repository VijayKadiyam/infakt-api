<?php

namespace Tests\Feature;

use App\CollectionEpaper;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CollectionEpaperTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp()
    {
        parent::setUp();

        factory(CollectionEpaper::class)->create([
            'epaper_collection_id' => 1,
            'toi_article_id'        => 1,
            'et_article_id'         => 1,
            'is_deleted' => 0,
        ]);

        $this->payload = [
            'epaper_collection_id' => 1,
            'toi_article_id'        => 1,
            'et_article_id'         => 1,
            'is_deleted' => 0,
        ];
    }

    /** @test */
    function it_requires_following_details()
    {
        $this->json('post', '/api/collection_epapers/toi_store', [], $this->headers)
            ->assertStatus(422)
            ->assertExactJson([
                "errors"  =>  [
                    "epaper_collection_id"        =>  ["The epaper collection id field is required."],
                ],
                "message" =>  "The given data was invalid."
            ]);
    }

    /** @test */
    function add_new_collection_epaper()
    {
        $this->disableEH();

        $this->json('post', '/api/collection_epapers/toi_store', $this->payload, $this->headers)
            ->assertStatus(201)
            ->assertJson([
                'data'   => [
                    'epaper_collection_id'         => 1,
                    'toi_article_id'        => 1,
                    'et_article_id'         => 1,
                    'is_deleted'            => 0,
                ]
            ])
            ->assertJsonStructureExact([
                'data'   => [
                    'epaper_collection_id',
                    'toi_article_id',
                    'et_article_id',
                    'is_deleted',
                    'updated_at',
                    'created_at',
                    'id'
                ]
            ]);
    }

    /** @test */
    function list_of_collection_epapers()
    {
        $this->disableEH();
        $this->json('GET', '/api/collection_epapers', [], $this->headers)
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    0 =>  [
                        'epaper_collection_id',
                        'toi_article_id',
                        'et_article_id',
                        'is_deleted',
                    ]
                ]
            ]);
        $this->assertCount(1, CollectionEpaper::all());
    }

    /** @test */
    function show_single_collection_epaper()
    {

        $this->json('get', "/api/collection_epapers/1", [], $this->headers)
            ->assertStatus(200)
            ->assertJson([
                'data'  => [
                    'epaper_collection_id' => 1,
                    'toi_article_id'        => 1,
                    'et_article_id'         => 1,
                    'is_deleted' => 0,
                ]
            ]);
    }

    /** @test */
    function update_single_collection_epaper()
    {
        $payload = [
            'epaper_collection_id' => 1,
            'toi_article_id'        => 1,
            'et_article_id'         => 1,
            'is_deleted' => 0,
        ];

        $this->json('patch', '/api/collection_epapers/1', $payload, $this->headers)
            ->assertStatus(200)
            ->assertJson([
                'data'    => [
                    'epaper_collection_id' => 1,
                    'toi_article_id'        => 1,
                    'et_article_id'         => 1,
                    'is_deleted' => 0,
                ]
            ])
            ->assertJsonStructureExact([
                'data'  => [
                    'id',
                    'epaper_collection_id',
                    'toi_article_id',
                    'et_article_id',
                    'is_deleted',
                    'created_at',
                    'updated_at',
                ]
            ]);
    }

    /** @test */
    function delete_collection_epaper()
    {
        $this->json('delete', '/api/collection_epapers/1', [], $this->headers)
            ->assertStatus(204);

        $this->assertCount(0, CollectionEpaper::all());
    }
}
