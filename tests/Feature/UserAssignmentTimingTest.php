<?php

namespace Tests\Feature;

use App\UserAssignmentTiming;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserAssignmentTimingTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp()
    {
        parent::setUp();

        $this->company = factory(\App\Company::class)->create([
            'name' => 'test'
        ]);

        $this->headers['company-id'] = $this->company->id;

        factory(UserAssignmentTiming::class)->create([
            'company_id' =>  $this->company->id,
            'user_id' => 1,
            'assignment_id' => 1,
            'user_assignment_id' => 1,
            'timestamp' => 'timestamp',
        ]);

        $this->payload = [
            'user_id' => 1,
            'assignment_id' => 1,
            'user_assignment_id' => 1,
            'timestamp' => 'timestamp',
        ];
    }

    /** @test */
    function it_requires_following_details()
    {
        $this->json('post', '/api/user_assignment_timings', [], $this->headers)
            ->assertStatus(422)
            ->assertExactJson([
                "errors"  =>  [
                    "user_id"        =>  ["The user id field is required."],
                ],
                "message" =>  "The given data was invalid."
            ]);
    }

    /** @test */
    function add_new_user_assignment_timing()
    {
        $this->disableEH();

        $this->json('post', '/api/user_assignment_timings', $this->payload, $this->headers)
            ->assertStatus(201)
            ->assertJson([
                'data'   => [
                    'user_id' => 1,
                    'assignment_id' => 1,
                    'user_assignment_id' => 1,
                    'timestamp' => 'timestamp',
                ]
            ])
            ->assertJsonStructureExact([
                'data'   => [
                    'user_id',
                    'assignment_id',
                    'user_assignment_id',
                    'timestamp',
                    'company_id',
                    'updated_at',
                    'created_at',
                    'id'
                ]
            ]);
    }

    /** @test */
    function list_of_user_assignment_timings()
    {
        $this->disableEH();
        $this->json('GET', '/api/user_assignment_timings', [], $this->headers)
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    0 =>  [
                        'user_id',
                        'assignment_id',
                        'user_assignment_id',
                        'timestamp',
                    ]
                ]
            ]);
        $this->assertCount(2, UserAssignmentTiming::all());
    }

    /** @test */
    function show_single_user_assignment_timing()
    {

        $this->json('get', "/api/user_assignment_timings/1", [], $this->headers)
            ->assertStatus(200)
            ->assertJson([
                'data'  => [
                    'user_id' => 1,
                    'assignment_id' => 1,
                    'user_assignment_id' => 1,
                    'timestamp' => 'timestamp',
                ]
            ]);
    }

    /** @test */
    function update_single_user_assignment_timing()
    {
        $payload = [
            'user_id' => 1,
            'assignment_id' => 1,
            'user_assignment_id' => 1,
            'timestamp' => 'timestamp',
        ];

        $this->json('patch', '/api/user_assignment_timings/1', $payload, $this->headers)
            ->assertStatus(200)
            ->assertJson([
                'data'    => [
                    'user_id' => 1,
                    'assignment_id' => 1,
                    'user_assignment_id' => 1,
                    'timestamp' => 'timestamp',
                ]
            ])
            ->assertJsonStructureExact([
                'data'  => [
                    'id',
                    'company_id',
                    'user_id',
                    'assignment_id',
                    'user_assignment_id',
                    'timestamp',
                    'created_at',
                    'updated_at',
                ]
            ]);
    }

    /** @test */
    function delete_user_assignment_timing()
    {
        $this->json('delete', '/api/user_assignment_timings/1', [], $this->headers)
            ->assertStatus(204);

        $this->assertCount(1, UserAssignmentTiming::all());
    }
}
