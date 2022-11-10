<?php

namespace Tests\Feature;

use App\EpaperCollection;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EpaperCollectionTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp()
    {
        parent::setUp();

        $this->company = factory(\App\Company::class)->create([
            'name' => 'test'
        ]);

        $this->headers['company-id'] = $this->company->id;

        factory(EpaperCollection::class)->create([
            'company_id' =>  $this->company->id,
            'user_id' => 1,
            'collection_name' => 'collection_name',
            'is_deleted' => 0,
        ]);

        $this->payload = [
            'user_id' => 1,
            'collection_name' => 'collection_name',
            'is_deleted' => 0,
        ];
    }

    /** @test */
    function it_requires_following_details()
    {
        $this->json('post', '/api/epaper_collections', [], $this->headers)
            ->assertStatus(422)
            ->assertExactJson([
                "errors"  =>  [
                    "user_id"        =>  ["The user id field is required."],
                    "collection_name"        =>  ["The collection name field is required."],
                ],
                "message" =>  "The given data was invalid."
            ]);
    }

    /** @test */
    function add_new_epaper_collection()
    {
        $this->disableEH();

        $this->json('post', '/api/epaper_collections', $this->payload, $this->headers)
            ->assertStatus(201)
            ->assertJson([
                'data'   => [
                    'user_id' => 1,
                    'collection_name' => 'collection_name',
                    'is_deleted' => 0,
                ]
            ])
            ->assertJsonStructureExact([
                'data'   => [
                    'user_id',
                    'collection_name',
                    'is_deleted',
                    'company_id',
                    'updated_at',
                    'created_at',
                    'id'
                ],
                'msg',
                'success',
            ]);
    }

    /** @test */
    function list_of_collections()
    {
        $this->disableEH();
        $this->json('GET', '/api/epaper_collections?user_id=1', [], $this->headers)
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    0 =>  [
                        'user_id',
                        'collection_name',
                        'is_deleted',
                    ]
                ]
            ]);
        $this->assertCount(1, EpaperCollection::all());
    }

    /** @test */
    function show_single_epaper_collection()
    {

        $this->json('get', "/api/epaper_collections/1", [], $this->headers)
            ->assertStatus(200)
            ->assertJson([
                'data'  => [
                    'user_id' => 1,
                    'collection_name' => 'collection_name',
                    'is_deleted' => 0,
                ]
            ]);
    }

    /** @test */
    function update_single_epaper_collection()
    {
        $payload = [
            'user_id' => 1,
            'collection_name' => 'collection_name',
            'is_deleted' => 0,
        ];

        $this->json('patch', '/api/epaper_collections/1', $payload, $this->headers)
            ->assertStatus(200)
            ->assertJson([
                'data'    => [
                    'user_id' => 1,
                    'collection_name' => 'collection_name',
                    'is_deleted' => 0,
                ]
            ])
            ->assertJsonStructureExact([
                'data'  => [
                    'id',
                    'company_id',
                    'user_id',
                    'collection_name',
                    'is_deleted',
                    'created_at',
                    'updated_at',
                ]
            ]);
    }

    /** @test */
    function delete_epaper_collection()
    {
        $this->json('delete', '/api/epaper_collections/1', [], $this->headers)
            ->assertStatus(204);

        $this->assertCount(0, EpaperCollection::all());
    }
}
