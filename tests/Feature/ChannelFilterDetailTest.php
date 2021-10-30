<?php

namespace Tests\Feature;

use App\ChannelFilterDetail;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ChannelFilterDetailTest extends TestCase
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

        factory(ChannelFilterDetail::class)->create([
            'company_id'  =>  $this->company->id
        ]);

        $this->payload = [
            'ba_1' => 'BA 1',
            'ba_1_status' => 'BA 1 Status',
            'ba_2' => 'BA 2',
            'ba_2_status' => 'BA 2 Status',
            'ba_3' => 'BA 3',
            'ba_3_status' => 'BA 3 Status',
            'ba_4' => 'BA 4',
            'ba_4_status' => 'BA 4 Status',
            'brand_block_description' => 'Barnd Block Description',
            'is_tester' => true,
            'is_planogram' => true,
            'is_grooming' => true,
            'is_uniform' => true,
            'is_tester_details' => true,
            'is_planogram_details' => true,
            'is_grooming_details' => true,
            'is_uniform_details' => true,
            'retailer_id' => 1,
            'channel_filter_id' => 1,
            'is_primary_category' => true,
            'is_secondary_category' => true,
            'is_ba_training' => true,
            'ba_training_date' => 'BA Training Date',
            'ba_training_category' => 'BA Training Category',
            'date' => 'Date',
            'visit_feedback' => 'Visit Feedback',
        ];
    }

    /** @test */
    function it_requires_following_details()
    {
        $this->json('post', '/api/channel_filter_details', [], $this->headers)
            ->assertStatus(422)
            ->assertExactJson([
                "errors"  =>  [
                    "ba_1"    =>  ["The ba 1 field is required."]
                ],
                "message" =>  "The given data was invalid."
            ]);
    }

    /** @test */
    function add_new_channel_filter_detail()
    {
        $this->disableEH();
        $this->json('post', '/api/channel_filter_details', $this->payload, $this->headers)
            ->assertStatus(201)
            ->assertJson([
                'data'   => [
                    'ba_1' => 'BA 1',
                    'ba_1_status' => 'BA 1 Status',
                    'ba_2' => 'BA 2',
                    'ba_2_status' => 'BA 2 Status',
                    'ba_3' => 'BA 3',
                    'ba_3_status' => 'BA 3 Status',
                    'ba_4' => 'BA 4',
                    'ba_4_status' => 'BA 4 Status',
                    'brand_block_description' => 'Barnd Block Description',
                    'is_tester' => true,
                    'is_planogram' => true,
                    'is_grooming' => true,
                    'is_uniform' => true,
                    'is_tester_details' => true,
                    'is_planogram_details' => true,
                    'is_grooming_details' => true,
                    'is_uniform_details' => true,
                    'retailer_id' => 1,
                    'channel_filter_id' => 1,
                    'is_primary_category' => true,
                    'is_secondary_category' => true,
                    'is_ba_training' => true,
                    'ba_training_date' => 'BA Training Date',
                    'ba_training_category' => 'BA Training Category',
                    'date' => 'Date',
                    'visit_feedback' => 'Visit Feedback',
                ]
            ])
            ->assertJsonStructureExact([
                'data'   => [
                    'ba_1',
                    'ba_1_status',
                    'ba_2',
                    'ba_2_status',
                    'ba_3',
                    'ba_3_status',
                    'ba_4',
                    'ba_4_status',
                    'brand_block_description',
                    'is_tester',
                    'is_planogram',
                    'is_grooming',
                    'is_uniform',
                    'is_tester_details',
                    'is_planogram_details',
                    'is_grooming_details',
                    'is_uniform_details',
                    'retailer_id',
                    'channel_filter_id',
                    'is_primary_category',
                    'is_secondary_category',
                    'is_ba_training',
                    'ba_training_date',
                    'ba_training_category',
                    'date',
                    'visit_feedback',
                    'company_id',
                    'updated_at',
                    'created_at',
                    'id'
                ]
            ]);
    }

    /** @test */
    function list_of_channel_filter_details()
    {
        $this->json('GET', '/api/channel_filter_details', [], $this->headers)
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    0 => [
                        'ba_1',
                        'ba_1_status',
                        'ba_2',
                        'ba_2_status',
                        'ba_3',
                        'ba_3_status',
                        'ba_4',
                        'ba_4_status',
                        'brand_block_imagepath',
                        'brand_block_description',
                        'is_tester',
                        'is_planogram',
                        'is_grooming',
                        'is_uniform',
                        'is_tester_details',
                        'is_planogram_details',
                        'is_grooming_details',
                        'is_uniform_details',
                        'retailer_id',
                        'channel_filter_id',
                        'is_primary_category',
                        'primary_category_imagepath',
                        'is_secondary_category',
                        'secondary_category_imagepath',
                        'secondary_category_fsu_imagepath',
                        'secondary_category_parasite_imagepath',
                        'gandola_imagepath',
                        'is_ba_training',
                        'ba_training_date',
                        'ba_training_category',
                        'date',
                        'visit_feedback',
                        'selfie_imagepath',
                    ]
                ]
            ]);
        $this->assertCount(1, ChannelFilterDetail::all());
    }

    /** @test */
    function show_single_channel_filter_detail()
    {
        $this->json('get', "/api/channel_filter_details/1", [], $this->headers)
            ->assertStatus(200)
            ->assertJson([
                'data'  => [
                    'ba_1' => 'BA 1',
                    'ba_1_status' => 'BA 1 Status',
                    'ba_2' => 'BA 2',
                    'ba_2_status' => 'BA 2 Status',
                    'ba_3' => 'BA 3',
                    'ba_3_status' => 'BA 3 Status',
                    'ba_4' => 'BA 4',
                    'ba_4_status' => 'BA 4 Status',
                    'brand_block_description' => 'Barnd Block Description',
                    'is_tester' => true,
                    'is_planogram' => true,
                    'is_grooming' => true,
                    'is_uniform' => true,
                    'is_tester_details' => true,
                    'is_planogram_details' => true,
                    'is_grooming_details' => true,
                    'is_uniform_details' => true,
                    'retailer_id' => 1,
                    'channel_filter_id' => 1,
                    'is_primary_category' => true,
                    'is_secondary_category' => true,
                    'is_ba_training' => true,
                    'ba_training_date' => 'BA Training Date',
                    'ba_training_category' => 'BA Training Category',
                    'date' => 'Date',
                    'visit_feedback' => 'Visit Feedback',
                ]
            ]);
    }

    /** @test */
    function update_single_channel_filter_detail()
    {
        $payload = [
            'ba_1' => 'BA 1 1',
            'ba_1_status' => 'BA 1 Status 1',
            'ba_2' => 'BA 2 1',
            'ba_2_status' => 'BA 2 Status 1',
            'ba_3' => 'BA 3 1',
            'ba_3_status' => 'BA 3 Status 1',
            'ba_4' => 'BA 4 1',
            'ba_4_status' => 'BA 4 Status 1',
            'brand_block_description' => 'Barnd Block Description 1',
            'is_tester' => false,
            'is_planogram' => false,
            'is_grooming' => false,
            'is_uniform' => false,
            'is_tester_details' => false,
            'is_planogram_details' => false,
            'is_grooming_details' => false,
            'is_uniform_details' => false,
            'retailer_id' =>  11,
            'channel_filter_id' =>  11,
            'is_primary_category' => false,
            'is_secondary_category' => false,
            'is_ba_training' => false,
            'ba_training_date' => 'BA Training Date 1',
            'ba_training_category' => 'BA Training Category 1',
            'date' => 'Date 1',
            'visit_feedback' => 'Visit Feedback 1',
        ];

        $this->json('patch', '/api/channel_filter_details/1', $payload, $this->headers)
            ->assertStatus(200)
            ->assertJson([
                'data'    => [
                    'ba_1' => 'BA 1 1',
                    'ba_1_status' => 'BA 1 Status 1',
                    'ba_2' => 'BA 2 1',
                    'ba_2_status' => 'BA 2 Status 1',
                    'ba_3' => 'BA 3 1',
                    'ba_3_status' => 'BA 3 Status 1',
                    'ba_4' => 'BA 4 1',
                    'ba_4_status' => 'BA 4 Status 1',
                    'brand_block_description' => 'Barnd Block Description 1',
                    'is_tester' => false,
                    'is_planogram' => false,
                    'is_grooming' => false,
                    'is_uniform' => false,
                    'is_tester_details' => false,
                    'is_planogram_details' => false,
                    'is_grooming_details' => false,
                    'is_uniform_details' => false,
                    'retailer_id' =>  11,
                    'channel_filter_id' =>  11,
                    'is_primary_category' => false,
                    'is_secondary_category' => false,
                    'is_ba_training' => false,
                    'ba_training_date' => 'BA Training Date 1',
                    'ba_training_category' => 'BA Training Category 1',
                    'date' => 'Date 1',
                    'visit_feedback' => 'Visit Feedback 1',
                ]
            ])
            ->assertJsonStructureExact([
                'data'  => [
                    'id',
                    'company_id',
                    'ba_1',
                    'ba_1_status',
                    'ba_2',
                    'ba_2_status',
                    'ba_3',
                    'ba_3_status',
                    'ba_4',
                    'ba_4_status',
                    'brand_block_imagepath',
                    'brand_block_description',
                    'is_tester',
                    'is_planogram',
                    'is_grooming',
                    'is_uniform',
                    'is_tester_details',
                    'is_planogram_details',
                    'is_grooming_details',
                    'is_uniform_details',
                    'retailer_id',
                    'channel_filter_id',
                    'is_primary_category',
                    'primary_category_imagepath',
                    'is_secondary_category',
                    'secondary_category_imagepath',
                    'secondary_category_fsu_imagepath',
                    'secondary_category_parasite_imagepath',
                    'gandola_imagepath',
                    'is_ba_training',
                    'ba_training_date',
                    'ba_training_category',
                    'date',
                    'visit_feedback',
                    'selfie_imagepath',
                    'created_at',
                    'updated_at',
                    'ba_training_sub_category'
                ]
            ]);
    }
}
