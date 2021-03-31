<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\ShelfAnalysis;

class ShelfAnalysisTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp()
    {
        parent::setUp();

        $this->company = factory(\App\Company::class)->create([
            'name' => 'test'
        ]);
        $this->retailer = factory(\App\Retailer::class)->create();
        $this->referencePlan = factory(\App\ReferencePlan::class)->create([
            'company_id'    =>  $this->company->id
        ]);

        $this->user->assignCompany($this->company->id);
        $this->headers['company-id'] = $this->company->id;

        factory(\App\ShelfAnalysis::class)->create([
            'company_id'         =>  $this->company->id,
            'reference_plan_id'  => $this->referencePlan->id,
            'retailer_id'        => $this->retailer->id,
        ]);

        $this->payload = [
            'company_id'             =>   $this->company->id,
            'reference_plan_id'      =>   $this->referencePlan->id,
            'retailer_id'            =>   $this->retailer->id,
            'description'            =>   'Description1',
            'points'                 =>   1,
            'image_path_1'           =>   'Image Path 1',
            'image_path_2'           =>   'Image Path 2',
            'image_path_3'           =>   'Image Path 3',
            'image_path_4'           =>   'Image Path 4',
        ];
    }
    /**
     * A basic feature test example.
     *
     * @return void
     */

    /** @test */
    function add_new_shelf_analyses()
    {
        $this->disableEH();
        $this->json('post', '/api/shelf_analyses', $this->payload, $this->headers)
            ->assertStatus(201)
            ->assertJson([
                'data'   => [
                    'reference_plan_id'      =>   $this->referencePlan->id,
                    'retailer_id'            =>   $this->retailer->id,
                    'description'            =>   'Description1',
                    'points'                 =>   1,
                ]
            ])
            ->assertJsonStructureExact([
                'data'   => [
                    'company_id',
                    'reference_plan_id',
                    'retailer_id',
                    'description',
                    'points',
                    'image_path_1',
                    'image_path_2',
                    'image_path_3',
                    'image_path_4',
                    'updated_at',
                    'created_at',
                    'id'
                ],
                'success'
            ]);
    }

    /** @test */
    function list_of_shelf_analyses()
    {
        $this->disableEH();
        $this->json('GET', '/api/shelf_analyses', [], $this->headers)
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    0 => [
                        'reference_plan_id',
                        'retailer_id',
                        'description',
                        'points',
                        'image_path_1',
                        'image_path_2',
                        'image_path_3',
                        'image_path_4'
                    ]
                ]
            ]);
        $this->assertCount(1, ShelfAnalysis::all());
    }

    /** @test */
    function show_single_shelf_analyses()
    {
        $this->disableEH();
        $this->json('get', "/api/shelf_analyses/1", [], $this->headers)
            ->assertStatus(200)
            ->assertJson([
                'data'  => [
                    'reference_plan_id'      =>   $this->referencePlan->id,
                    'retailer_id'            =>   $this->retailer->id,
                    'description'            =>   'Description1',
                    'points'                 =>   '1',
                    'image_path_1'           =>   'Image 1',
                    'image_path_2'           =>   'Image 2',
                    'image_path_3'           =>   'Image 3',
                    'image_path_4'           =>   'Image 4',
                ]
            ]);
    }

    /** @test */
    function update_single_shelf_analyses()
    {
        $this->disableEH();
        $payload = [
            'reference_plan_id'      =>   $this->referencePlan->id,
            'retailer_id'            =>   $this->retailer->id,
            'description'            =>   'Description 2',
            'points'                 =>   1,
            'image_path_1'           =>   'Image Path 11',
            'image_path_2'           =>   'Image Path 22',
            'image_path_3'           =>   'Image Path 33',
            'image_path_4'           =>   'Image Path 44',
        ];

        $this->json('patch', '/api/shelf_analyses/1', $payload, $this->headers)
            ->assertStatus(200)
            ->assertJson([
                'data'    => [
                    'reference_plan_id'      =>   $this->referencePlan->id,
                    'retailer_id'            =>   $this->retailer->id,
                    'description'            =>   'Description 2',
                    'points'                 =>   1,
                    'image_path_1'           =>   'Image Path 11',
                    'image_path_2'           =>   'Image Path 22',
                    'image_path_3'           =>   'Image Path 33',
                    'image_path_4'           =>   'Image Path 44',
                ]
            ])
            ->assertJsonStructureExact([
                'data'  => [
                    'id',
                    'company_id',
                    'reference_plan_id',
                    'retailer_id',
                    'description',
                    'points',
                    'image_path_1',
                    'image_path_2',
                    'image_path_3',
                    'image_path_4',
                    'created_at',
                    'updated_at',
                ],
                'success'
            ]);
    }

    /** @test */
  function delete_single_shelf_analyses()
  {
    //   $this.disableEH();
    $this->json('delete', '/api/shelf_analyses/1', [], $this->headers)
      ->assertStatus(200);     
    $this->assertCount(0, ShelfAnalysis::all()); 
  }
}
