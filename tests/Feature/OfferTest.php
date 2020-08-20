<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\OfferType;
use App\Offer;

class OfferTest extends TestCase
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

    $this->offerType = factory(OfferType::class)->create([
      'company_id'  =>  $this->company->id 
    ]);

    factory(Offer::class)->create([
      'company_id'  =>  $this->company->id,
      'offer_type_id' =>  $this->offerType->id
    ]);

    $this->payload = [ 
      'company_id'  =>  $this->company->id,
      'offer_type_id' =>  $this->offerType->id,
      'offer'     =>  10
    ];
  }

  /** @test */
  function user_must_be_logged_in_before_accessing_the_controller()
  {
    $this->json('post', '/api/offers')
      ->assertStatus(401); 
  }

  /** @test */
  function it_requires_following_details()
  {
    $this->json('post', '/api/offers', [], $this->headers)
      ->assertStatus(422)
      ->assertExactJson([
          "errors"  =>  [
            "offer"    =>  ["The offer field is required."],
            'offer_type_id' =>  ["The offer type id field is required."]
          ],
          "message" =>  "The given data was invalid."
        ]);
  }

  /** @test */
  function add_new_offer()
  {
    $this->disableEH();
    $this->json('post', '/api/offers', $this->payload, $this->headers)
      ->assertStatus(201)
      ->assertJson([
          'data'   =>[
            'offer' => 10,
          ]
        ])
      ->assertJsonStructureExact([
          'data'   => [
            'offer_type_id',
            'offer',
            'company_id',
            'updated_at',
            'created_at',
            'id'
          ]
        ]);
  }

  /** @test */
  function list_of_offers()
  {
    $this->disableEH();
    $this->json('GET', '/api/offers',[], $this->headers)
      ->assertStatus(200)
      ->assertJsonStructure([
          'data' => [
            0=>[
              'offer'
            ] 
          ]
        ]);
      $this->assertCount(1, Offer::all());
  }

  /** @test */
  function show_single_offer()
  {
    $this->json('get', '/api/offers/1', [], $this->headers)
      ->assertStatus(200)
      ->assertJson([
          'data'  => [
            'offer'=> '50',
          ]
        ]);
  }

  /** @test */
  function update_single_offer()
  {
    $payload = [ 
      'offer'  =>  '51',
      'offer_type_id' =>  $this->offerType->id,
    ];

    $this->json('patch', '/api/offers/1', $payload, $this->headers)
      ->assertStatus(200)
      ->assertJson([
          'data'    => [
            'offer'  =>  '51',
          ]
       ])
      ->assertJsonStructureExact([
          'data'  => [
            'id',
            'company_id',
            'offer_type_id',
            'offer',
            'created_at',
            'updated_at',
          ]
      ]);
  }
}
