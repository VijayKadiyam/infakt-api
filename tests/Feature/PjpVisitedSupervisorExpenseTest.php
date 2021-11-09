<?php

namespace Tests\Feature;

use App\PjpVisitedSupervisorExpense;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PjpVisitedSupervisorExpenseTest extends TestCase
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

        factory(PjpVisitedSupervisorExpense::class)->create([
            'company_id'  =>  $this->company->id
        ]);

        $this->payload = [
            'company_id' => 2,
            'pjp_visited_supervisor_id' => 1,
            'expense_type' => 'expense_type',
            'travelling_way' => 'travelling_way',
            'transport_mode' => 'transport_mode',
            'km_travelled' => 0,
            'amount' => 0,
            'description' => 'description',
        ];
    }

    /** @test */
    function it_requires_following_details()
    {
        $this->json('post', '/api/pjp_visited_supervisor_expenses', [], $this->headers)
            ->assertStatus(422)
            ->assertExactJson([
                "errors"  =>  [
                    "expense_type"    =>  ["The expense type field is required."]
                ],
                "message" =>  "The given data was invalid."
            ]);
    }

    /** @test */
    function add_new_channel_filter()
    {
        $this->json('post', '/api/pjp_visited_supervisor_expenses', $this->payload, $this->headers)
            ->assertStatus(201)
            ->assertJson([
                'data'   => [
                    'company_id' => 2,
                    'pjp_visited_supervisor_id' => 1,
                    'expense_type' => 'expense_type',
                    'travelling_way' => 'travelling_way',
                    'transport_mode' => 'transport_mode',
                    'km_travelled' => 0,
                    'amount' => 0,
                    'description' => 'description',
                ]
            ])
            ->assertJsonStructureExact([
                'data'   => [
                    'company_id',
                    'pjp_visited_supervisor_id',
                    'expense_type',
                    'travelling_way',
                    'transport_mode',
                    'km_travelled',
                    'amount',
                    'description',
                    'updated_at',
                    'created_at',
                    'id'
                ]
            ]);
    }

    /** @test */
    function list_of_pjp_visited_supervisor_expenses()
    {
        $this->json('GET', '/api/pjp_visited_supervisor_expenses', [], $this->headers)
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    0 => [
                        'company_id',
                        'pjp_visited_supervisor_id',
                        'expense_type',
                        'travelling_way',
                        'transport_mode',
                        'km_travelled',
                        'amount',
                        'description',
                    ]
                ]
            ]);
        $this->assertCount(1, PjpVisitedSupervisorExpense::all());
    }

    /** @test */
    function show_single_channel_filter()
    {
        $this->json('get', "/api/pjp_visited_supervisor_expenses/1", [], $this->headers)
            ->assertStatus(200)
            ->assertJson([
                'data'  => [
                    'company_id' => 2,
                    'pjp_visited_supervisor_id' => 1,
                    'expense_type' => 'expense_type',
                    'travelling_way' => 'travelling_way',
                    'transport_mode' => 'transport_mode',
                    'km_travelled' => 0,
                    'amount' => 0,
                    'description' => 'description',
                ]
            ]);
    }

    /** @test */
    function update_single_channel_filter()
    {
        $payload = [
            'company_id' => 2,
            'pjp_visited_supervisor_id' => 1,
            'expense_type' => 'expense_type',
            'travelling_way' => 'travelling_way',
            'transport_mode' => 'transport_mode',
            'km_travelled' => 0,
            'amount' => 0,
            'description' => 'description',
        ];

        $this->json('patch', '/api/pjp_visited_supervisor_expenses/1', $payload, $this->headers)
            ->assertStatus(200)
            ->assertJson([
                'data'    => [
                    'company_id' => 2,
                    'pjp_visited_supervisor_id' => 1,
                    'expense_type' => 'expense_type',
                    'travelling_way' => 'travelling_way',
                    'transport_mode' => 'transport_mode',
                    'km_travelled' => 0,
                    'amount' => 0,
                    'description' => 'description',
                ]
            ])
            ->assertJsonStructureExact([
                'data'  => [
                    'id',
                    'company_id',
                    'pjp_visited_supervisor_id',
                    'expense_type',
                    'travelling_way',
                    'transport_mode',
                    'km_travelled',
                    'amount',
                    'description',
                    'created_at',
                    'updated_at'
                ]
            ]);
    }
}
