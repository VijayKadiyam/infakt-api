<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Company;
use App\Value;
use App\ValueList;

class ValueListTest extends TestCase
{
  use DatabaseTransactions;

  public function setUp()
  {
    parent::setUp();

    $this->company = factory(Company::class)->create();

    $this->user->assignRole(1);
    $this->user->assignCompany($this->company->id);
    $this->headers['company-id'] = $this->company->id;

    $this->value = factory(Value::class)->create([
      'company_id' =>  $this->company->id
    ]);

    factory(ValueList::class)->create([
      'value_id' =>  $this->value->id
    ]);

    $this->payload = [
      'value_id'     =>  $this->value->id,
      'company_id'      =>  $this->company->id,
      'description'  =>  'Description 2',
    ];
  }

  /** @test */
  function it_requires_following_details()
  {
    $this->json('post', '/api/values/' . $this->value->id . '/value_lists', [], $this->headers)
      ->assertStatus(422)
      ->assertExactJson([
        "errors"  =>  [
          "value_id"      =>  ["The value id field is required."],
          "company_id"      =>  ["The company id field is required."],
          "description"   =>  ["The description field is required."],
        ],
        "message" =>  "The given data was invalid."
      ]);
  }

  /** @test */
  function add_new_value_list()
  {
    $this->json('post', '/api/values/' . $this->value->id . '/value_lists', $this->payload, $this->headers)
      ->assertStatus(201)
      ->assertJson([
        'data'   => [
          'description'  =>  'Description 2',
        ]
      ])
      ->assertJsonStructureExact([
        'data'   => [
          'value_id',
          'company_id',
          'description',
          'updated_at',
          'created_at',
          'id'
        ]
      ]);
  }

  /** @test */
  function array_requires_following_details()
  {
    $payload = [
      'datas'  =>  [
        0 =>  []
      ]
    ];

    $this->json('post', '/api/values/' . $this->value->id . '/value_lists_multiple', $payload, $this->headers)
      ->assertStatus(422)
      ->assertExactJson([
        "errors"  =>  [
          "datas.0.value_id"      =>  ["The datas.0.value_id field is required."],
          "datas.0.company_id"       =>  ["The datas.0.company_id field is required."],
          "datas.0.description"   =>  ["The datas.0.description field is required."],
          "datas.0.code"          =>  ["The datas.0.code field is required."],
        ],
        "message" =>  "The given data was invalid."
      ]);
  }

  /** @test */
  function add_array_of_value_list()
  {
    $this->disableEH();
    $valueList = factory(ValueList::class)->create([
      'value_id' =>  $this->value->id
    ]);

    $payload = [
      'datas'  =>  [
        [
          'id'           => $valueList->id,
          'value_id'     =>  $this->value->id,
          'company_id'      =>  $this->company->id,
          'description'  =>  'Description 3',
          'code'         => 'Code 3',
        ],
        [
          'value_id'     =>  $this->value->id,
          'company_id'      =>  $this->company->id,
          'description'  =>  'Description 2',
          'code'         => 'Code 2',
        ],
      ],
    ];

    $this->json('post', '/api/values/' . $this->value->id . '/value_lists_multiple', $payload, $this->headers)
      ->assertStatus(201)
      ->assertJson([
        'data'   => [
          0 =>  [
            'id'           => $valueList->id,
            'value_id'     =>  $this->value->id,
            'company_id'      =>  $this->company->id,
            'description'  =>  'Description 3',
            'code'         => 'Code 3',
          ],
          1 =>  [
            'value_id'     =>  $this->value->id,
            'company_id'      =>  $this->company->id,
            'description'  =>  'Description 2',
            'code'         => 'Code 2',
          ],
        ]
      ])
      ->assertJsonStructureExact([
        'data'   => [
          0 => [
            'id',
            'company_id',
            'value_id',
            'description',
            'code',
            'is_active',
            'is_deleted',
            'created_at',
            'updated_at',
          ],
          1 => [
            'value_id',
            'company_id',
            'description',
            'code',
            'updated_at',
            'created_at',
            'id'
          ]
        ]
      ]);
  }

  /** @test */
  function list_of_value_lists()
  {
    $this->disableEH();
    $this->json('GET', '/api/values/' . $this->value->id . '/value_lists', [], $this->headers)
      ->assertStatus(200)
      ->assertJsonStructure([
        'data' => [
          0 =>  [
            'description'
          ]
        ]
      ]);
    $this->assertCount(1, ValueList::all());
  }

  /** @test */
  function show_single_value_list()
  {

    $this->json('get', '/api/values/' . $this->value->id . '/value_lists/1', [], $this->headers)
      ->assertStatus(200)
      ->assertJson([
        'data'  => [
          'description'  =>  'Description 1',
        ]
      ]);
  }

  /** @test */
  function update_single_value_list()
  {
    $payload = [
      'description'  =>  'Description 1 Updated',
    ];

    $this->json('patch', '/api/values/' . $this->value->id . '/value_lists/1', $payload, $this->headers)
      ->assertStatus(200)
      ->assertJson([
        'data'    => [
          'description'  =>  'Description 1 Updated',
        ]
      ])
      ->assertJsonStructureExact([
        'data'  => [
          'id',
          'company_id',
          'value_id',
          'description',
          'code',
          'is_active',
          'is_deleted',
          'created_at',
          'updated_at',
        ]
      ]);
  }

  /** @test */
  function delete_value_list()
  {
    $this->json('delete', '/api/values/' . $this->value->id . '/value_lists/1', [], $this->headers)
      ->assertStatus(204);

    $this->assertCount(0, ValueList::all());
  }
}
