<?php

namespace Tests\Feature;

use App\Company;
use App\UserStandard;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserStandardTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp()
    {
        parent::setUp();

        $this->company = factory(\App\Company::class)->create([
            'name' => 'test'
        ]);

        $this->headers['company-id'] = $this->company->id;

        factory(UserStandard::class)->create([
            'company_id' =>  $this->company->id,
            'user_id' => '1',
            'standard_id' => '1',
            'start_date' => 'start_date',
            'end_date' => 'end_date',
            'is_active' => '1',
            'is_deleted' => '0',
        ]);

        $this->payload = [
            'user_id' => '1',
            'standard_id' => '1',
            'start_date' => 'start_date',
            'end_date' => 'end_date',
            'is_active' => '1',
            'is_deleted' => '0',
        ];
    }

    /** @test */
    function it_requires_following_details()
    {
        $this->json('post', '/api/user_standards', [], $this->headers)
            ->assertStatus(422)
            ->assertExactJson([
                "errors"  =>  [
                    "user_id"        =>  ["The user id field is required."],
                ],
                "message" =>  "The given data was invalid."
            ]);
    }

    /** @test */
    function add_new_user_standard()
    {
        $this->disableEH();

        $this->json('post', '/api/user_standards', $this->payload, $this->headers)
            ->assertStatus(201)
            ->assertJson([
                'data'   => [
                    'user_id' => '1',
                    'standard_id' => '1',
                    'start_date' => 'start_date',
                    'end_date' => 'end_date',
                    'is_active' => '1',
                    'is_deleted' => '0',
                ]
            ])
            ->assertJsonStructureExact([
                'data'   => [
                    'user_id',
                    'standard_id',
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
    function list_of_user_standards()
    {
        $this->disableEH();
        $this->json('GET', '/api/user_standards', [], $this->headers)
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    0 =>  [
                        'user_id',
                        'standard_id',
                        'start_date',
                        'end_date',
                        'is_active',
                        'is_deleted'
                    ]
                ]
            ]);
        $this->assertCount(1, UserStandard::all());
    }

    /** @test */
    function show_single_user_standard()
    {

        $this->json('get', "/api/user_standards/1", [], $this->headers)
            ->assertStatus(200)
            ->assertJson([
                'data'  => [
                    'user_id' => '1',
                    'standard_id' => '1',
                    'start_date' => 'start_date',
                    'end_date' => 'end_date',
                    'is_active' => '1',
                    'is_deleted' => '0',
                ]
            ]);
    }

    /** @test */
    function update_single_user_standard()
    {
        $payload = [
            'user_id' => '1',
            'standard_id' => '1',
            'start_date' => 'start_date',
            'end_date' => 'end_date',
            'is_active' => '1',
            'is_deleted' => '0',
        ];

        $this->json('patch', '/api/user_standards/1', $payload, $this->headers)
            ->assertStatus(200)
            ->assertJson([
                'data'    => [
                    'user_id' => '1',
                    'standard_id' => '1',
                    'start_date' => 'start_date',
                    'end_date' => 'end_date',
                    'is_active' => '1',
                    'is_deleted' => '0',
                ]
            ])
            ->assertJsonStructureExact([
                'data'  => [
                    'id',
                    'company_id',
                    'user_id',
                    'standard_id',
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
    function delete_user_standard()
    {
        $this->json('delete', '/api/user_standards/1', [], $this->headers)
            ->assertStatus(204);

        $this->assertCount(0, UserStandard::all());
    }
}
