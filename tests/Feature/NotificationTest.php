<?php

namespace Tests\Feature;

use App\Notification;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class NotificationTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp()
    {
        parent::setUp();

        $this->company = factory(\App\Company::class)->create([
            'name' => 'test'
        ]);

        $this->headers['company-id'] = $this->company->id;

        factory(Notification::class)->create([
            'company_id' =>  $this->company->id,
            'user_id' => 1,
            'description' => 'description',
        ]);

        $this->payload = [
            'user_id' => 1,
            'description' => 'description',
        ];
    }

    /** @test */
    function it_requires_following_details()
    {
        $this->json('post', '/api/notifications', [], $this->headers)
            ->assertStatus(422)
            ->assertExactJson([
                "errors"  =>  [
                    "user_id"        =>  ["The user id field is required."],
                ],
                "message" =>  "The given data was invalid."
            ]);
    }

    /** @test */
    function add_new_user_section()
    {
        $this->disableEH();

        $this->json('post', '/api/notifications', $this->payload, $this->headers)
            ->assertStatus(201)
            ->assertJson([
                'data'   => [
                    'user_id' => 1,
                    'description' => 'description',
                ]
            ])
            ->assertJsonStructureExact([
                'data'   => [
                    'user_id',
                    'description',
                    'company_id',
                    'updated_at',
                    'created_at',
                    'id'
                ]
            ]);
    }

    /** @test */
    function list_of_notifications()
    {
        $this->disableEH();
        $this->json('GET', '/api/notifications', [], $this->headers)
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    0 =>  [
                        'user_id',
                        'description',
                    ]
                ]
            ]);
        $this->assertCount(1, Notification::all());
    }

    /** @test */
    function show_single_user_section()
    {

        $this->json('get', "/api/notifications/1", [], $this->headers)
            ->assertStatus(200)
            ->assertJson([
                'data'  => [
                    'user_id' => 1,
                    'description' => 'description',
                ]
            ]);
    }

    /** @test */
    function update_single_user_section()
    {
        $payload = [
            'user_id' => 1,
            'description' => 'description',
        ];

        $this->json('patch', '/api/notifications/1', $payload, $this->headers)
            ->assertStatus(200)
            ->assertJson([
                'data'    => [
                    'user_id' => 1,
                    'description' => 'description',
                ]
            ])
            ->assertJsonStructureExact([
                'data'  => [
                    'id',
                    'company_id',
                    'user_id',
                    'description',
                    'created_at',
                    'updated_at',
                    "is_read",
                    "is_deleted",
                ]
            ]);
    }

    /** @test */
    function delete_user_section()
    {
        $this->json('delete', '/api/notifications/1', [], $this->headers)
            ->assertStatus(204);

        $this->assertCount(0, Notification::all());
    }
}
