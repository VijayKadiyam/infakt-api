<?php

namespace Tests\Feature;

use App\ContentGrade;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ContentGradeTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp()
    {
        parent::setUp();

        factory(ContentGrade::class)->create([
            'content_id' => 1,
            'grade_id' => 1,
        ]);

        $this->payload = [
            'content_id' => 1,
            'grade_id' => 1,
        ];
    }

    /** @test */
    function it_requires_following_details()
    {
        $this->json('post', '/api/content_grades', [], $this->headers)
            ->assertStatus(422)
            ->assertExactJson([
                "errors"  =>  [
                    "content_id"        =>  ["The content id field is required."],
                ],
                "message" =>  "The given data was invalid."
            ]);
    }

    /** @test */
    function add_new_content_grade()
    {
        $this->disableEH();

        $this->json('post', '/api/content_grades', $this->payload, $this->headers)
            ->assertStatus(201)
            ->assertJson([
                'data'   => [
                    'content_id' => 1,
                    'grade_id' => 1
                ]
            ])
            ->assertJsonStructureExact([
                'data'   => [
                    'content_id',
                    'grade_id',
                    'updated_at',
                    'created_at',
                    'id'
                ]
            ]);
    }

    /** @test */
    function list_of_content_grades()
    {
        $this->disableEH();
        $this->json('GET', '/api/content_grades', [], $this->headers)
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    0 =>  [
                        'content_id',
                        'grade_id',
                    ]
                ]
            ]);
        $this->assertCount(1, ContentGrade::all());
    }

    /** @test */
    function show_single_content_grade()
    {

        $this->json('get', "/api/content_grades/1", [], $this->headers)
            ->assertStatus(200)
            ->assertJson([
                'data'  => [
                    'content_id' => 1,
                    'grade_id' => 1,
                ]
            ]);
    }

    /** @test */
    function update_single_content_grade()
    {
        $payload = [
            'content_id' => 1,
            'grade_id' => 1,
        ];

        $this->json('patch', '/api/content_grades/1', $payload, $this->headers)
            ->assertStatus(200)
            ->assertJson([
                'data'    => [
                    'content_id' => 1,
                    'grade_id' => 1,
                ]
            ])
            ->assertJsonStructureExact([
                'data'  => [
                    'id',
                    'content_id',
                    'grade_id',
                    'created_at',
                    'updated_at',
                ]
            ]);
    }

    /** @test */
    function delete_content_grade()
    {
        $this->json('delete', '/api/content_grades/1', [], $this->headers)
            ->assertStatus(204);

        $this->assertCount(0, ContentGrade::all());
    }
}
