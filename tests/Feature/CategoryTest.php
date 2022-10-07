<?php

namespace Tests\Feature;

use App\Category;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CategoryTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp()
    {
        parent::setUp();

        factory(Category::class)->create([
            'name' => 'name',
            'is_active' => 1,
            'is_deleted' => 0,
        ]);

        $this->payload = [
            'name' => 'name',
            'is_active' => 1,
            'is_deleted' => 0,
        ];
    }

    /** @test */
    function it_requires_following_details()
    {
        $this->json('post', '/api/categories', [], $this->headers)
            ->assertStatus(422)
            ->assertExactJson([
                "errors"  =>  [
                    "name"        =>  ["The name field is required."],
                ],
                "message" =>  "The given data was invalid."
            ]);
    }

    /** @test */
    function add_new_category()
    {
        $this->disableEH();

        $this->json('post', '/api/categories', $this->payload, $this->headers)
            ->assertStatus(201)
            ->assertJson([
                'data'   => [
                    'name' => 'name',
                    'is_active' => 1,
                    'is_deleted' => 0,
                ]
            ])
            ->assertJsonStructureExact([
                'data'   => [
                    'name',
                    'is_active',
                    'is_deleted',
                    'updated_at',
                    'created_at',
                    'id'
                ]
            ]);
    }

    /** @test */
    function list_of_categories()
    {
        $this->disableEH();
        $this->json('GET', '/api/categories', [], $this->headers)
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    0 =>  [
                        'name',
                        'is_active',
                        'is_deleted',
                    ]
                ]
            ]);
        $this->assertCount(1, Category::all());
    }

    /** @test */
    function show_single_category()
    {

        $this->json('get', "/api/categories/1", [], $this->headers)
            ->assertStatus(200)
            ->assertJson([
                'data'  => [
                    'name' => 'name',
                    'is_active' => 1,
                    'is_deleted' => 0,
                ]
            ]);
    }

    /** @test */
    function update_single_category()
    {
        $payload = [
            'name' => 'name',
            'is_active' => 1,
            'is_deleted' => 0,
        ];

        $this->json('patch', '/api/categories/1', $payload, $this->headers)
            ->assertStatus(200)
            ->assertJson([
                'data'    => [
                    'name' => 'name',
                    'is_active' => 1,
                    'is_deleted' => 0,
                ]
            ])
            ->assertJsonStructureExact([
                'data'  => [
                    'id',
                    'name',
                    'is_active',
                    'is_deleted',
                    'created_at',
                    'updated_at',
                ]
            ]);
    }

    /** @test */
    function delete_category()
    {
        $this->json('delete', '/api/categories/1', [], $this->headers)
            ->assertStatus(204);

        $this->assertCount(0, Category::all());
    }
}