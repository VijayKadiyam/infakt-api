<?php

namespace Tests\Feature;

use App\Customer;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CustomerTest extends TestCase
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

        factory(Customer::class)->create([
            'company_id'  =>  $this->company->id
        ]);

        $this->payload = [
            'user_id' => 1,
            'date' => 'date',
            'no_of_customer' => 'No Of Customer',
            'no_of_billed_customer' => 'No Of Billed Customer',
            'more_than_two' => 'More Than Two',
        ];
    }

    /** @test */
    function user_must_be_logged_in_before_accessing_the_controller()
    {
        $this->json('post', '/api/customers')
            ->assertStatus(401);
    }

    /** @test */
    function it_requires_following_details()
    {
        $this->json('post', '/api/customers', [], $this->headers)
            ->assertStatus(422)
            ->assertExactJson([
                "errors"  =>  [
                    "user_id"    =>  ["The user id field is required."]
                ],
                "message" =>  "The given data was invalid."
            ]);
    }

    /** @test */
    function add_new_customers()
    {
        $this->json('post', '/api/customers', $this->payload, $this->headers)
            ->assertStatus(201)
            ->assertJson([
                'data'   => [
                    'user_id' => 1,
                    'date' => 'date',
                    'no_of_customer' => 'No Of Customer',
                    'no_of_billed_customer' => 'No Of Billed Customer',
                    'more_than_two' => 'More Than Two',
                ]
            ])
            ->assertJsonStructureExact([
                'data'   => [
                    'user_id',
                    'date',
                    'no_of_customer',
                    'no_of_billed_customer',
                    'more_than_two',
                    'company_id',
                    'updated_at',
                    'created_at',
                    'id'
                ]
            ]);
    }

    /** @test */
    function list_of_customers()
    {
        $this->json('GET', '/api/customers', [], $this->headers)
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    0 => [
                        'user_id',
                        'date',
                        'no_of_customer',
                        'no_of_billed_customer',
                        'more_than_two',
                    ]
                ]
            ]);
        $this->assertCount(1, Customer::all());
    }

    /** @test */
    function show_single_customer()
    {
        // dd($this->user->id);
        $this->json('get', "/api/customers/1" , [], $this->headers)
            ->assertStatus(200)
            ->assertJson([
                'data'  => [
                    'user_id' => 1,
                    'date' => 'date',
                    'no_of_customer' => 'No Of Customer',
                    'no_of_billed_customer' => 'No Of Billed Customer',
                    'more_than_two' => 'More Than Two',
                ]
            ]);
    }

    /** @test */
    function update_single_customer()
    {
        $payload = [
            'user_id' => $this->user->id,
            'date' => 'date Updated',
            'no_of_customer' => 'No Of Customer Updated',
            'no_of_billed_customer' => 'No Of Billed Customer Updated',
            'more_than_two' => 'More Than Two Updated',
        ];

        $this->json('patch', '/api/customers/1', $payload, $this->headers)
            ->assertStatus(200)
            ->assertJson([
                'data'    => [
                    'user_id' => $this->user->id,
                    'date' => 'date Updated',
                    'no_of_customer' => 'No Of Customer Updated',
                    'no_of_billed_customer' => 'No Of Billed Customer Updated',
                    'more_than_two' => 'More Than Two Updated',
                ]
            ])
            ->assertJsonStructureExact([
                'data'  => [
                    'id',
                    'company_id',
                    'user_id',
                    'date',
                    'no_of_customer',
                    'no_of_billed_customer',
                    'more_than_two',
                    'created_at',
                    'updated_at'
                ]
            ]);
    }
}
