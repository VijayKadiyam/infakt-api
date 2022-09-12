<?php

namespace Tests\Feature;

use App\UserTimestamp;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTimestampTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp()
    {
        parent::setUp();

        $this->company = factory(\App\Company::class)->create([
            'name' => 'test'
        ]);

        $this->headers['company-id'] = $this->company->id;

        factory(UserTimestamp::class)->create([
            'company_id' =>  $this->company->id,
            'user_id'=>1,
        'timestamp'=>'timestamp',
        'event'=>'event',
        ]);

        $this->payload = [
            'user_id'=>1,
            'timestamp'=>'timestamp',
            'event'=>'event',
        ];
    }

    /** @test */
    function it_requires_following_details()
    {
        $this->json('post', '/api/user_timestamps', [], $this->headers)
            ->assertStatus(422)
            ->assertExactJson([
                "errors"  =>  [
                    "user_id"        =>  ["The user id field is required."],
                ],
                "message" =>  "The given data was invalid."
            ]);
    }

    /** @test */
    function add_new_user_timestamp()
    {
        $this->disableEH();

        $this->json('post', '/api/user_timestamps', $this->payload, $this->headers)
            ->assertStatus(201)
            ->assertJson([
                'data'   => [
                    'user_id'=>1,
                    'timestamp'=>'timestamp',
                    'event'=>'event',
                ]
            ])
            ->assertJsonStructureExact([
                'data'   => [
                    'user_id',
                    'timestamp',
                    'event',
                    'company_id',
                    'updated_at',
                    'created_at',
                    'id'
                ]
            ]);
    }

    /** @test */
    function list_of_user_timestamps()
    {
        $this->disableEH();
        $this->json('GET', '/api/user_timestamps', [], $this->headers)
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    0 =>  [
                        'user_id',
                        'timestamp',
                        'event',
                    ]
                ]
            ]);
        $this->assertCount(1, UserTimestamp::all());
    }

    /** @test */
    function show_single_user_timestamp()
    {

        $this->json('get', "/api/user_timestamps/1", [], $this->headers)
            ->assertStatus(200)
            ->assertJson([
                'data'  => [
                    'user_id'=>1,
                    'timestamp'=>'timestamp',
                    'event'=>'event',
                ]
            ]);
    }

    /** @test */
    function update_single_user_timestamp()
    {
        $payload = [
            'user_id'=>1,
            'timestamp'=>'timestamp',
            'event'=>'event',
        ];

        $this->json('patch', '/api/user_timestamps/1', $payload, $this->headers)
            ->assertStatus(200)
            ->assertJson([
                'data'    => [
                    'user_id'=>1,
                    'timestamp'=>'timestamp',
                    'event'=>'event',
                ]
            ])
            ->assertJsonStructureExact([
                'data'  => [
                    'id',
                    'company_id',
                    'user_id',
                    'timestamp',
                    'event',
                    'created_at',
                    'updated_at',
                ]
            ]);
    }

    /** @test */
    function delete_user_timestamp()
    {
        $this->json('delete', '/api/user_timestamps/1', [], $this->headers)
            ->assertStatus(204);

        $this->assertCount(0, UserTimestamp::all());
    }
}
