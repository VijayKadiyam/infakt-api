<?php

namespace Tests\Feature;

use App\Grade;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GradeTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp()
    {
        parent::setUp();

        factory(Grade::class)->create([
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
        $this->json('post', '/api/grades', [], $this->headers)
            ->assertStatus(422)
            ->assertExactJson([
                "errors"  =>  [
                    "name"        =>  ["The name field is required."],
                ],
                "message" =>  "The given data was invalid."
            ]);
    }

    /** @test */
    function add_new_grade()
    {
        $this->disableEH();

        $this->json('post', '/api/grades', $this->payload, $this->headers)
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
    function list_of_grades()
    {
        $this->disableEH();
        $this->json('GET', '/api/grades', [], $this->headers)
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
        $this->assertCount(1, Grade::all());
    }

    /** @test */
    function show_single_grade()
    {

        $this->json('get', "/api/grades/1", [], $this->headers)
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
    function update_single_grade()
    {
        $payload = [
            'name' => 'name',
            'is_active' => 1,
            'is_deleted' => 0,
        ];

        $this->json('patch', '/api/grades/1', $payload, $this->headers)
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
    function delete_grade()
    {
        $this->json('delete', '/api/grades/1', [], $this->headers)
            ->assertStatus(204);

        $this->assertCount(0, Grade::all());
    }
}
