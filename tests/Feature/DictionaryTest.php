<?php

namespace Tests\Feature;

use App\Dictionary;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DictionaryTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp()
    {
        parent::setUp();

        $this->company = factory(\App\Company::class)->create([
            'name' => 'test'
        ]);

        $this->headers['company-id'] = $this->company->id;

        factory(Dictionary::class)->create([
            'company_id' =>  $this->company->id,
            'keyword' => 'keyword',
        ]);

        $this->payload = [
            'keyword' => 'keyword',
        ];
    }

    /** @test */
    function it_requires_following_details()
    {
        $this->json('post', '/api/dictionaries', [], $this->headers)
            ->assertStatus(422)
            ->assertExactJson([
                "errors"  =>  [
                    "keyword"        =>  ["The keyword field is required."],
                ],
                "message" =>  "The given data was invalid."
            ]);
    }

    /** @test */
    function add_new_dictionary()
    {
        $this->disableEH();

        $this->json('post', '/api/dictionaries', $this->payload, $this->headers)
            ->assertStatus(201)
            ->assertJson([
                'data'   => [
                    'keyword' => 'keyword',
                ]
            ])
            ->assertJsonStructureExact([
                'data'   => [
                    'keyword',
                    'company_id',
                    'updated_at',
                    'created_at',
                    'id'
                ]
            ]);
    }

    /** @test */
    function list_of_dictionaries()
    {
        $this->disableEH();
        $this->json('GET', '/api/dictionaries', [], $this->headers)
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    0 =>  [
                        'keyword'
                    ]
                ]
            ]);
        $this->assertCount(1, Dictionary::all());
    }

    /** @test */
    function show_single_dictionary()
    {

        $this->json('get', "/api/dictionaries/1", [], $this->headers)
            ->assertStatus(200)
            ->assertJson([
                'data'  => [
                    'keyword' => 'keyword',
                ]
            ]);
    }

    /** @test */
    function update_single_dictionary()
    {
        $payload = [
            'keyword' => 'keyword',
        ];

        $this->json('patch', '/api/dictionaries/1', $payload, $this->headers)
            ->assertStatus(200)
            ->assertJson([
                'data'    => [
                    'keyword' => 'keyword',
                ]
            ])
            ->assertJsonStructureExact([
                'data'  => [
                    'id',
                    'company_id',
                    'keyword',
                    'response',
                    'created_at',
                    'updated_at',
                ]
            ]);
    }

    /** @test */
    function delete_dictionary()
    {
        $this->json('delete', '/api/dictionaries/1', [], $this->headers)
            ->assertStatus(204);

        $this->assertCount(0, Dictionary::all());
    }
}
