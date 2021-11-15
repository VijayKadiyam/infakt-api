<?php

namespace Tests\Feature;

use App\ReportList;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ReportListTest extends TestCase
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

        factory(ReportList::class)->create([
            'company_id'  =>  $this->company->id
        ]);

        $this->payload = [
            'report_type' => 'Report Type',
            'date' => 'Date',
        ];
    }

    /** @test */
    function user_must_be_logged_in_before_accessing_the_controller()
    {
        $this->json('post', '/api/report_lists')
            ->assertStatus(401);
    }

    /** @test */
    function it_requires_following_details()
    {
        $this->json('post', '/api/report_lists', [], $this->headers)
            ->assertStatus(422)
            ->assertExactJson([
                "errors"  =>  [
                    "report_type"    =>  ["The report type field is required."]
                ],
                "message" =>  "The given data was invalid."
            ]);
    }

    /** @test */
    function add_new_report_lists()
    {
        $this->json('post', '/api/report_lists', $this->payload, $this->headers)
            ->assertStatus(201)
            ->assertJson([
                'data'   => [
                    'report_type' => 'Report Type',
                    'date' => 'Date',
                ]
            ])
            ->assertJsonStructureExact([
                'data'   => [
                    'report_type',
                    'date',
                    'company_id',
                    'updated_at',
                    'created_at',
                    'id'
                ]
            ]);
    }

    /** @test */
    function list_of_report_lists()
    {
        $this->json('GET', '/api/report_lists', [], $this->headers)
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    0 => [
                        'report_type',
                        'date',
                    ]
                ]
            ]);
        $this->assertCount(1, ReportList::all());
    }

    /** @test */
    function show_single_report_list()
    {
        $this->json('get', "/api/report_lists/1", [], $this->headers)
            ->assertStatus(200)
            ->assertJson([
                'data'  => [
                    'report_type' => 'Report Type',
                    'date' => 'Date',
                ]
            ]);
    }

    /** @test */
    function update_single_report_list()
    {
        $payload = [
            'report_type' => 'Report Type Updated',
            'date' => 'Date Updated',
        ];

        $this->json('patch', '/api/report_lists/1', $payload, $this->headers)
            ->assertStatus(200)
            ->assertJson([
                'data'    => [
                    'report_type' => 'Report Type Updated',
                    'date' => 'Date Updated',
                ]
            ])
            ->assertJsonStructureExact([
                'data'  => [
                    'id',
                    'company_id',
                    'report_type',
                    'attachment_path',
                    'date',
                    'created_at',
                    'updated_at'
                ]
            ]);
    }
}
