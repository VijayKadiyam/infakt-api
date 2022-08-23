<?php

namespace Tests\Feature;

use App\BookmarkClasscode;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BookmarkClasscodeTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp()
    {
        parent::setUp();

        $this->company = factory(\App\Company::class)->create([
            'name' => 'test'
        ]);

        $this->headers['company-id'] = $this->company->id;

        factory(BookmarkClasscode::class)->create([
            'company_id' =>  $this->company->id,
            'bookmark_id' => 1,
            'classcode_id' => 1,
        ]);

        $this->payload = [
            'bookmark_id' => 1,
            'classcode_id' => 1,
        ];
    }

    /** @test */
    function it_requires_following_details()
    {
        $this->json('post', '/api/bookmark_classcodes', [], $this->headers)
            ->assertStatus(422)
            ->assertExactJson([
                "errors"  =>  [
                    "bookmark_id"        =>  ["The bookmark id field is required."],
                ],
                "message" =>  "The given data was invalid."
            ]);
    }

    /** @test */
    function add_new_bookmark_classcode()
    {
        $this->disableEH();

        $this->json('post', '/api/bookmark_classcodes', $this->payload, $this->headers)
            ->assertStatus(201)
            ->assertJson([
                'data'   => [
                    'bookmark_id' => 1,
                    'classcode_id' => 1,
                ]
            ])
            ->assertJsonStructureExact([
                'data'   => [
                    'bookmark_id',
                    'classcode_id',
                    'company_id',
                    'updated_at',
                    'created_at',
                    'id'
                ]
            ]);
    }

    /** @test */
    function list_of_bookmark_classcodes()
    {
        $this->disableEH();
        $this->json('GET', '/api/bookmark_classcodes', [], $this->headers)
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    0 =>  [
                        'bookmark_id',
                        'classcode_id'
                    ]
                ]
            ]);
        $this->assertCount(1, BookmarkClasscode::all());
    }

    /** @test */
    function show_single_bookmark_classcode()
    {

        $this->json('get', "/api/bookmark_classcodes/1", [], $this->headers)
            ->assertStatus(200)
            ->assertJson([
                'data'  => [
                    'bookmark_id' => 1,
                    'classcode_id' => 1,
                ]
            ]);
    }

    /** @test */
    function update_single_bookmark_classcode()
    {
        $payload = [
            'bookmark_id' => 1,
            'classcode_id' => 1,
        ];

        $this->json('patch', '/api/bookmark_classcodes/1', $payload, $this->headers)
            ->assertStatus(200)
            ->assertJson([
                'data'    => [
                    'bookmark_id' => 1,
                    'classcode_id' => 1,
                ]
            ])
            ->assertJsonStructureExact([
                'data'  => [
                    'id',
                    'company_id',
                    'bookmark_id',
                    'classcode_id',
                    'created_at',
                    'updated_at',
                ]
            ]);
    }

    /** @test */
    function delete_bookmark_classcode()
    {
        $this->json('delete', '/api/bookmark_classcodes/1', [], $this->headers)
            ->assertStatus(204);

        $this->assertCount(0, BookmarkClasscode::all());
    }
}
