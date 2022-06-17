<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\VoucherType;

class VoucherTypeTest extends TestCase
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

    factory(VoucherType::class)->create([
      'company_id'  =>  $this->company->id 
    ]);

    $this->payload = [ 
      'name'     => 'mobile'
    ];
  }

  /** @test */
  function user_must_be_logged_in_before_accessing_the_controller()
  {
    $this->json('post', '/api/voucher_types')
      ->assertStatus(401); 
  }

  /** @test */
  function it_requires_following_details()
  {
    $this->json('post', '/api/voucher_types', [], $this->headers)
      ->assertStatus(422)
      ->assertExactJson([
          "errors"  =>  [
            "name"    =>  ["The name field is required."],
          ],
          "message" =>  "The given data was invalid."
        ]);
  }

  /** @test */
  function add_new_voucher_type()
  {
    $this->disableEH();
    $this->json('post', '/api/voucher_types', $this->payload, $this->headers)
      ->assertStatus(201)
      ->assertJson([
          'data'   =>[
            'name' => 'mobile'
          ]
        ])
      ->assertJsonStructureExact([
          'data'   => [
            'name',
            'company_id',
            'updated_at',
            'created_at',
            'id',
          ]
        ]);
  }

  /** @test */
  function list_of_voucher_types()
  {
    $this->json('GET', '/api/voucher_types',[], $this->headers)
      ->assertStatus(200)
      ->assertJsonStructure([
          'data' => [
            0=>[
              'name'
            ] 
          ]
        ]);
      $this->assertCount(1, VoucherType::all());
  }

  /** @test */
  function show_single_voucher_type()
  {
    $this->disableEH();
    $this->json('get', "/api/voucher_types/1", [], $this->headers)
      ->assertStatus(200)
      ->assertJson([
          'data'  => [
            'name'  => 'xerox',
          ]
        ]);
  }

  /** @test */
  function update_single_voucher_type()
  {
    $this->disableEH();
    $payload = [ 
      'name'  =>  'xerox 1'
    ];

    $this->json('patch', '/api/voucher_types/1', $payload, $this->headers)
      ->assertStatus(200)
      ->assertJson([
          'data'    => [
            'name'  =>  'xerox 1',
          ]
       ])
      ->assertJsonStructureExact([
          'data'  => [
            'id',
            'company_id',
            'name',
            'created_at',
            'updated_at'
          ]
      ]);
  }
}
