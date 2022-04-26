<?php

namespace Tests\Feature;

use App\Profile;
use App\Site;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProfileTest extends TestCase
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

        factory(Profile::class)->create([
            'company_id' =>  $this->company->id
        ]);

        $this->payload = [
            'user_id' => 1,
            'visit_call' => 'visit_call',
            'mark_in_lat' => 'mark_in_lat',
            'mark_in_lng' => 'mark_in_lng',
            'mark_out_lat' => 'mark_out_lat',
            'mark_out_lng' => 'mark_out_lng',
            'date' => 'date',
            'mobile_1' => 1,
            'email_1' => 'email_1',
            'photo_1_path' => 'photo_1_path',
            'company_name' => 'company_name',
            'industry' => 'industry',
            'employee_size' => 'employee_size',
            'turnover' => 'turnover',
            'head_office' => 'head_office',
            'address' => 'address',
            'website' => 'website',
            'contact_1_mobile' => 1,
            'contact_1_email' => 'contact_1_email',
            'contact_2_mobile' => 1,
            'contact_2_email' => 'contact_2_email',
            'contact_1_name' => 'contact_1_name',
            'contact_2_name' => 'contact_2_name',
            'product_offered' => 'product_offered',
            'deal_date' => 'deal_date',
            'agreement_date' => 'agreement_date',
            'terms' => 'terms',
            'remarks' => 'remarks',
            'next_meeting_date' => 'next_meeting_date',
            'is_active' => true,
            'is_deleted' => false,
        ];
    }

    /** @test */
    function it_requires_following_details()
    {
        $this->json('post', '/api/profiles', [], $this->headers)
            ->assertStatus(422)
            ->assertExactJson([
                "errors"  =>  [
                    "user_id"        =>  ["The user id field is required."],
                ],
                "message" =>  "The given data was invalid."
            ]);
    }

    /** @test */
    function add_new_profile()
    {
        $this->disableEH();
        $this->json('post', '/api/profiles', $this->payload, $this->headers)
            ->assertStatus(201)
            ->assertJson([
                'data'   => [
                    'user_id' => 1,
                    'visit_call' => 'visit_call',
                    'mark_in_lat' => 'mark_in_lat',
                    'mark_in_lng' => 'mark_in_lng',
                    'mark_out_lat' => 'mark_out_lat',
                    'mark_out_lng' => 'mark_out_lng',
                    'date' => 'date',
                    'mobile_1' => 1,
                    'email_1' => 'email_1',
                    'photo_1_path' => 'photo_1_path',
                    'company_name' => 'company_name',
                    'industry' => 'industry',
                    'employee_size' => 'employee_size',
                    'turnover' => 'turnover',
                    'head_office' => 'head_office',
                    'address' => 'address',
                    'website' => 'website',
                    'contact_1_mobile' => 1,
                    'contact_1_email' => 'contact_1_email',
                    'contact_2_mobile' => 1,
                    'contact_2_email' => 'contact_2_email',
                    'contact_1_name' => 'contact_1_name',
                    'contact_2_name' => 'contact_2_name',
                    'product_offered' => 'product_offered',
                    'deal_date' => 'deal_date',
                    'agreement_date' => 'agreement_date',
                    'terms' => 'terms',
                    'remarks' => 'remarks',
                    'next_meeting_date' => 'next_meeting_date',
                    'is_active' => true,
                    'is_deleted' => false,
                ]
            ])
            ->assertJsonStructureExact([
                'data'   => [
                    'user_id',
                    'visit_call',
                    'mark_in_lat',
                    'mark_in_lng',
                    'mark_out_lat',
                    'mark_out_lng',
                    'date',
                    'mobile_1',
                    'email_1',
                    'photo_1_path',
                    'company_name',
                    'industry',
                    'employee_size',
                    'turnover',
                    'head_office',
                    'address',
                    'website',
                    'contact_1_mobile',
                    'contact_1_email',
                    'contact_2_mobile',
                    'contact_2_email',
                    'contact_1_name',
                    'contact_2_name',
                    'product_offered',
                    'deal_date',
                    'agreement_date',
                    'terms',
                    'remarks',
                    'next_meeting_date',
                    'is_active',
                    'is_deleted',
                    'company_id',
                    'updated_at',
                    'created_at',
                    'id'
                ]
            ]);
    }

    /** @test */
    function list_of_profiles()
    {
        $this->disableEH();
        $this->json('GET', '/api/profiles', [], $this->headers)
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    0 =>  [
                        'user_id',
                        'visit_call',
                        'mark_in_lat',
                        'mark_in_lng',
                        'mark_out_lat',
                        'mark_out_lng',
                        'date',
                        'mobile_1',
                        'email_1',
                        'photo_1_path',
                        'company_name',
                        'industry',
                        'employee_size',
                        'turnover',
                        'head_office',
                        'address',
                        'website',
                        'contact_1_mobile',
                        'contact_1_email',
                        'contact_2_mobile',
                        'contact_2_email',
                        'contact_1_name',
                        'contact_2_name',
                        'product_offered',
                        'deal_date',
                        'agreement_date',
                        'terms',
                        'remarks',
                        'next_meeting_date',
                        'is_active',
                        'is_deleted',
                    ]
                ]
            ]);
        $this->assertCount(1, Profile::all());
    }

    /** @test */
    function show_single_profile()
    {

        $this->json('get', "/api/profiles/1", [], $this->headers)
            ->assertStatus(200)
            ->assertJson([
                'data'  => [
                    'user_id' => 1,
                    'visit_call' => 'visit_call',
                    'mark_in_lat' => 'mark_in_lat',
                    'mark_in_lng' => 'mark_in_lng',
                    'mark_out_lat' => 'mark_out_lat',
                    'mark_out_lng' => 'mark_out_lng',
                    'date' => 'date',
                    'mobile_1' => 1,
                    'email_1' => 'email_1',
                    'photo_1_path' => 'photo_1_path',
                    'company_name' => 'company_name',
                    'industry' => 'industry',
                    'employee_size' => 'employee_size',
                    'turnover' => 'turnover',
                    'head_office' => 'head_office',
                    'address' => 'address',
                    'website' => 'website',
                    'contact_1_mobile' => 1,
                    'contact_1_email' => 'contact_1_email',
                    'contact_2_mobile' => 1,
                    'contact_2_email' => 'contact_2_email',
                    'contact_1_name' => 'contact_1_name',
                    'contact_2_name' => 'contact_2_name',
                    'product_offered' => 'product_offered',
                    'deal_date' => 'deal_date',
                    'agreement_date' => 'agreement_date',
                    'terms' => 'terms',
                    'remarks' => 'remarks',
                    'next_meeting_date' => 'next_meeting_date',
                    'is_active' => true,
                    'is_deleted' => false,
                ]
            ]);
    }

    /** @test */
    function update_single_profile()
    {
        $payload = [
            'user_id' => 1,
            'visit_call' => 'visit_call',
            'mark_in_lat' => 'mark_in_lat',
            'mark_in_lng' => 'mark_in_lng',
            'mark_out_lat' => 'mark_out_lat',
            'mark_out_lng' => 'mark_out_lng',
            'date' => 'date',
            'mobile_1' => 1,
            'email_1' => 'email_1',
            'photo_1_path' => 'photo_1_path',
            'company_name' => 'company_name',
            'industry' => 'industry',
            'employee_size' => 'employee_size',
            'turnover' => 'turnover',
            'head_office' => 'head_office',
            'address' => 'address',
            'website' => 'website',
            'contact_1_mobile' => 1,
            'contact_1_email' => 'contact_1_email',
            'contact_2_mobile' => 1,
            'contact_2_email' => 'contact_2_email',
            'contact_1_name' => 'contact_1_name',
            'contact_2_name' => 'contact_2_name',
            'product_offered' => 'product_offered',
            'deal_date' => 'deal_date',
            'agreement_date' => 'agreement_date',
            'terms' => 'terms',
            'remarks' => 'remarks',
            'next_meeting_date' => 'next_meeting_date',
            'is_active' => true,
            'is_deleted' => false,
        ];

        $this->json('patch', '/api/profiles/1', $payload, $this->headers)
            ->assertStatus(200)
            ->assertJson([
                'data'    => [
                    'user_id' => 1,
                    'visit_call' => 'visit_call',
                    'mark_in_lat' => 'mark_in_lat',
                    'mark_in_lng' => 'mark_in_lng',
                    'mark_out_lat' => 'mark_out_lat',
                    'mark_out_lng' => 'mark_out_lng',
                    'date' => 'date',
                    'mobile_1' => 1,
                    'email_1' => 'email_1',
                    'photo_1_path' => 'photo_1_path',
                    'company_name' => 'company_name',
                    'industry' => 'industry',
                    'employee_size' => 'employee_size',
                    'turnover' => 'turnover',
                    'head_office' => 'head_office',
                    'address' => 'address',
                    'website' => 'website',
                    'contact_1_mobile' => 1,
                    'contact_1_email' => 'contact_1_email',
                    'contact_2_mobile' => 1,
                    'contact_2_email' => 'contact_2_email',
                    'contact_1_name' => 'contact_1_name',
                    'contact_2_name' => 'contact_2_name',
                    'product_offered' => 'product_offered',
                    'deal_date' => 'deal_date',
                    'agreement_date' => 'agreement_date',
                    'terms' => 'terms',
                    'remarks' => 'remarks',
                    'next_meeting_date' => 'next_meeting_date',
                    'is_active' => true,
                    'is_deleted' => false,
                ]
            ])
            ->assertJsonStructureExact([
                'data'  => [
                    'id',
                    'company_id',
                    'user_id',
                    'visit_call',
                    'mark_in_lat',
                    'mark_in_lng',
                    'mark_out_lat',
                    'mark_out_lng',
                    'date',
                    'mobile_1',
                    'email_1',
                    'photo_1_path',
                    'company_name',
                    'industry',
                    'employee_size',
                    'turnover',
                    'head_office',
                    'address',
                    'website',
                    'contact_1_mobile',
                    'contact_1_email',
                    'contact_2_mobile',
                    'contact_2_email',
                    'contact_1_name',
                    'contact_2_name',
                    'product_offered',
                    'deal_date',
                    'agreement_date',
                    'terms',
                    'remarks',
                    'next_meeting_date',
                    'is_active',
                    'is_deleted',
                    'created_at',
                    'updated_at',
                ]
            ]);
    }

    /** @test */
    function delete_profile()
    {
        $this->json('delete', '/api/profiles/1', [], $this->headers)
            ->assertStatus(204);

        $this->assertCount(0, Profile::all());
    }
}
