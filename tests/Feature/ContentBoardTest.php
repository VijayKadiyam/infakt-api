<?php

namespace Tests\Feature;

use App\ContentBoard;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ContentBoardTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp()
    {
        parent::setUp();

        factory(ContentBoard::class)->create([
            'content_id' => 1,
            'board_id' => 1,
        ]);

        $this->payload = [
            'content_id' => 1,
            'board_id' => 1,
        ];
    }

    /** @test */
    function it_requires_following_details()
    {
        $this->json('post', '/api/content_boards', [], $this->headers)
            ->assertStatus(422)
            ->assertExactJson([
                "errors"  =>  [
                    "content_id"        =>  ["The content id field is required."],
                ],
                "message" =>  "The given data was invalid."
            ]);
    }

    /** @test */
    function add_new_content_board()
    {
        $this->disableEH();

        $this->json('post', '/api/content_boards', $this->payload, $this->headers)
            ->assertStatus(201)
            ->assertJson([
                'data'   => [
                    'content_id' => 1,
                    'board_id' => 1,
                ]
            ])
            ->assertJsonStructureExact([
                'data'   => [
                    'content_id',
                    'board_id',
                    'updated_at',
                    'created_at',
                    'id'
                ]
            ]);
    }

    /** @test */
    function list_of_content_boards()
    {
        $this->disableEH();
        $this->json('GET', '/api/content_boards', [], $this->headers)
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    0 =>  [
                        'content_id',
                        'board_id',
                    ]
                ]
            ]);
        $this->assertCount(1, ContentBoard::all());
    }

    /** @test */
    function show_single_content_board()
    {

        $this->json('get', "/api/content_boards/1", [], $this->headers)
            ->assertStatus(200)
            ->assertJson([
                'data'  => [
                    'content_id' => 1,
                    'board_id' => 1,
                ]
            ]);
    }

    /** @test */
    function update_single_content_board()
    {
        $payload = [
            'content_id' => 1,
            'board_id' => 1,
        ];

        $this->json('patch', '/api/content_boards/1', $payload, $this->headers)
            ->assertStatus(200)
            ->assertJson([
                'data'    => [
                    'content_id' => 1,
                    'board_id' => 1,
                ]
            ])
            ->assertJsonStructureExact([
                'data'  => [
                    'id',
                    'content_id',
                    'board_id',
                    'created_at',
                    'updated_at',
                ]
            ]);
    }

    /** @test */
    function delete_content_board()
    {
        $this->json('delete', '/api/content_boards/1', [], $this->headers)
            ->assertStatus(204);

        $this->assertCount(0, ContentBoard::all());
    }
}
