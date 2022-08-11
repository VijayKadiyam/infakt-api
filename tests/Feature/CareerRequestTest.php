<?php

namespace Tests\Feature;

use App\CareerRequest;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CareerRequestTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp()
    {
        parent::setUp();

        factory(\App\CareerRequest::class)->create([
            'name'          => 'name',
            'email'         => 'email',
            'description'   => 'description',
            'status'        => 'status',
            'remarks'       => 'remarks',
        ]);

        $this->payload = [
            'name'          => 'name',
            'email'         => 'email',
            'description'   => 'description',
            'status'        => 'status',
            'remarks'       => 'remarks',
        ];
    }

    /** @test */
    function it_requires_career_request_name()
    {
        $this->json('post', '/api/career_requests', [], $this->headers)
            ->assertStatus(422)
            ->assertExactJson([
                "errors"  =>  [
                    "name"          =>  ["The name field is required."],
                    "email"         =>  ["The email field is required."],
                    "description"   =>  ["The description field is required."]
                ],
                "message" =>  "The given data was invalid."
            ]);
    }

    /** @test */
    function add_new_career_request()
    {
        $this->disableEH();
        $this->json('post', '/api/career_requests', $this->payload, $this->headers)
            ->assertStatus(201)
            ->assertJson([
                'data'  =>  [
                    'name'          => 'name',
                    'email'         => 'email',
                    'description'   => 'description',
                    'status'        => 'status',
                    'remarks'       => 'remarks',
                ]
            ])
            ->assertJsonStructureExact([
                'data'  =>  [
                    'name',
                    'email',
                    'description',
                    'status',
                    'remarks',
                    // 'is_deleted',
                    'updated_at',
                    'created_at',
                    'id'
                ]
            ]);
    }

    /** @test */
    function list_of_career_requests()
    {
        $this->disableEH();
        $this->json('GET', '/api/career_requests', [], $this->headers)
            ->assertStatus(200)
            ->assertJsonStructure([
                'data'  =>  [
                    0 =>  [
                        'name',
                        'email',
                        'description',
                        'status',
                        'remarks',
                        // 'is_deleted',
                    ]
                ]
            ]);
        $this->assertCount(1, CareerRequest::all());
    }

    /** @test */
    function show_single_career_request()
    {
        $this->disableEH();
        $this->json('get', "/api/career_requests/1", [],  $this->headers)
            ->assertStatus(200)
            ->assertJson([
                'data'  => [
                    'name'          => 'name',
                    'email'         => 'email',
                    'description'   => 'description',
                    'status'        => 'status',
                    'remarks'       => 'remarks',
                ]
            ])
            ->assertJsonStructureExact([
                'data'    => [
                    'id',
                    'name',
                    'email',
                    'description',
                    'status',
                    'remarks',
                    'is_deleted',
                    'created_at',
                    'updated_at'
                ]
            ]);
    }

    /** @test */
    function update_single_career_request()
    {
        $this->disableEH();
        $payload = [
            'name'          => 'name',
            'email'         => 'email',
            'description'   => 'description',
            'status'        => 'status',
            'remarks'       => 'remarks',
        ];

        $this->json('patch', '/api/career_requests/1', $payload, $this->headers)
            ->assertStatus(200)
            ->assertJson([
                'data'    => [
                    'name'          => 'name',
                    'email'         => 'email',
                    'description'   => 'description',
                    'status'        => 'status',
                    'remarks'       => 'remarks',
                ]
            ])
            ->assertJsonStructureExact([
                'data'    => [
                    'id',
                    'name',
                    'email',
                    'description',
                    'status',
                    'remarks',
                    'is_deleted',
                    'created_at',
                    'updated_at'
                ]
            ]);
    }

    /** @test */
    function delete_career_request()
    {
        $this->json('delete', '/api/career_requests/1', [], $this->headers)
            ->assertStatus(204);

        $this->assertCount(0, CareerRequest::all());
    }
}
