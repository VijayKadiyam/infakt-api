<?php

namespace Tests\Feature;

use App\CompanyBoard;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CompanyBoardTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp()
    {
        parent::setUp();

        $this->company = factory(\App\Company::class)->create([
            'name' => 'test'
        ]);

        $this->headers['company-id'] = $this->company->id;

        factory(CompanyBoard::class)->create([
            'company_id' =>  $this->company->id,
            'board_id' => 1,
        ]);

        $this->payload = [
            'board_id' => 1,
        ];
    }

    /** @test */
    function it_requires_following_details()
    {
        $this->json('post', '/api/company_boards', [], $this->headers)
            ->assertStatus(422)
            ->assertExactJson([
                "errors"  =>  [
                    "board_id"        =>  ["The board id field is required."],
                ],
                "message" =>  "The given data was invalid."
            ]);
    }

    /** @test */
    function add_new_company_board()
    {
        $this->disableEH();

        $this->json('post', '/api/company_boards', $this->payload, $this->headers)
            ->assertStatus(201)
            ->assertJson([
                'data'   => [
                    'board_id' => 1,
                ]
            ])
            ->assertJsonStructureExact([
                'data'   => [
                    'board_id',
                    'company_id',
                    'updated_at',
                    'created_at',
                    'id'
                ]
            ]);
    }

    /** @test */
    function list_of_company_boards()
    {
        $this->disableEH();
        $this->json('GET', '/api/company_boards', [], $this->headers)
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    0 =>  [
                        'board_id',
                    ]
                ]
            ]);
        $this->assertCount(1, CompanyBoard::all());
    }

    /** @test */
    function show_single_company_board()
    {

        $this->json('get', "/api/company_boards/1", [], $this->headers)
            ->assertStatus(200)
            ->assertJson([
                'data'  => [
                    'board_id' => 1,
                ]
            ]);
    }

    /** @test */
    function update_single_company_board()
    {
        $payload = [
            'board_id' => 1,
        ];

        $this->json('patch', '/api/company_boards/1', $payload, $this->headers)
            ->assertStatus(200)
            ->assertJson([
                'data'    => [
                    'board_id' => 1,
                ]
            ])
            ->assertJsonStructureExact([
                'data'  => [
                    'id',
                    'company_id',
                    'board_id',
                    'created_at',
                    'updated_at',
                ]
            ]);
    }

    /** @test */
    function delete_company_board()
    {
        $this->json('delete', '/api/company_boards/1', [], $this->headers)
            ->assertStatus(204);

        $this->assertCount(0, CompanyBoard::all());
    }
}
