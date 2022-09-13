<?php

namespace Tests\job;

use App\Job;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class JobTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp()
    {
        parent::setUp();

        factory(Job::class)->create([
            'title' => 'title',
            'description' => 'description',
        ]);

        $this->payload = [
            'title' => 'title',
            'description' => 'description',
        ];
    }

    /** @test */
    function it_requires_following_details()
    {
        $this->json('post', '/api/jobs', [], $this->headers)
            ->assertStatus(422)
            ->assertExactJson([
                "errors"  =>  [
                    "title"        =>  ["The title field is required."],
                ],
                "message" =>  "The given data was invalid."
            ]);
    }

    /** @test */
    function add_new_job()
    {
        $this->disableEH();

        $this->json('post', '/api/jobs', $this->payload, $this->headers)
            ->assertStatus(201)
            ->assertJson([
                'data'   => [
                    'title' => 'title',
                    'description' => 'description',
                ]
            ])
            ->assertJsonStructureExact([
                'data'   => [
                    'title',
                    'description',
                    'updated_at',
                    'created_at',
                    'id'
                ]
            ]);
    }

    /** @test */
    function list_of_jobs()
    {
        $this->disableEH();
        $this->json('GET', '/api/jobs', [], $this->headers)
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    0 =>  [
                        'title',
                        'description',
                    ]
                ]
            ]);
        $this->assertCount(1, Job::all());
    }

    /** @test */
    function show_single_job()
    {

        $this->json('get', "/api/jobs/1", [], $this->headers)
            ->assertStatus(200)
            ->assertJson([
                'data'  => [
                    'title' => 'title',
                    'description' => 'description',
                ]
            ]);
    }

    /** @test */
    function update_single_job()
    {
        $payload = [
            'title' => 'title',
            'description' => 'description',
        ];

        $this->json('patch', '/api/jobs/1', $payload, $this->headers)
            ->assertStatus(200)
            ->assertJson([
                'data'    => [
                    'title' => 'title',
                    'description' => 'description',
                ]
            ])
            ->assertJsonStructureExact([
                'data'  => [
                    'id',
                    'title',
                    'description',
                    'created_at',
                    'updated_at',
                ]
            ]);
    }

    /** @test */
    function delete_job()
    {
        $this->json('delete', '/api/jobs/1', [], $this->headers)
            ->assertStatus(204);

        $this->assertCount(0, Job::all());
    }
}
