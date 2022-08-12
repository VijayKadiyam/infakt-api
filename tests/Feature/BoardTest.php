<?php

namespace Tests\Feature;

use App\Board;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BoardTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp()
    {
        parent::setUp();
        factory(\App\Board::class)->create([
            'name' => "name",
            'is_active' => 1,
        ]);
        $this->payload = [
            'name' => "name",
            'is_active' => 1,
        ];
    }

    /** @test */
    function it_requires_board_name()
    {
        $this->json('post', '/api/boards', [])
            ->assertStatus(422)
            ->assertExactJson([
                "errors"  =>  [
                    "name"  =>  ["The name field is required."]
                ],
                "message" =>  "The given data was invalid."
            ]);
    }

    /** @test */
    function add_new_board()
    {
        $this->disableEH();
        $this->json('post', '/api/boards', $this->payload)
            ->assertStatus(201)
            ->assertJson([
                'data'  =>  [
                    'name' => "name",
                    'is_active' => 1,
                ]
            ])
            ->assertJsonStructureExact([
                'data'  =>  [
                    'name',
                    'is_active',
                    'updated_at',
                    'created_at',
                    'id'
                ]
            ]);
    }

    /** @test */
    function list_of_boards()
    {
        $this->disableEH();
        $this->json('GET', '/api/boards', [])
            ->assertStatus(200)
            ->assertJsonStructure([
                'data'  =>  [
                    0 =>  [
                        'name',
                        'is_active'
                    ]
                ]
            ]);
        $this->assertCount(1, Board::all());
    }

    /** @test */
    function show_single_board()
    {
        $this->disableEH();
        $this->json('get', "/api/boards/1", [])
            ->assertStatus(200)
            ->assertJson([
                'data'  => [
                    'name' => "name",
                    'is_active' => 1,
                ]
            ])
            ->assertJsonStructureExact([
                'data'    => [
                    'id',
                    'name',
                    'is_active',
                    'created_at',
                    'updated_at'
                ]
            ]);
    }

    /** @test */
    function update_single_board()
    {
        $this->disableEH();
        $payload = [
            'name' => "name",
            'is_active' => 1,
        ];

        $this->json('patch', '/api/boards/1', $payload)
            ->assertStatus(200)
            ->assertJson([
                'data'    => [
                    'name' => "name",
                    'is_active' => 1,
                ]
            ])
            ->assertJsonStructureExact([
                'data'    => [
                    'id',
                    'name',
                    'is_active',
                    'created_at',
                    'updated_at'
                ]
            ]);
    }
}
