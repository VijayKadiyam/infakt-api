<?php

namespace Tests\Feature;

use App\ContactRequest;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ContactRequestTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp()
    {
        parent::setUp();

        factory(\App\ContactRequest::class)->create([
            'name'          => 'name',
            'email'         => 'email',
            'phone_no'      => 'phone_no',
            'interested_in' => 'interested_in',
            'description'   => 'description',
            'status'        => 'status',
            'remarks'       => 'remarks',
            'is_deleted'    => false,
        ]);

        $this->payload = [
            'name'          => 'name',
            'email'         => 'email',
            'phone_no'      => 'phone_no',
            'interested_in' => 'interested_in',
            'description'   => 'description',
            'status'        => 'status',
            'remarks'       => 'remarks',
            'is_deleted'    => false,
        ];
    }

    /** @test */
    function it_requires_contact_request_name()
    {
        $this->json('post', '/api/contact_requests', [], $this->headers)
            ->assertStatus(422)
            ->assertExactJson([
                "errors"  =>  [
                    "name"  =>  ["The name field is required."]
                ],
                "message" =>  "The given data was invalid."
            ]);
    }

    /** @test */
    function add_new_contact_request()
    {
        $this->disableEH();
        $this->json('post', '/api/contact_requests', $this->payload, $this->headers)
            ->assertStatus(201)
            ->assertJson([
                'data'  =>  [
                    'name'          => 'name',
                    'email'         => 'email',
                    'phone_no'      => 'phone_no',
                    'interested_in' => 'interested_in',
                    'description'   => 'description',
                    'status'        => 'status',
                    'remarks'       => 'remarks',
                    'is_deleted'    => false,
                ]
            ])
            ->assertJsonStructureExact([
                'data'  =>  [
                    'name',
                    'email',
                    'phone_no',
                    'interested_in',
                    'description',
                    'status',
                    'remarks',
                    'is_deleted',
                    'updated_at',
                    'created_at',
                    'id'
                ]
            ]);
    }

    /** @test */
    function list_of_contact_requests()
    {
        $this->disableEH();
        $this->json('GET', '/api/contact_requests', [], $this->headers)
            ->assertStatus(200)
            ->assertJsonStructure([
                'data'  =>  [
                    0 =>  [
                        'name',
                        'email',
                        'phone_no',
                        'interested_in',
                        'description',
                        'status',
                        'remarks',
                        'is_deleted',
                    ]
                ]
            ]);
        $this->assertCount(1, ContactRequest::all());
    }

    /** @test */
    function show_single_contact_request()
    {
        $this->disableEH();
        $this->json('get', "/api/contact_requests/1", [],  $this->headers)
            ->assertStatus(200)
            ->assertJson([
                'data'  => [
                    'name'          => 'name',
                    'email'         => 'email',
                    'phone_no'      => 'phone_no',
                    'interested_in' => 'interested_in',
                    'description'   => 'description',
                    'status'        => 'status',
                    'remarks'       => 'remarks',
                    'is_deleted'    => false,
                ]
            ])
            ->assertJsonStructureExact([
                'data'    => [
                    'id',
                    'name',
                    'email',
                    'phone_no',
                    'interested_in',
                    'description',
                    'status',
                    'remarks',
                    'is_deleted',
                    'created_at',
                    'updated_at',
                    'school_name',
                    'role',
                    'city',
                    'state',
                    'pincode',
                ]
            ]);
    }

    /** @test */
    function update_single_contact_request()
    {
        $this->disableEH();
        $payload = [
            'name'          => 'name',
            'email'         => 'email',
            'phone_no'      => 'phone_no',
            'interested_in' => 'interested_in',
            'description'   => 'description',
            'status'        => 'status',
            'remarks'       => 'remarks',
            'is_deleted'    => false,
        ];

        $this->json('patch', '/api/contact_requests/1', $payload, $this->headers)
            ->assertStatus(200)
            ->assertJson([
                'data'    => [
                    'name'          => 'name',
                    'email'         => 'email',
                    'phone_no'      => 'phone_no',
                    'interested_in' => 'interested_in',
                    'description'   => 'description',
                    'status'        => 'status',
                    'remarks'       => 'remarks',
                    'is_deleted'    => false,
                ]
            ])
            ->assertJsonStructureExact([
                'data'    => [
                    'id',
                    'name',
                    'email',
                    'phone_no',
                    'interested_in',
                    'description',
                    'status',
                    'remarks',
                    'is_deleted',
                    'created_at',
                    'updated_at',
                    'school_name',
                    'role',
                    'city',
                    'state',
                    'pincode',
                ]
            ]);
    }

    /** @test */
    function delete_contact_request()
    {
        $this->json('delete', '/api/contact_requests/1', [], $this->headers)
            ->assertStatus(204);

        $this->assertCount(0, ContactRequest::all());
    }
}
