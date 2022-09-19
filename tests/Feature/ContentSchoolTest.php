<?php

namespace Tests\Feature;

use App\ContentSchool;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ContentSchoolTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp()
    {
        parent::setUp();

        factory(ContentSchool::class)->create([
            'content_id' => 1,
            'company_id' => 1,
        ]);

        $this->payload = [
            'content_id' => 1,
            'company_id' => 1,
        ];
    }

    /** @test */
    function it_requires_following_details()
    {
        $this->json('post', '/api/content_schools', [], $this->headers)
            ->assertStatus(422)
            ->assertExactJson([
                "errors"  =>  [
                    "content_id"        =>  ["The content id field is required."],
                ],
                "message" =>  "The given data was invalid."
            ]);
    }

    /** @test */
    function add_new_content_school()
    {
        $this->disableEH();

        $this->json('post', '/api/content_schools', $this->payload, $this->headers)
            ->assertStatus(201)
            ->assertJson([
                'data'   => [
                    'content_id' => 1,
                    'company_id' => 1,
                ]
            ])
            ->assertJsonStructureExact([
                'data'   => [
                    'content_id',
                    'company_id',
                    'updated_at',
                    'created_at',
                    'id'
                ]
            ]);
    }

    /** @test */
    function list_of_content_schools()
    {
        $this->disableEH();
        $this->json('GET', '/api/content_schools', [], $this->headers)
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    0 =>  [
                        'content_id',
                        'company_id',
                    ]
                ]
            ]);
        $this->assertCount(1, ContentSchool::all());
    }

    /** @test */
    function show_single_content_school()
    {

        $this->json('get', "/api/content_schools/1", [], $this->headers)
            ->assertStatus(200)
            ->assertJson([
                'data'  => [
                    'content_id' => 1,
                    'company_id' => 1,
                ]
            ]);
    }

    /** @test */
    function update_single_content_school()
    {
        $payload = [
            'content_id' => 1,
            'company_id' => 1,
        ];

        $this->json('patch', '/api/content_schools/1', $payload, $this->headers)
            ->assertStatus(200)
            ->assertJson([
                'data'    => [
                    'content_id' => 1,
                    'company_id' => 1,
                ]
            ])
            ->assertJsonStructureExact([
                'data'  => [
                    'id',
                    'content_id',
                    'company_id',
                    'created_at',
                    'updated_at',
                ]
            ]);
    }

    /** @test */
    function delete_content_school()
    {
        $this->json('delete', '/api/content_schools/1', [], $this->headers)
            ->assertStatus(204);

        $this->assertCount(0, ContentSchool::all());
    }
}
