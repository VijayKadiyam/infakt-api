<?php

namespace Tests\Feature;

use App\AboutUs;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AboutUsTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp()
    {
        parent::setUp();

        factory(AboutUs::class)->create([
            'tagline' => 'tagline',
            'info' => 'info',
            'info_1' => 'info_1',
            'description' => 'description',
        ]);

        $this->payload = [
            'tagline' => 'tagline',
            'info' => 'info',
            'info_1' => 'info_1',
            'description' => 'description',
        ];
    }

    /** @test */
    function it_requires_following_details()
    {
        $this->json('post', '/api/about_us', [], $this->headers)
            ->assertStatus(422)
            ->assertExactJson([
                "errors"  =>  [
                    "tagline"        =>  ["The tagline field is required."],
                ],
                "message" =>  "The given data was invalid."
            ]);
    }

    /** @test */
    function add_new_about_us()
    {
        $this->disableEH();

        $this->json('post', '/api/about_us', $this->payload, $this->headers)
            ->assertStatus(201)
            ->assertJson([
                'data'   => [
                    'tagline' => 'tagline',
                    'info' => 'info',
                    'info_1' => 'info_1',
                    'description' => 'description',
                ]
            ])
            ->assertJsonStructureExact([
                'data'   => [
                    'tagline',
                    'info',
                    'info_1',
                    'description',
                    'updated_at',
                    'created_at',
                    'id'
                ]
            ]);
    }

    /** @test */
    function list_of_about_us()
    {
        $this->disableEH();
        $this->json('GET', '/api/about_us', [], $this->headers)
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    0 =>  [
                        'tagline',
                        'info',
                        'info_1',
                        'description',
                    ]
                ]
            ]);
        $this->assertCount(1, AboutUs::all());
    }

    /** @test */
    function show_single_about_us()
    {

        $this->json('get', "/api/about_us/1", [], $this->headers)
            ->assertStatus(200)
            ->assertJson([
                'data'  => [
                    'tagline' => 'tagline',
                    'info' => 'info',
                    'info_1' => 'info_1',
                    'description' => 'description',
                ]
            ]);
    }

    /** @test */
    function update_single_about_us()
    {
        $payload = [
            'tagline' => 'tagline',
            'info' => 'info',
            'info_1' => 'info_1',
            'description' => 'description',
        ];

        $this->json('patch', '/api/about_us/1', $payload, $this->headers)
            ->assertStatus(200)
            ->assertJson([
                'data'    => [
                    'tagline' => 'tagline',
                    'info' => 'info',
                    'info_1' => 'info_1',
                    'description' => 'description',
                ]
            ])
            ->assertJsonStructureExact([
                'data'  => [
                    'id',
                    'tagline',
                    'info',
                    'info_1',
                    'description',
                    'created_at',
                    'updated_at',
                ]
            ]);
    }

    /** @test */
    function delete_about_us()
    {
        $this->json('delete', '/api/about_us/1', [], $this->headers)
            ->assertStatus(204);

        $this->assertCount(0, AboutUs::all());
    }
}
