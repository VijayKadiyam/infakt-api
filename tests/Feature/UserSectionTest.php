<?php

namespace Tests\Feature;

use App\UserSection;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserSectionTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp()
    {
        parent::setUp();

        $this->company = factory(\App\Company::class)->create([
            'name' => 'test'
        ]);

        $this->headers['company-id'] = $this->company->id;

        factory(UserSection::class)->create([
            'company_id' =>  $this->company->id,
            'user_id' => 1,
            'section_id' => 1,
            'start_date' => 'start_date',
            'end_date' => 'end_date',
            'is_active' => 1,
            'is_deleted' => 0,
        ]);

        $this->payload = [
            'user_id' => 1,
            'section_id' => 1,
            'start_date' => 'start_date',
            'end_date' => 'end_date',
            'is_active' => 1,
            'is_deleted' => 0,
        ];
    }

    /** @test */
    function it_requires_following_details()
    {
        $this->json('post', '/api/user_sections', [], $this->headers)
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

        $this->json('post', '/api/user_sections', $this->payload, $this->headers)
            ->assertStatus(201)
            ->assertJson([
                'data'   => [
                    'user_id' => 1,
                    'section_id' => 1,
                    'start_date' => 'start_date',
                    'end_date' => 'end_date',
                    'is_active' => 1,
                    'is_deleted' => 0,
                ]
            ])
            ->assertJsonStructureExact([
                'data'   => [
                    'user_id',
                    'section_id',
                    'start_date',
                    'end_date',
                    'is_active',
                    'is_deleted',
                    'company_id',
                    'updated_at',
                    'created_at',
                    'id'
                ]
            ]);
    }

    /** @test */
    function list_of_user_sections()
    {
        $this->disableEH();
        $this->json('GET', '/api/user_sections', [], $this->headers)
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    0 =>  [
                        'user_id',
                        'section_id',
                        'start_date',
                        'end_date',
                        'is_active',
                        'is_deleted'
                    ]
                ]
            ]);
        $this->assertCount(1, UserSection::all());
    }

    /** @test */
    function show_single_user_section()
    {

        $this->json('get', "/api/user_sections/1", [], $this->headers)
            ->assertStatus(200)
            ->assertJson([
                'data'  => [
                    'user_id' => 1,
                    'section_id' => 1,
                    'start_date' => 'start_date',
                    'end_date' => 'end_date',
                    'is_active' => 1,
                    'is_deleted' => 0,
                ]
            ]);
    }

    /** @test */
    function update_single_user_section()
    {
        $payload = [
            'user_id' => 1,
            'section_id' => 1,
            'start_date' => 'start_date',
            'end_date' => 'end_date',
            'is_active' => 1,
            'is_deleted' => 0,
        ];

        $this->json('patch', '/api/user_sections/1', $payload, $this->headers)
            ->assertStatus(200)
            ->assertJson([
                'data'    => [
                    'user_id' => 1,
                    'section_id' => 1,
                    'start_date' => 'start_date',
                    'end_date' => 'end_date',
                    'is_active' => 1,
                    'is_deleted' => 0,
                ]
            ])
            ->assertJsonStructureExact([
                'data'  => [
                    'id',
                    'company_id',
                    'user_id',
                    'section_id',
                    'start_date',
                    'end_date',
                    'is_active',
                    'is_deleted',
                    'created_at',
                    'updated_at',
                ]
            ]);
    }

    /** @test */
    function delete_user_section()
    {
        $this->json('delete', '/api/user_sections/1', [], $this->headers)
            ->assertStatus(204);

        $this->assertCount(0, UserSection::all());
    }
}
