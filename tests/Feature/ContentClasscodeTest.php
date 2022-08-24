<?php

namespace Tests\Feature;

use App\ContentClasscode;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ContentClasscodeTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp()
    {
        parent::setUp();

        $this->company = factory(\App\Company::class)->create([
            'name' => 'test'
        ]);

        $this->headers['company-id'] = $this->company->id;

        factory(ContentClasscode::class)->create([
            'company_id' =>  $this->company->id,
            'content_id' => 1,
            'classcode_id' => 1,
            'created_by_id' => 1,
        ]);

        $this->payload = [
            'content_id' => 1,
            'classcode_id' => 1,
            'created_by_id' => 1,
        ];
    }

    /** @test */
    function it_requires_following_details()
    {
        $this->json('post', '/api/content_classcodes', [], $this->headers)
            ->assertStatus(422)
            ->assertExactJson([
                "errors"  =>  [
                    "content_id"        =>  ["The content id field is required."],
                ],
                "message" =>  "The given data was invalid."
            ]);
    }

    /** @test */
    function add_new_content_classcode()
    {
        $this->disableEH();

        $this->json('post', '/api/content_classcodes', $this->payload, $this->headers)
            ->assertStatus(201)
            ->assertJson([
                'data'   => [
                    'content_id' => 1,
                    'classcode_id' => 1,
                    'created_by_id' => 1,
                ]
            ])
            ->assertJsonStructureExact([
                'data'   => [
                    'content_id',
                    'classcode_id',
                    'created_by_id',
                    'company_id',
                    'updated_at',
                    'created_at',
                    'id'
                ]
            ]);
    }

    /** @test */
    function list_of_content_classcodes()
    {
        $this->disableEH();
        $this->json('GET', '/api/content_classcodes', [], $this->headers)
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    0 =>  [
                        'content_id',
                        'classcode_id',
                        'created_by_id',
                    ]
                ]
            ]);
        $this->assertCount(1, ContentClasscode::all());
    }

    /** @test */
    function show_single_content_classcode()
    {

        $this->json('get', "/api/content_classcodes/1", [], $this->headers)
            ->assertStatus(200)
            ->assertJson([
                'data'  => [
                    'content_id' => 1,
                    'classcode_id' => 1,
                    'created_by_id' => 1,
                ]
            ]);
    }

    /** @test */
    function update_single_content_classcode()
    {
        $payload = [
            'content_id' => 1,
            'classcode_id' => 1,
            'created_by_id' => 1,
        ];

        $this->json('patch', '/api/content_classcodes/1', $payload, $this->headers)
            ->assertStatus(200)
            ->assertJson([
                'data'    => [
                    'content_id' => 1,
                    'classcode_id' => 1,
                    'created_by_id' => 1,
                ]
            ])
            ->assertJsonStructureExact([
                'data'  => [
                    'id',
                    'company_id',
                    'content_id',
                    'classcode_id',
                    'created_by_id',
                    'created_at',
                    'updated_at',
                ]
            ]);
    }

    /** @test */
    function delete_content_classcode()
    {
        $this->json('delete', '/api/content_classcodes/1', [], $this->headers)
            ->assertStatus(204);

        $this->assertCount(0, ContentClasscode::all());
    }
}
