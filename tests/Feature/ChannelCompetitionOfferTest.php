<?php

namespace Tests\Feature;

use App\ChannelCompetitionOffer;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ChannelCompetitionOfferTest extends TestCase
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

        factory(ChannelCompetitionOffer::class)->create([
            'company_id'  =>  $this->company->id
        ]);

        $this->payload = [
            'channel_filter_id' => 1,
            'competitor_name' => 'Competitor Name',
            'description' => 'description',
            'top_articles' => 'Top Articles',
        ];
    }

    /** @test */
    function it_requires_following_details()
    {
        $this->json('post', '/api/channel_competition_offers', [], $this->headers)
            ->assertStatus(422)
            ->assertExactJson([
                "errors"  =>  [
                    "competitor_name"    =>  ["The competitor name field is required."]
                ],
                "message" =>  "The given data was invalid."
            ]);
    }

    /** @test */
    function add_new_channel_filter()
    {
        $this->json('post', '/api/channel_competition_offers', $this->payload, $this->headers)
            ->assertStatus(201)
            ->assertJson([
                'data'   => [
                    'channel_filter_id' => 1,
                    'competitor_name' => 'Competitor Name',
                    'description' => 'description',
                    'top_articles' => 'Top Articles',
                ]
            ])
            ->assertJsonStructureExact([
                'data'   => [
                    'channel_filter_id',
                    'competitor_name',
                    'description',
                    'top_articles',
                    'company_id',
                    'updated_at',
                    'created_at',
                    'id'
                ]
            ]);
    }

    /** @test */
    function list_of_channel_competition_offers()
    {
        $this->json('GET', '/api/channel_competition_offers', [], $this->headers)
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    0 => [
                        'competitor_name'
                    ]
                ]
            ]);
        $this->assertCount(1, ChannelCompetitionOffer::all());
    }

    /** @test */
    function show_single_channel_filter()
    {
        $this->json('get', "/api/channel_competition_offers/1", [], $this->headers)
            ->assertStatus(200)
            ->assertJson([
                'data'  => [
                    'channel_filter_id' => 1,
                    'competitor_name' => 'Competitor Name',
                    'description' => 'description',
                    'top_articles' => 'Top Articles',
                ]
            ]);
    }

    /** @test */
    function update_single_channel_filter()
    {
        $payload = [
            'channel_filter_id' => 1,
            'competitor_name' => 'Competitor Name',
            'description' => 'description',
            'top_articles' => 'Top Articles',
        ];

        $this->json('patch', '/api/channel_competition_offers/1', $payload, $this->headers)
            ->assertStatus(200)
            ->assertJson([
                'data'    => [
                    'channel_filter_id' => 1,
                    'competitor_name' => 'Competitor Name',
                    'description' => 'description',
                    'top_articles' => 'Top Articles',
                ]
            ])
            ->assertJsonStructureExact([
                'data'  => [
                    'id',
                    'company_id',
                    'channel_filter_id',
                    'competitor_name',
                    'description',
                    'top_articles',
                    'created_at',
                    'updated_at'
                ]
            ]);
    }
}
