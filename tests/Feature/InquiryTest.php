<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Inquiry;

class InquiryTest extends TestCase
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

    factory(Inquiry::class)->create([
      'company_id'  =>  $this->company->id 
    ]);

    $this->payload = [ 
      'date'     =>  'Date 2',
      'company_name'  =>  'Name 1',
      'contact_person_1'  =>  'Person 1',
      'mobile_1'          =>  'Mobile 1'
    ];
  }

  /** @test */
  function user_must_be_logged_in_before_accessing_the_controller()
  {
    $this->json('post', '/api/inquiries')
      ->assertStatus(401); 
  }

  /** @test */
  function it_requires_following_details()
  {
    $this->json('post', '/api/inquiries', [], $this->headers)
      ->assertStatus(422)
      ->assertExactJson([
          "errors"  =>  [
            "date"          =>  ["The date field is required."],
            "company_name"  =>  ["The company name field is required."],
            "contact_person_1"  =>  ["The contact person 1 field is required."],
            "mobile_1"      =>  ["The mobile 1 field is required."],
          ],
          "message" =>  "The given data was invalid."
        ]);
  }

  /** @test */
  function add_new_sku_type()
  {
    $this->json('post', '/api/inquiries', $this->payload, $this->headers)
      ->assertStatus(201)
      ->assertJson([
          'data'   =>[
            'date' => 'Date 2'
          ]
        ])
      ->assertJsonStructureExact([
          'data'   => [
            'date',
            'company_name',
            'contact_person_1',
            'mobile_1',
            'company_id',
            'updated_at',
            'created_at',
            'id'
          ],
          'success'
        ]);
  }

  /** @test */
  function list_of_inquirys()
  {
    $this->disableEH();
    $this->json('GET', '/api/inquiries',[], $this->headers)
      ->assertStatus(200)
      ->assertJsonStructure([
          'data' => [
            0=>[
              'date'
            ]
          ],
          'success'
        ]);
      $this->assertCount(1, Inquiry::all());
  }

  /** @test */
  public function list_of_inquiries_of_search()
  {
    $inquiry = factory(Inquiry::class)->create([
      'company_id'  =>  $this->company->id
    ]);

    $this->json('get', '/api/inquiries?search=' . $inquiry->company_name, [], $this->headers)
      ->assertStatus(200)
      ->assertJsonStructure([
          'data' => [
            0 =>  [
              'company_name'
            ]
          ]
        ]);
  }

  /** @test */
  function show_single_inquiry()
  {
    $this->json('get', "/api/inquiries/1", [], $this->headers)
      ->assertStatus(200)
      ->assertJson([
          'data'  => [
            'date'=> 'Date 1',
          ],
          'success'
        ]);
  }

  /** @test */
  function update_single_inquiry()
  {
    $payload = [ 
      'date'  =>  'GRAM 1'
    ];

    $this->json('patch', '/api/inquiries/1', $payload, $this->headers)
      ->assertStatus(200)
      ->assertJson([
          'data'    => [
            'date'  =>  'GRAM 1',
          ]
       ])
      ->assertJsonStructureExact([
          'data'  => [
            'id',
            'company_id',
            'date', 'company_name', 'industry', 'employee_size', 'turnover', 'head_office', 'address', 'website', 'contact_person_1', 'designation', 'landline', 'mobile_1', 'mobile_2', 'email_1', 'email_2', 'contact_person_2', 'contact_person_3', 'date_of_contact', 'status',
            'created_at',
            'updated_at'
          ]
      ]);
  }
}
