<?php

namespace Tests\Feature;

use App\Document;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DocumentTest extends TestCase
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

        factory(Document::class)->create([
            'company_id'  =>  $this->company->id
        ]);

        $this->payload = [
            'image_path' => 'image_path',
            'description' => 'description',
        ];
    }

    /** @test */
    function user_must_be_logged_in_before_accessing_the_controller()
    {
        $this->json('post', '/api/documents')
            ->assertStatus(401);
    }

    /** @test */
    function it_requires_following_details()
    {
        $this->json('post', '/api/documents', [], $this->headers)
            ->assertStatus(422)
            ->assertExactJson([
                "errors"  =>  [
                    "description"    =>  ["The description field is required."],
                ],
                "message" =>  "The given data was invalid."
            ]);
    }

    /** @test */
    function add_new_document()
    {
        $this->disableEH();
        $this->json('post', '/api/documents', $this->payload, $this->headers)
            ->assertStatus(201)
            ->assertJson([
                'data'   => [
                    'image_path' => 'image_path',
                    'description' => 'description',
                ]
            ])
            ->assertJsonStructureExact([
                'data'   => [
                    'image_path',
                    'description',
                    'company_id',
                    'updated_at',
                    'created_at',
                    'id',
                ]
            ]);
    }

    /** @test */
    function list_of_documents()
    {
        $this->json('GET', '/api/documents', [], $this->headers)
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    0 => [
                        'image_path',
                        'description',
                    ]
                ]
            ]);
        $this->assertCount(1, Document::all());
    }

    /** @test */
    function show_single_document()
    {
        $this->disableEH();
        $this->json('get', "/api/documents/1", [], $this->headers)
            ->assertStatus(200)
            ->assertJson([
                'data'  => [
                    'image_path' => 'image_path',
                    'description' => 'description',
                ]
            ]);
    }

    /** @test */
    function update_single_document()
    {
        $this->disableEH();
        $payload = [
            'image_path' => 'image_path',
            'description' => 'description',
        ];

        $this->json('patch', '/api/documents/1', $payload, $this->headers)
            ->assertStatus(200)
            ->assertJson([
                'data'    => [
                    'image_path' => 'image_path',
                    'description' => 'description',
                ]
            ])
            ->assertJsonStructureExact([
                'data'  => [
                    'id',
                    'company_id',
                    'image_path',
                    'description',
                    'created_at',
                    'updated_at'
                ]
            ]);
    }
}
