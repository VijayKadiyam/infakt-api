<?php

namespace Tests\Feature;

use App\Search;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SearchTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp()
    {
        parent::setUp();

        $this->company = factory(\App\Company::class)->create([
            'name' => 'test'
        ]);

        $this->headers['company-id'] = $this->company->id;

        factory(Search::class)->create([
            'company_id' =>  $this->company->id,
            'user_id' => 1,
            'search_type' => 'search_type',
            'search' => 'search',
        ]);

        $this->payload = [
            'user_id' => 1,
            'search_type' => 'search_type',
            'search' => 'search',
        ];
    }

    /** @test */
    function it_requires_following_details()
    {
        $this->json('post', '/api/searches', [], $this->headers)
            ->assertStatus(422)
            ->assertExactJson([
                "errors"  =>  [
                    "user_id"        =>  ["The user id field is required."],
                ],
                "message" =>  "The given data was invalid."
            ]);
    }

    /** @test */
    function add_new_search()
    {
        $this->disableEH();

        $this->json('post', '/api/searches', $this->payload, $this->headers)
            ->assertStatus(201)
            ->assertJson([
                'data'   => [
                    'user_id' => 1,
                    'search_type' => 'search_type',
                    'search' => 'search',
                ]
            ])
            ->assertJsonStructureExact([
                'data'   => [
                    'user_id',
                    'search_type',
                    'search',
                    'company_id',
                    'updated_at',
                    'created_at',
                    'id'
                ]
            ]);
    }

    /** @test */
    function list_of_searches()
    {
        $this->disableEH();
        $this->json('GET', '/api/searches', [], $this->headers)
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    0 =>  [
                        'user_id',
                        'search_type',
                        'search',
                    ]
                ]
            ]);
        $this->assertCount(1, Search::all());
    }

    /** @test */
    function show_single_searche()
    {

        $this->json('get', "/api/searches/1", [], $this->headers)
            ->assertStatus(200)
            ->assertJson([
                'data'  => [
                    'user_id' => 1,
                    'search_type' => 'search_type',
                    'search' => 'search',
                ]
            ]);
    }

    /** @test */
    function update_single_searche()
    {
        $payload = [
            'user_id' => 1,
            'search_type' => 'search_type',
            'search' => 'search',
        ];

        $this->json('patch', '/api/searches/1', $payload, $this->headers)
            ->assertStatus(200)
            ->assertJson([
                'data'    => [
                    'user_id' => 1,
                    'search_type' => 'search_type',
                    'search' => 'search',
                ]
            ])
            ->assertJsonStructureExact([
                'data'  => [
                    'id',
                    'company_id',
                    'user_id',
                    'search_type',
                    'search',
                    'created_at',
                    'updated_at',
                ]
            ]);
    }

    /** @test */
    function delete_searche()
    {
        $this->json('delete', '/api/searches/1', [], $this->headers)
            ->assertStatus(204);

        $this->assertCount(0, Search::all());
    }
}
