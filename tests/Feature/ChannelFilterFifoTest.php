<?php

namespace Tests\Feature;

use App\ChannelFilterFifo;
use App\ChannelFilterFifoExpiry;
use App\Company;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ChannelFilterFifoTest extends TestCase
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

        factory(ChannelFilterFifo::class)->create([
            'company_id'  =>  $this->company->id
        ]);

        $this->payload = [
            'channel_filter_id' => 1,
            'retailer_id' => 1,
            'date' => 'date',
            'is_sample_article' => true,
            'is_sellable_article' => true,
            'expiries' =>  [
                0 =>  [
                    'channel_filter_fifo_id' =>  1,
                ]
            ],
        ];
    }

    /** @test */
    function it_requires_following_details()
    {
        $this->json('post', '/api/channel_filter_fifos', [], $this->headers)
            ->assertStatus(422)
            ->assertExactJson([
                "errors"  =>  [
                    "channel_filter_id"  =>  ["The channel filter id field is required."],
                ],
                "message" =>  "The given data was invalid."
            ]);
    }

    /** @test */
    function add_new_channel_filter_fifo()
    {
        $this->disableEH();
        $this->json('post', '/api/channel_filter_fifos', $this->payload, $this->headers)
            ->assertStatus(201)
            ->assertJson([
                'data'   => [
                    'channel_filter_id' => 1,
                    'retailer_id' => 1,
                    'date' => 'date',
                    'is_sample_article' => true,
                    'is_sellable_article' => true,
                    'channel_filter_fifo_expiries' =>  [
                        0 =>  [
                            'channel_filter_fifo_id' =>  '2',
                        ]
                    ],
                ]
            ])
            ->assertJsonStructureExact([
                'data'   => [
                    'channel_filter_id',
                    'retailer_id',
                    'date',
                    'is_sample_article',
                    'is_sellable_article',
                    'company_id',
                    'updated_at',
                    'created_at',
                    'id',
                    'channel_filter_fifo_expiries',
                ]
            ]);
    }

    /** @test */
    function list_of_channel_filter_fifoes()
    {
        $this->disableEH();
        $this->json('GET', '/api/channel_filter_fifos', [], $this->headers)
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    0 =>  [
                        'channel_filter_id'
                    ]
                ]
            ]);
        $this->assertCount(1, ChannelFilterFifo::all());
    }

    /** @test */
    function show_single_channel_filter_fifo()
    {
        $this->disableEH();
        $this->json('get', "/api/channel_filter_fifos/1", [], $this->headers)
            ->assertStatus(200)
            ->assertJson([
                'data'  => [
                    'channel_filter_id'  =>  1,
                ]
            ]);
    }

    /** @test */
    function update_single_channel_filter_fifo()
    {
        $this->disableEH();

        $channel_filter_fifo = factory(ChannelFilterFifo::class)->create([
            'company_id'  =>  $this->company->id
        ]);
        $channel_filter_fifoExpiry = factory(ChannelFilterFifoExpiry::class)->create([
            'channel_filter_fifo_id' =>  $channel_filter_fifo->id
        ]);

        // Old Edit + No Delete + 1 New
        $payload = [
            'id'          =>  $channel_filter_fifo->id,
            'channel_filter_id' => 1,
            'retailer_id' => 1,
            'date' => 'date',
            'is_sample_article' => true,
            'is_sellable_article' => true,
            'expiries' =>  [
                0 =>  [

                    'id'        =>  $channel_filter_fifoExpiry->id,
                    'channel_filter_fifo_id' =>  '2',
                ],
                1 =>  [

                    'channel_filter_fifo_id' =>  '2',
                ],
            ],
        ];

        $this->json('post', '/api/channel_filter_fifos', $payload, $this->headers)
            ->assertStatus(201)
            ->assertJson([
                'data'    => [
                    'id'          =>  $channel_filter_fifo->id,
                    'channel_filter_id' => 1,
                    'retailer_id' => 1,
                    'date' => 'date',
                    'is_sample_article' => true,
                    'is_sellable_article' => true,
                    'channel_filter_fifo_expiries' =>  [
                        0 =>  [

                            'id'        =>  $channel_filter_fifoExpiry->id,
                            'channel_filter_fifo_id' =>  '2',
                        ],
                        1 =>  [

                            'channel_filter_fifo_id' =>  '2',
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
                    'is_sample_article',
                    'is_sellable_article',
                    'created_at',
                    'updated_at',
                    'channel_filter_fifo_expiries',
                ]
            ]);

        // 1 Delete + 1 New
        $payload = [
            'id'          =>  $channel_filter_fifo->id,
            'channel_filter_id' => 1,
            'retailer_id' => 1,
            'date' => 'date',
            'is_sample_article' => true,
            'is_sellable_article' => true,
            'expiries' =>  [
                0 =>  [
                    'id'        =>  $channel_filter_fifoExpiry->id,
                    'channel_filter_fifo_id' =>  '2',
                ]
            ],
        ];

        $this->json('post', '/api/channel_filter_fifos', $payload, $this->headers)
            ->assertStatus(201)
            ->assertJson([
                'data'    => [
                    'id'          =>  $channel_filter_fifo->id,
                    'channel_filter_id' => 1,
                    'retailer_id' => 1,
                    'date' => 'date',
                    'is_sample_article' => true,
                    'is_sellable_article' => true,
                    'channel_filter_fifo_expiries' =>  [
                        0 =>  [
                            'id'        =>  $channel_filter_fifoExpiry->id,
                            'channel_filter_fifo_id' =>  '2',
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
                    'is_sample_article',
                    'is_sellable_article',
                    'created_at',
                    'updated_at',
                    'channel_filter_fifo_expiries',
                ]
            ]);
    }

    /** @test */
    function delete_channel_filter_fifo()
    {
        $this->json('delete', '/api/channel_filter_fifos/1', [], $this->headers)
            ->assertStatus(204);

        $this->assertCount(0, ChannelFilterFifo::all());
    }
}
