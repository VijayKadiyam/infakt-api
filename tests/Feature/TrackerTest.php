<?php

namespace Tests\Feature;

use App\Tracker;
use App\TrackerSku;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TrackerTest extends TestCase
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

        factory(Tracker::class)->create([
            'company_id'  =>  $this->company->id
        ]);

        $this->payload = [
            'retailer_id' => 1,
            'customer_name' => 'Customer Name',
            'contact_no' => 'Contact no',
            'email_id' => 'Email id',
            'tracker_type' => 'Sample',
            'skus' =>  [
                0 =>  [
                    'tracker_id' =>  1,
                ]
            ],
        ];
    }

    /** @test */
    function it_requires_following_details()
    {
        $this->json('post', '/api/trackers', [], $this->headers)
            ->assertStatus(422)
            ->assertExactJson([
                "errors"  =>  [
                    "retailer_id"  =>  ["The retailer id field is required."],
                ],
                "message" =>  "The given data was invalid."
            ]);
    }

    /** @test */
    function add_new_tracker()
    {
        $this->disableEH();
        $this->json('post', '/api/trackers', $this->payload, $this->headers)
            ->assertStatus(201)
            ->assertJson([
                'data'   => [
                    'retailer_id' => 1,
                    'customer_name' => 'Customer Name',
                    'contact_no' => 'Contact no',
                    'email_id' => 'Email id',
                    'tracker_type' => 'Sample',
                    'tracker_skus' =>  [
                        0 =>  [
                            'tracker_id' =>  '2',
                        ]
                    ],
                ]
            ])
            ->assertJsonStructureExact([
                'data'   => [
                    'retailer_id',
                    'customer_name',
                    'contact_no',
                    'email_id',
                    'tracker_type',
                    'company_id',
                    'updated_at',
                    'created_at',
                    'id',
                    'tracker_skus',
                ]
            ]);
    }

    /** @test */
    function list_of_trackeres()
    {
        $this->disableEH();
        $this->json('GET', '/api/trackers', [], $this->headers)
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    0 =>  [
                        'retailer_id'
                    ]
                ]
            ]);
        $this->assertCount(1, Tracker::all());
    }

    /** @test */
    function show_single_tracker()
    {
        $this->disableEH();
        $this->json('get', "/api/trackers/1", [], $this->headers)
            ->assertStatus(200)
            ->assertJson([
                'data'  => [
                    'retailer_id'  =>  1,
                ]
            ]);
    }

    /** @test */
    function update_single_tracker()
    {
        $this->disableEH();

        $tracker = factory(Tracker::class)->create([
            'company_id'  =>  $this->company->id
        ]);
        $trackerSku = factory(TrackerSku::class)->create([
            'tracker_id' =>  $tracker->id
        ]);

        // Old Edit + No Delete + 1 New
        $payload = [
            'id'          =>  $tracker->id,
            'retailer_id' => 1,
            'customer_name' => 'Customer Name',
            'contact_no' => 'Contact no',
            'email_id' => 'Email id',
            'tracker_type' => 'Sample',
            'skus' =>  [
                0 =>  [

                    'id'        =>  $trackerSku->id,
                    'tracker_id' =>  '2',
                ],
                1 =>  [

                    'tracker_id' =>  '2',
                ],
            ],
        ];

        $this->json('post', '/api/trackers', $payload, $this->headers)
            ->assertStatus(201)
            ->assertJson([
                'data'    => [
                    'id'          =>  $tracker->id,
                    'retailer_id' => 1,
                    'customer_name' => 'Customer Name',
                    'contact_no' => 'Contact no',
                    'email_id' => 'Email id',
                    'tracker_type' => 'Sample',
                    'tracker_skus' =>  [
                        0 =>  [

                            'id'        =>  $trackerSku->id,
                            'tracker_id' =>  '2',
                        ],
                        1 =>  [

                            'tracker_id' =>  '2',
                        ],
                    ],
                ]
            ])
            ->assertJsonStructureExact([
                'data'  => [
                    'id',
                    'company_id',
                    'retailer_id',
                    'customer_name',
                    'contact_no',
                    'email_id',
                    'tracker_type',
                    'created_at',
                    'updated_at',
                    'tracker_skus',
                ]
            ]);

        // 1 Delete + 1 New
        $payload = [
            'id'          =>  $tracker->id,
            'retailer_id' => 1,
            'customer_name' => 'Customer Name',
            'contact_no' => 'Contact no',
            'email_id' => 'Email id',
            'tracker_type' => 'Sample',
            'skus' =>  [
                0 =>  [
                    'id'        =>  $trackerSku->id,
                    'tracker_id' =>  '2',
                ]
            ],
        ];

        $this->json('post', '/api/trackers', $payload, $this->headers)
            ->assertStatus(201)
            ->assertJson([
                'data'    => [
                    'id'          =>  $tracker->id,
                    'retailer_id' => 1,
                    'customer_name' => 'Customer Name',
                    'contact_no' => 'Contact no',
                    'email_id' => 'Email id',
                    'tracker_type' => 'Sample',
                    'tracker_skus' =>  [
                        0 =>  [
                            'id'        =>  $trackerSku->id,
                            'tracker_id' =>  '2',
                        ]
                    ],
                ]
            ])
            ->assertJsonStructureExact([
                'data'  => [
                    'id',
                    'company_id',
                    'retailer_id',
                    'customer_name',
                    'contact_no',
                    'email_id',
                    'tracker_type',
                    'created_at',
                    'updated_at',
                    'tracker_skus',
                ]
            ]);
    }

    /** @test */
    function delete_tracker()
    {
        $this->json('delete', '/api/trackers/1', [], $this->headers)
            ->assertStatus(204);

        $this->assertCount(0, Tracker::all());
    }
}
