<?php

namespace Tests\Feature;

use App\ProfileFollowUp;
use App\Site;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProfileFollowUpTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp()
    {
        parent::setUp();

        $this->site = factory(Site::class)->create();

        $this->user->assignRole(1);
        $this->user->assignSite($this->site->id);
        $this->headers['siteid'] = $this->site->id;

        factory(ProfileFollowUp::class)->create([
            'site_id' =>  $this->site->id
        ]);

        $this->payload = [
            'user_id' => 1,
            'profile_id' => 1,
            'remarks' => 'remarks',
            'next_meeting_date' => 'next_meeting_date',
            'is_active' => true,
            'is_deleted' => false,
        ];
    }

    /** @test */
    function it_requires_following_details()
    {
        $this->json('post', '/api/profile_follow_ups', [], $this->headers)
            ->assertStatus(422)
            ->assertExactJson([
                "errors"  =>  [
                    "user_id"        =>  ["The user id field is required."],
                    "profile_id"        =>  ["The profile id field is required."],
                ],
                "message" =>  "The given data was invalid."
            ]);
    }

    /** @test */
    function add_new_profile()
    {
        $this->disableEH();
        $this->json('post', '/api/profile_follow_ups', $this->payload, $this->headers)
            ->assertStatus(201)
            ->assertJson([
                'data'   => [
                    'user_id' => 1,
                    'profile_id' => 1,
                    'remarks' => 'remarks',
                    'next_meeting_date' => 'next_meeting_date',
                    'is_active' => true,
                    'is_deleted' => false,
                ]
            ])
            ->assertJsonStructureExact([
                'data'   => [
                    'user_id',
                    'profile_id',
                    'remarks',
                    'next_meeting_date',
                    'is_active',
                    'is_deleted',
                    'site_id',
                    'updated_at',
                    'created_at',
                    'id'
                ]
            ]);
    }

    /** @test */
    function list_of_profiles()
    {
        $this->disableEH();
        $this->json('GET', '/api/profile_follow_ups', [], $this->headers)
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    0 =>  [
                        'user_id',
                        'profile_id',
                        'remarks',
                        'next_meeting_date',
                        'is_active',
                        'is_deleted',
                    ]
                ]
            ]);
        $this->assertCount(1, ProfileFollowUp::all());
    }

    /** @test */
    function show_single_profile()
    {

        $this->json('get', "/api/profile_follow_ups/1", [], $this->headers)
            ->assertStatus(200)
            ->assertJson([
                'data'  => [
                    'user_id' => 1,
                    'profile_id' => 1,
                    'remarks' => 'remarks',
                    'next_meeting_date' => 'next_meeting_date',
                    'is_active' => true,
                    'is_deleted' => false,
                ]
            ]);
    }

    /** @test */
    function update_single_profile()
    {
        $payload = [
            'user_id' => 1,
            'profile_id' => 1,
            'remarks' => 'remarks',
            'next_meeting_date' => 'next_meeting_date',
            'is_active' => true,
            'is_deleted' => false,
        ];

        $this->json('patch', '/api/profile_follow_ups/1', $payload, $this->headers)
            ->assertStatus(200)
            ->assertJson([
                'data'    => [
                    'user_id' => 1,
                    'profile_id' => 1,
                    'remarks' => 'remarks',
                    'next_meeting_date' => 'next_meeting_date',
                    'is_active' => true,
                    'is_deleted' => false,
                ]
            ])
            ->assertJsonStructureExact([
                'data'  => [
                    'id',
                    'site_id',
                    'user_id',
                    'profile_id',
                    'remarks',
                    'next_meeting_date',
                    'is_active',
                    'is_deleted',
                    'created_at',
                    'updated_at',
                ]
            ]);
    }

    /** @test */
    function delete_profile()
    {
        $this->json('delete', '/api/profile_follow_ups/1', [], $this->headers)
            ->assertStatus(204);

        $this->assertCount(0, ProfileFollowUp::all());
    }
}
