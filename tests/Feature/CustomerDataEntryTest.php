<?php

namespace Tests\Feature;

use App\CustomerDataEntry;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CustomerDataEntryTest extends TestCase
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

        factory(CustomerDataEntry::class)->create([
            'company_id'  =>  $this->company->id
        ]);

        $this->payload = [
            'user_id' => 1,
            'retailer_id' => 1,
            'name' => 'name',
            'number' => 'numeber',
            'email' => 'email',
            'product_brought' => 'product_brought',
            'sample_given' => 'sample_given',
        ];
    }

    /** @test */
    function it_requires_following_details()
    {
        $this->json('post', '/api/customer_data_entries', [], $this->headers)
            ->assertStatus(422)
            ->assertExactJson([
                "errors"  =>  [
                    "user_id"    =>  ["The user id field is required."]
                ],
                "message" =>  "The given data was invalid."
            ]);
    }

    /** @test */
    function add_new_customer_data_entry()
    {
        $this->json('post', '/api/customer_data_entries', $this->payload, $this->headers)
            ->assertStatus(201)
            ->assertJson([
                'data'   => [
                    'user_id' => 1,
                    'retailer_id' => 1,
                    'name' => 'name',
                    'number' => 'numeber',
                    'email' => 'email',
                    'product_brought' => 'product_brought',
                    'sample_given' => 'sample_given',
                ]
            ])
            ->assertJsonStructureExact([
                'data'   => [
                    'user_id',
                    'retailer_id',
                    'name',
                    'number',
                    'email',
                    'product_brought',
                    'sample_given',
                    'company_id',
                    'updated_at',
                    'created_at',
                    'id'
                ]
            ]);
    }

    /** @test */
    function list_of_customer_data_entries()
    {
        $this->json('GET', '/api/customer_data_entries', [], $this->headers)
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    0 => [
                        'user_id'
                    ]
                ]
            ]);
        $this->assertCount(1, CustomerDataEntry::all());
    }

    /** @test */
    function show_single_customer_data_entry()
    {
        $this->json('get', "/api/customer_data_entries/1", [], $this->headers)
            ->assertStatus(200)
            ->assertJson([
                'data'  => [
                    'user_id' => 1,
                    'retailer_id' => 1,
                    'name' => 'name',
                    'number' => 'numeber',
                    'email' => 'email',
                    'product_brought' => 'product_brought',
                    'sample_given' => 'sample_given',
                ]
            ]);
    }

    /** @test */
    function update_single_customer_data_entry()
    {
        $payload = [
            'user_id' => 1,
            'retailer_id' => 1,
            'name' => 'name',
            'number' => 'numeber',
            'email' => 'email',
            'product_brought' => 'product_brought',
            'sample_given' => 'sample_given',
        ];

        $this->json('patch', '/api/customer_data_entries/1', $payload, $this->headers)
            ->assertStatus(200)
            ->assertJson([
                'data'    => [
                    'user_id' => 1,
                    'retailer_id' => 1,
                    'name' => 'name',
                    'number' => 'numeber',
                    'email' => 'email',
                    'product_brought' => 'product_brought',
                    'sample_given' => 'sample_given',
                ]
            ])
            ->assertJsonStructureExact([
                'data'  => [
                    'id',
                    'company_id',
                    'user_id',
                    'retailer_id',
                    'name',
                    'number',
                    'email',
                    'product_brought',
                    'sample_given',
                    'created_at',
                    'updated_at',
                ]
            ]);
    }
}
