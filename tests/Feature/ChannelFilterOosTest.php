<?php

namespace Tests\Feature;

use App\ChannelFilterOos;
use App\ChannelFilterOosSku;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ChannelFilterOosTest extends TestCase
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

        factory(ChannelFilterOos::class)->create([
            'company_id'  =>  $this->company->id
        ]);

        $this->payload = [
            'channel_filter_id' => 1,
            'retailer_id' => 1,
            'date' => 'date',
            'skus' =>  [
                0 =>  [
                    'channel_filter_oos_id' =>  1,
                ]
            ],
        ];
    }

    /** @test */
    function it_requires_following_details()
    {
        $this->json('post', '/api/channel_filter_oos', [], $this->headers)
            ->assertStatus(422)
            ->assertExactJson([
                "errors"  =>  [
                    "channel_filter_id"  =>  ["The channel filter id field is required."],
                ],
                "message" =>  "The given data was invalid."
            ]);
    }

    /** @test */
    function add_new_channel_filter_oos()
    {
        $this->disableEH();
        $this->json('post', '/api/channel_filter_oos', $this->payload, $this->headers)
            ->assertStatus(201)
            ->assertJson([
                'data'   => [
                    'channel_filter_id' => 1,
                    'retailer_id' => 1,
                    'date' => 'date',
                    'channel_filter_oos_skus' =>  [
                        0 =>  [
                            'channel_filter_oos_id' =>  '2',
                        ]
                    ],
                ]
            ])
            ->assertJsonStructureExact([
                'data'   => [
                    'channel_filter_id',
                    'retailer_id',
                    'date',
                    'company_id',
                    'updated_at',
                    'created_at',
                    'id',
                    'channel_filter_oos_skus',
                ]
            ]);
    }

    /** @test */
    function list_of_channel_filter_ooses()
    {
        $this->disableEH();
        $this->json('GET', '/api/channel_filter_oos', [], $this->headers)
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    0 =>  [
                        'channel_filter_id'
                    ]
                ]
            ]);
        $this->assertCount(1, ChannelFilterOos::all());
    }

    /** @test */
    function show_single_channel_filter_oos()
    {
        $this->disableEH();
        $this->json('get', "/api/channel_filter_oos/1", [], $this->headers)
            ->assertStatus(200);
    }

    /** @test */
    function update_single_channel_filter_oos()
    {
        $this->disableEH();

        $channel_filter_oos = factory(ChannelFilterOos::class)->create([
            'company_id'  =>  $this->company->id
        ]);
        $channel_filter_oosSku = factory(ChannelFilterOosSku::class)->create([
            'channel_filter_oos_id' =>  $channel_filter_oos->id
        ]);

        // Old Edit + No Delete + 1 New
        $payload = [
            'id'          =>  $channel_filter_oos->id,
            'channel_filter_id' => 1,
            'retailer_id' => 1,
            'date' => 'date',
            'skus' =>  [
                0 =>  [

                    'id'        =>  $channel_filter_oosSku->id,
                    'channel_filter_oos_id' =>  '2',
                ],
                1 =>  [

                    'channel_filter_oos_id' =>  '2',
                ],
            ],
        ];

        $this->json('post', '/api/channel_filter_oos', $payload, $this->headers)
            ->assertStatus(201)
            ->assertJson([
                'data'    => [
                    'id'          =>  $channel_filter_oos->id,
                    'channel_filter_id' => 1,
                    'retailer_id' => 1,
                    'date' => 'date',
                    'channel_filter_oos_skus' =>  [
                        0 =>  [

                            'id'        =>  $channel_filter_oosSku->id,
                            'channel_filter_oos_id' =>  '2',
                        ],
                        1 =>  [

                            'channel_filter_oos_id' =>  '2',
                        ],
                    ],
                ]
            ])
            ->assertJsonStructureExact([
                'data'  => [
                    'id',
                    'company_id',
                    'channel_filter_id',
                    'retailer_id',
                    'date',
                    'created_at',
                    'updated_at',
                    'channel_filter_oos_skus',
                ]
            ]);

        // 1 Delete + 1 New
        $payload = [
            'id'          =>  $channel_filter_oos->id,
            'channel_filter_id' => 1,
            'retailer_id' => 1,
            'date' => 'date',
            'skus' =>  [
                0 =>  [
                    'id'        =>  $channel_filter_oosSku->id,
                    'channel_filter_oos_id' =>  '2',
                ]
            ],
        ];

        $this->json('post', '/api/channel_filter_oos', $payload, $this->headers)
            ->assertStatus(201)
            ->assertJson([
                'data'    => [
                    'id'          =>  $channel_filter_oos->id,
                    'channel_filter_id' => 1,
                    'retailer_id' => 1,
                    'date' => 'date',
                    'channel_filter_oos_skus' =>  [
                        0 =>  [
                            'id'        =>  $channel_filter_oosSku->id,
                            'channel_filter_oos_id' =>  '2',
                        ]
                    ],
                ]
            ])
            ->assertJsonStructureExact([
                'data'  => [
                    'id',
                    'company_id',
                    'channel_filter_id',
                    'retailer_id',
                    'date',
                    'created_at',
                    'updated_at',
                    'channel_filter_oos_skus',
                ]
            ]);
    }

    /** @test */
    function delete_channel_filter_oos()
    {
        $this->json('delete', '/api/channel_filter_oos/1', [], $this->headers)
            ->assertStatus(204);

        $this->assertCount(0, ChannelFilterOos::all());
    }
}
