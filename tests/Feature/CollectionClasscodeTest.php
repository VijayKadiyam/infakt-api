<?php

namespace Tests\Feature;

use App\CollectionClasscode;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CollectionClasscodeTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp()
    {
        parent::setUp();

        $this->company = factory(\App\Company::class)->create([
            'name' => 'test'
        ]);

        $this->headers['company-id'] = $this->company->id;

        factory(CollectionClasscode::class)->create([
            'company_id' =>  $this->company->id,
            'collection_id' => 1,
            'classcode_id' => 1,
        ]);

        $this->payload = [
            'collection_id' => 1,
            'classcode_id' => 1,
        ];
    }

    /** @test */
    function it_requires_following_details()
    {
        $this->json('post', '/api/collection_classcodes', [], $this->headers)
            ->assertStatus(422)
            ->assertExactJson([
                "errors"  =>  [
                    "collection_id"        =>  ["The collection id field is required."],
                ],
                "message" =>  "The given data was invalid."
            ]);
    }

    /** @test */
    function add_new_collection_classcode()
    {
        $this->disableEH();

        $this->json('post', '/api/collection_classcodes', $this->payload, $this->headers)
            ->assertStatus(201)
            ->assertJson([
                'data'   => [
                    'collection_id' => 1,
                    'classcode_id' => 1,
                ]
            ])
            ->assertJsonStructureExact([
                'data'   => [
                    'collection_id',
                    'classcode_id',
                    'company_id',
                    'updated_at',
                    'created_at',
                    'id'
                ]
            ]);
    }

    /** @test */
    function list_of_collection_classcodes()
    {
        $this->disableEH();
        $this->json('GET', '/api/collection_classcodes', [], $this->headers)
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    0 =>  [
                        'collection_id',
                        'classcode_id'
                    ]
                ]
            ]);
        $this->assertCount(1, CollectionClasscode::all());
    }

    /** @test */
    function show_single_collection_classcode()
    {

        $this->json('get', "/api/collection_classcodes/1", [], $this->headers)
            ->assertStatus(200)
            ->assertJson([
                'data'  => [
                    'collection_id' => 1,
                    'classcode_id' => 1,
                ]
            ]);
    }

    /** @test */
    function update_single_collection_classcode()
    {
        $payload = [
            'collection_id' => 1,
            'classcode_id' => 1,
        ];

        $this->json('patch', '/api/collection_classcodes/1', $payload, $this->headers)
            ->assertStatus(200)
            ->assertJson([
                'data'    => [
                    'collection_id' => 1,
                    'classcode_id' => 1,
                ]
            ])
            ->assertJsonStructureExact([
                'data'  => [
                    'id',
                    'company_id',
                    'collection_id',
                    'classcode_id',
                    'created_at',
                    'updated_at',
                ]
            ]);
    }

    /** @test */
    function delete_collection_classcode()
    {
        $this->json('delete', '/api/collection_classcodes/1', [], $this->headers)
            ->assertStatus(204);

        $this->assertCount(0, CollectionClasscode::all());
    }
}
