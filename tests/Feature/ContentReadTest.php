<?php

namespace Tests\Feature;

use App\ContentRead;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ContentReadTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp()
    {
        parent::setUp();

        $this->company = factory(\App\Company::class)->create([
            'name' => 'test'
        ]);

        $this->headers['company-id'] = $this->company->id;

        factory(ContentRead::class)->create([
            'company_id' =>  $this->company->id,
        ]);

        $this->payload = [
            'user_id' => 1,
            'content_id' => 1,
        ];
    }

    /** @test */
    function it_requires_following_details()
    {
        $this->json('post', '/api/content_reads', [], $this->headers)
            ->assertStatus(422)
            ->assertExactJson([
                "errors"  =>  [
                    "user_id"        =>  ["The user id field is required."],
                ],
                "message" =>  "The given data was invalid."
            ]);
    }

    /** @test */
    function add_new_content_read()
    {
        $this->disableEH();

        $this->json('post', '/api/content_reads', $this->payload, $this->headers)
            ->assertStatus(201)
            ->assertJson([
                'data'   => [
                    'user_id' => 1,
                    'content_id' => 1,
                ]
            ])
            ->assertJsonStructureExact([
                'data'   => [
                    'user_id',
                    'content_id',
                    'company_id',
                    'updated_at',
                    'created_at',
                    'id'
                ]
            ]);
    }

    /** @test */
    function list_of_content_reads()
    {
        $this->disableEH();
        $this->json('GET', '/api/content_reads', [], $this->headers)
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    0 =>  [
                        'user_id',
                        'content_id',
                    ]
                ]
            ]);
        $this->assertCount(1, ContentRead::all());
    }

    /** @test */
    function show_single_content_read()
    {

        $this->json('get', "/api/content_reads/1", [], $this->headers)
            ->assertStatus(200)
            ->assertJson([
                'data'  => [
                    'user_id' => 1,
                    'content_id' => 1,
                ]
            ])->assertJsonStructureExact([
                'data'    => [
                    'id',
                    'company_id',
                    'content_id',
                    'user_id',
                    'created_at',
                    'updated_at'
                ]
            ]);;
    }

    /** @test */
    function update_single_content_read()
    {
        $payload = [
            'user_id' => 1,
            'content_id' => 1,
        ];

        $this->json('patch', '/api/content_reads/1', $payload, $this->headers)
            ->assertStatus(200)
            ->assertJson([
                'data'    => [
                    'user_id' => 1,
                    'content_id' => 1,
                ]
            ])
            ->assertJsonStructureExact([
                'data'  => [
                    'id',
                    'company_id',
                    'content_id',
                    'user_id',
                    'created_at',
                    'updated_at'
                ]
            ]);
    }

    /** @test */
    function delete_content_read()
    {
        $this->json('delete', '/api/content_reads/1', [], $this->headers)
            ->assertStatus(204);

        $this->assertCount(0, ContentRead::all());
    }
}
