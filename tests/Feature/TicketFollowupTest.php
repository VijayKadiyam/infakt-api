<?php

namespace Tests\Feature;

use App\TicketFollowup;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TicketFollowupTest extends TestCase
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

        factory(\App\TicketFollowup::class)->create([
            'company_id'  =>  $this->company->id
        ]);

        $this->payload = [
            'ticket_id' => 1,
            'description' => 'description',
            'imagepath1' => 'imagepath1',
            'imagepath2' => 'imagepath2',
            'imagepath3' => 'imagepath3',
            'imagepath4' => 'imagepath4',
            'replied_by_id' => 1,
        ];
    }


    /** @test */
    function it_requires_following_details()
    {
        $this->json('post', '/api/ticket_followups', [], $this->headers)
            ->assertStatus(422)
            ->assertExactJson([
                "errors"  =>  [
                    "description"    =>  ["The description field is required."]
                ],
                "message" =>  "The given data was invalid."
            ]);
    }

    /** @test */
    function add_new_ticket_followup()
    {
        $this->disableEH();
        $this->json('post', '/api/ticket_followups', $this->payload, $this->headers)
            ->assertStatus(201)
            ->assertJson([
                'data'   => [
                    'ticket_id' => 1,
                    'description' => 'description',
                    'imagepath1' => 'imagepath1',
                    'imagepath2' => 'imagepath2',
                    'imagepath3' => 'imagepath3',
                    'imagepath4' => 'imagepath4',
                    'replied_by_id' => 1,
                ]
            ])
            ->assertJsonStructureExact([
                'data'   => [
                    'ticket_id',
                    'description',
                    'imagepath1',
                    'imagepath2',
                    'imagepath3',
                    'imagepath4',
                    'replied_by_id',
                    'company_id',
                    'updated_at',
                    'created_at',
                    'id'
                ]
            ]);
    }

    /** @test */
    function list_of_ticket_followups()
    {
        $this->json('GET', '/api/ticket_followups', [], $this->headers)
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    0 => [
                        'ticket_id',
                        'description',
                        'imagepath1',
                        'imagepath2',
                        'imagepath3',
                        'imagepath4',
                        'replied_by_id',
                    ]
                ]
            ]);
        $this->assertCount(1, TicketFollowup::all());
    }

    /** @test */
    function show_single_ticket_followup()
    {
        $this->disableEH();
        $this->json('get', "/api/ticket_followups/1", [], $this->headers)
            ->assertStatus(200)
            ->assertJson([
                'data'  => [
                    'ticket_id' => 1,
                    'description' => 'description',
                    'imagepath1' => 'imagepath1',
                    'imagepath2' => 'imagepath2',
                    'imagepath3' => 'imagepath3',
                    'imagepath4' => 'imagepath4',
                    'replied_by_id' => 1,
                ]
            ]);
    }

    /** @test */
    function update_single_ticket_followup()
    {
        $payload = [
            'ticket_id' => 1,
            'description' => 'description',
            'imagepath1' => 'imagepath1',
            'imagepath2' => 'imagepath2',
            'imagepath3' => 'imagepath3',
            'imagepath4' => 'imagepath4',
            'replied_by_id' => 1,
        ];

        $this->json('patch', '/api/ticket_followups/1', $payload, $this->headers)
            ->assertStatus(200)
            ->assertJson([
                'data'    => [
                    'ticket_id' => 1,
                    'description' => 'description',
                    'imagepath1' => 'imagepath1',
                    'imagepath2' => 'imagepath2',
                    'imagepath3' => 'imagepath3',
                    'imagepath4' => 'imagepath4',
                    'replied_by_id' => 1,
                ]
            ])
            ->assertJsonStructureExact([
                'data'  => [
                    'id',
                    'company_id',
                    'ticket_id',
                    'description',
                    'imagepath1',
                    'imagepath2',
                    'imagepath3',
                    'imagepath4',
                    'replied_by_id',
                    'created_at',
                    'updated_at'
                ]
            ]);
    }
}
