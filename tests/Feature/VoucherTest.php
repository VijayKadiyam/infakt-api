<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\VoucherType;
use App\Voucher;

class VoucherTest extends TestCase
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

    $this->voucherType = factory(VoucherType::class)->create([
      'company_id'  =>  $this->company->id 
    ]);

    factory(Voucher::class)->create([
      'user_id'  =>  $this->user->id 
    ]);

    $this->month = (\Carbon\Carbon::now()->format('m'));

    $this->payload = [ 
      'voucher_type_id' =>  $this->voucherType->id,
      'amount'          =>  '5000',
      'description'     =>  'Hello'
    ];
  }

  /** @test */
  function user_must_be_logged_in_before_accessing_the_controller()
  {
    $this->json('post', '/api/vouchers')
      ->assertStatus(401); 
  }

  /** @test */
  function it_requires_following_details()
  {
    $this->json('post', '/api/vouchers', [], $this->headers)
      ->assertStatus(422)
      ->assertExactJson([
          "errors"  =>  [
            "voucher_type_id"  =>  ["The voucher type id field is required."],
            "amount"               =>  ["The amount field is required."],
            "description"               =>  ["The description field is required."]
          ],
          "message" =>  "The given data was invalid."
        ]);
  }

  /** @test */
  function add_new_voucher()
  {
    $this->disableEH();
    $this->json('post', '/api/vouchers', $this->payload, $this->headers)
      ->assertStatus(201)
      ->assertJson([
          'data'   =>[
            'voucher_type_id' =>  $this->voucherType->id,
            'amount'          =>  '5000',
            'description'     =>  'Hello'
          ]
        ])
      ->assertJsonStructureExact([
          'data'   => [
            'voucher_type_id',
            'amount',
            'description',
            'user_id',
            'updated_at',
            'created_at',
            'id'
          ],
          'success'
        ]);
  }

  /** @test */
  function list_of_vouchers()
  {
    $this->json('GET', '/api/vouchers',[], $this->headers)
      ->assertStatus(200)
      ->assertJsonStructure([
          'data' => [
            0=>[
              'amount'
            ] 
          ]
        ]);
      $this->assertCount(1, Voucher::all());
  }

  /** @test */
  function list_of_vouchers_of_request_user()
  {
    $this->json('GET', '/api/vouchers?user_id=' . $this->user->id . '&month=' . $this->month,[], $this->headers)
      ->assertStatus(200)
      ->assertJsonStructure([
          'data' => [
            0=>[
              'amount'
            ] 
          ]
        ]);
      $this->assertCount(1, Voucher::all());
  }

  /** @test */
  function show_single_voucher()
  {
    $this->disableEH();
    $this->json('get', "/api/vouchers/1", [], $this->headers)
      ->assertStatus(200)
      ->assertJson([
          'data'  => [
            'amount'          =>  '200',
          ]
        ]);
  }

  /** @test */
  function update_single_voucher()
  {
    $this->disableEH();
    $payload = [ 
      'voucher_type_id' =>  $this->voucherType->id,
      'amount'          =>  '5001',
      'description'     =>  'Hello'
    ];

    $this->json('patch', '/api/vouchers/1', $payload, $this->headers)
      ->assertStatus(200)
      ->assertJson([
          'data'    => [
          'voucher_type_id' =>  $this->voucherType->id,
          'amount'          =>  '5001',
          'description'     =>  'Hello'
          ]
       ])
      ->assertJsonStructureExact([
          'data'  => [
            'id',
            'user_id',
            'voucher_type_id',
            'amount',
            'description',
            'created_at',
            'updated_at',
          ],
          'success'
      ]);
  }
}
