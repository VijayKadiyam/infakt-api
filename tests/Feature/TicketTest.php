<?php

namespace Tests\Feature;

use App\Ticket;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TicketTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp()
    {
        parent::setUp();

        $this->company = factory(\App\Company::class)->create([
            'name' => 'test'
        ]);
        $this->user->assignCompany($this->company->id);
        $this->headers['company-id'] = $this->company->id;

        factory(\App\Ticket::class)->create([
            'company_id'  =>  $this->company->id
        ]);

        $this->payload = [
            'title' => 'title',
            'description' => 'description',
            'type' => 'type',
            'status' => 'status',
            'assigned_to_id' => 1,
            'imagepath1' => 'imagepath1',
            'imagepath2' => 'imagepath2',
            'imagepath3' => 'imagepath3',
            'imagepath4' => 'imagepath4',
            'created_by_id' => 1,
        ];
    }


    /** @test */
    function it_requires_following_details()
    {
        $this->json('post', '/api/tickets', [], $this->headers)
            ->assertStatus(422)
            ->assertExactJson([
                "errors"  =>  [
                    "title"    =>  ["The title field is required."]
                ],
                "message" =>  "The given data was invalid."
            ]);
    }

    /** @test */
    function add_new_ticket()
    {
        $this->disableEH();
        $this->json('post', '/api/tickets', $this->payload, $this->headers)
            ->assertStatus(201)
            ->assertJson([
                'data'   => [
                    'title' => 'title',
                    'description' => 'description',
                    'type' => 'type',
                    'status' => 'status',
                    'assigned_to_id' => 1,
                    'imagepath1' => 'imagepath1',
                    'imagepath2' => 'imagepath2',
                    'imagepath3' => 'imagepath3',
                    'imagepath4' => 'imagepath4',
                    'created_by_id' => 1,
                ]
            ])
            ->assertJsonStructureExact([
                'data'   => [
                    'title',
                    'description',
                    'type',
                    'status',
                    'assigned_to_id',
                    'imagepath1',
                    'imagepath2',
                    'imagepath3',
                    'imagepath4',
                    'created_by_id',
                    'company_id',
                    'updated_at',
                    'created_at',
                    'id'
                ]
            ]);
    }

    /** @test */
    function list_of_tickets()
    {
        $this->json('GET', '/api/tickets', [], $this->headers)
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    0 => [
                        'title',
                        'description',
                        'type',
                        'status',
                        'assigned_to_id',
                        'imagepath1',
                        'imagepath2',
                        'imagepath3',
                        'imagepath4',
                        'created_by_id',
                    ]
                ]
            ]);
        $this->assertCount(1, Ticket::all());
    }

    /** @test */
    function show_single_ticket()
    {
        $this->disableEH();
        $this->json('get', "/api/tickets/1", [], $this->headers)
            ->assertStatus(200)
            ->assertJson([
                'data'  => [
                    'title' => 'title',
                    'description' => 'description',
                    'type' => 'type',
                    'status' => 'status',
                    'assigned_to_id' => 1,
                    'imagepath1' => 'imagepath1',
                    'imagepath2' => 'imagepath2',
                    'imagepath3' => 'imagepath3',
                    'imagepath4' => 'imagepath4',
                    'created_by_id' => 1,
                ]
            ]);
    }

    /** @test */
    function update_single_ticket()
    {
        $payload = [
            'title' => 'title',
            'description' => 'description',
            'type' => 'type',
            'status' => 'status',
            'assigned_to_id' => 1,
            'imagepath1' => 'imagepath1',
            'imagepath2' => 'imagepath2',
            'imagepath3' => 'imagepath3',
            'imagepath4' => 'imagepath4',
            'created_by_id' => 1,
        ];

        $this->json('patch', '/api/tickets/1', $payload, $this->headers)
            ->assertStatus(200)
            ->assertJson([
                'data'    => [
                    'title' => 'title',
                    'description' => 'description',
                    'type' => 'type',
                    'status' => 'status',
                    'assigned_to_id' => 1,
                    'imagepath1' => 'imagepath1',
                    'imagepath2' => 'imagepath2',
                    'imagepath3' => 'imagepath3',
                    'imagepath4' => 'imagepath4',
                    'created_by_id' => 1,
                ]
            ])
            ->assertJsonStructureExact([
                'data'  => [
                    'id',
                    'company_id',
                    'title',
                    'description',
                    'type',
                    'status',
                    'assigned_to_id',
                    'imagepath1',
                    'imagepath2',
                    'imagepath3',
                    'imagepath4',
                    'created_by_id',
                    'created_at',
                    'updated_at'
                ]
            ]);
    }
}
