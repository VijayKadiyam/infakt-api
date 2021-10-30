<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Requisition;

class RequisitionTest extends TestCase
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

    factory(Requisition::class)->create([
      'company_id'  =>  $this->company->id 
    ]);

    $this->payload = [ 
      'title'     =>  'Title 2',
    ];
  }

  /** @test */
  function user_must_be_logged_in_before_accessing_the_controller()
  {
    $this->json('post', '/api/requisitions')
      ->assertStatus(401); 
  }

  /** @test */
  function it_requires_following_details()
  {
    $this->json('post', '/api/requisitions', [], $this->headers)
      ->assertStatus(422)
      ->assertExactJson([
          "errors"  =>  [
            "title"    =>  ["The title field is required."]
          ],
          "message" =>  "The given data was invalid."
        ]);
  }

  /** @test */
  function add_new_requistion()
  {
    $this->disableEH();
    $this->json('post', '/api/requisitions', $this->payload, $this->headers)
      ->assertStatus(201)
      ->assertJson([
          'data'   =>[
            'title'     =>  'Title 2',
          ]
        ])
      ->assertJsonStructureExact([
          'data'   => [
            'title',
            'company_id',
            'updated_at',
            'created_at',
            'id'
          ],
          'success'
        ]);
  }

  /** @test */
  function list_of_requisition()
  {
    $this->json('GET', '/api/requisitions',[], $this->headers)
      ->assertStatus(200)
      ->assertJsonStructure([
          'data' => [
            0=>[
              'title'
            ]
          ]
        ]);
      $this->assertCount(1, Requisition::all());
  }

  /** @test */
  function show_single_requisition()
  {
    $this->disableEH();
    $this->json('get', "/api/requisitions/1", [], $this->headers)
      ->assertStatus(200)
      ->assertJson([
          'data'  => [
            'title' => 'Title 1',
          ]
        ]);
  }

  /** @test */
  function update_single_requisition()
  {
    $this->disableEH();
    $payload = [ 
      'title'  =>  'Title 2 Updated'
    ];

    $this->json('patch', '/api/requisitions/1', $payload, $this->headers)
      ->assertStatus(200)
      ->assertJson([
          'data'    => [
            'title'  =>  'Title 2 Updated'
          ]
       ])
      ->assertJsonStructureExact([
          'data'  => [
            'id',
            'company_id',
            'title',
            'description',
            'requisition_type',
            'image_path',
            'created_at',
            'updated_at',
            'user_id',
          ],
          'success'
      ]);
  }
}
