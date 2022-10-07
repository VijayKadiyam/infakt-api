<?php

namespace Tests\Feature;

use App\UserSubject;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserSubjectTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp()
    {
        parent::setUp();

        $this->company = factory(\App\Company::class)->create([
            'name' => 'test'
        ]);

        $this->headers['company-id'] = $this->company->id;

        factory(UserSubject::class)->create([
            'company_id' =>  $this->company->id,
            'user_id'=>   1,
            'subject_id' => 1,
        ]);

        $this->payload = [
            'user_id' => 1,
            'subject_id' => 1,
        ];
    }

    /** @test */
    function it_requires_following_details()
    {
        $this->json('post', '/api/user_subjects', [], $this->headers)
            ->assertStatus(422)
            ->assertExactJson([
                "errors"  =>  [
                    "user_id"        =>  ["The user id field is required."],
                ],
                "message" =>  "The given data was invalid."
            ]);
    }

    /** @test */
    function add_user_subjects()
    {
        $this->disableEH();

        $this->json('post', '/api/user_subjects', $this->payload, $this->headers)
            ->assertStatus(201)
            ->assertJson([
                'data'   => [
                    'user_id' => 1,
                    'subject_id' => 1,
                ]
            ])
            ->assertJsonStructureExact([
                'data'   => [
                    'user_id',
                    'subject_id',
                    'company_id',
                    'updated_at',
                    'created_at',
                    'id'
                ]
            ]);
    }

    /** @test */
    function list_of_user_subjects()
    {
        $this->disableEH();
        $this->json('GET', '/api/user_subjects', [], $this->headers)
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    0 =>  [
                        'user_id',
                        'subject_id',
                    ]
                ]
            ]);
        $this->assertCount(1, UserSubject::all());
    }

    /** @test */
    function show_single_user_subjects()
    {

        $this->json('get', "/api/user_subjects/1", [], $this->headers)
            ->assertStatus(200)
            ->assertJson([
                'data'  => [
                    'user_id' => 1,
                    'subject_id' => 1,
                ]
            ]);
    }

    /** @test */
    function update_single_user_subjects()
    {
        $payload = [
            'user_id' => 1,
            'subject_id' => 1,
        ];

        $this->json('patch', '/api/user_subjects/1', $payload, $this->headers)
            ->assertStatus(200)
            ->assertJson([
                'data'    => [
                    'user_id' => 1,
                    'subject_id' => 1,
                ]
            ])
            ->assertJsonStructureExact([
                'data'  => [
                    'id',
                    'company_id',
                    'user_id',
                    'subject_id',
                    'created_at',
                    'updated_at',
                ]
            ]);
    }

    /** @test */
    function delete_user_subjects()
    {
        $this->json('delete', '/api/user_subjects/1', [], $this->headers)
            ->assertStatus(204);

        $this->assertCount(0, UserSubject::all());
    }
}
