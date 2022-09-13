<?php

namespace Tests\Feature;

use App\Feature;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FeatureTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp()
    {
        parent::setUp();

        factory(Feature::class)->create([
            'title' => 'title',
            'description' => 'description',
        ]);

        $this->payload = [
            'title' => 'title',
            'description' => 'description',
        ];
    }

    /** @test */
    function it_requires_following_details()
    {
        $this->json('post', '/api/features', [], $this->headers)
            ->assertStatus(422)
            ->assertExactJson([
                "errors"  =>  [
                    "title"        =>  ["The title field is required."],
                ],
                "message" =>  "The given data was invalid."
            ]);
    }

    /** @test */
    function add_new_feature()
    {
        $this->disableEH();

        $this->json('post', '/api/features', $this->payload, $this->headers)
            ->assertStatus(201)
            ->assertJson([
                'data'   => [
                    'title' => 'title',
                    'description' => 'description',
                ]
            ])
            ->assertJsonStructureExact([
                'data'   => [
                    'title',
                    'description',
                    'updated_at',
                    'created_at',
                    'id'
                ]
            ]);
    }

    /** @test */
    function list_of_features()
    {
        $this->disableEH();
        $this->json('GET', '/api/features', [], $this->headers)
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    0 =>  [
                        'title',
                        'description',
                    ]
                ]
            ]);
        $this->assertCount(1, Feature::all());
    }

    /** @test */
    function show_single_feature()
    {

        $this->json('get', "/api/features/1", [], $this->headers)
            ->assertStatus(200)
            ->assertJson([
                'data'  => [
                    'title' => 'title',
                    'description' => 'description',
                ]
            ]);
    }

    /** @test */
    function update_single_feature()
    {
        $payload = [
            'title' => 'title',
            'description' => 'description',
        ];

        $this->json('patch', '/api/features/1', $payload, $this->headers)
            ->assertStatus(200)
            ->assertJson([
                'data'    => [
                    'title' => 'title',
                    'description' => 'description',
                ]
            ])
            ->assertJsonStructureExact([
                'data'  => [
                    'id',
                    'title',
                    'description',
                    'created_at',
                    'updated_at',
                ]
            ]);
    }

    /** @test */
    function delete_feature()
    {
        $this->json('delete', '/api/features/1', [], $this->headers)
            ->assertStatus(204);

        $this->assertCount(0, Feature::all());
    }
}
