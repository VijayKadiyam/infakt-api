<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\DailyPhoto;

class DailyPhotoTest extends TestCase
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
    
        factory(DailyPhoto::class)->create([
            'company_id'  =>  $this->company->id 
        ]);


        $this->payload = [ 
            'image_path'    =>  '2',
            'image_path1'    =>  '1',
            'image_path2'    =>  '1',
            'image_path3'    =>  '1',
            'image_path4'    =>  '1',
            'description'   =>  'Descriptison 2',
            'title'   =>  'title 1',
            'date'   =>  'date 1',
        ];
    }

    /** @test */
    function it_requires_daily_photo_name()
    {
        $this->json('post', '/api/daily_photos', [], $this->headers)
        ->assertStatus(422)
        ->assertExactJson([
            "errors"  =>  [
                "description"  =>  ["The description field is required."]
            ],
            "message" =>  "The given data was invalid."
            ]);
    }

    /** @test */
    function add_new_daily_photo()
    {
        $this->disableEH();
        $this->json('post', '/api/daily_photos', $this->payload, $this->headers)
        ->assertStatus(201)
        ->assertJson([
            'data'  =>  [
                'image_path'    =>  '2',
                'image_path1'    =>  '1',
                'image_path2'    =>  '1',
                'image_path3'    =>  '1',
                'image_path4'    =>  '1',
                'description'   =>  'Descriptison 2',
                'title'   =>  'title 1',
                'date'   =>  'date 1',
            ]
            ])
        ->assertJsonStructureExact([
            'data'  =>  [
                'image_path',
                'image_path1',
                'image_path2',
                'image_path3',
                'image_path4',
                'description',
                'title',
                'date',
                'company_id',
                'updated_at',
                'created_at',
                'id'
            ]
            ]);
    }

    /** @test */
    function list_of_daily_photos()
    {
        $this->json('GET', '/api/daily_photos', [], $this->headers)
        ->assertStatus(200)
        ->assertJsonStructure([
            'data'  =>  [
                0 =>  [
                'image_path',
                'image_path1',
                'image_path2',
                'image_path3',
                'image_path4',
                'description',
                'title',
                'date'
                ] 
            ]
        ]);
        $this->assertCount(1, DailyPhoto::all());
    }

    /** @test */
    function show_single_daily_photo()
    {
        $this->json('get', "/api/daily_photos/1", [], $this->headers)
        ->assertStatus(200)
        ->assertJson([
            'data'  => [
                'image_path'    =>  '1',
                'image_path1'    =>  '1',
                'image_path2'    =>  '1',
                'image_path3'    =>  '1',
                'image_path4'    =>  '1',
                'description'   =>  'Descriptison 1',
                'title'   =>  'title 1',
                'date'   =>  'date 1',
            ]
            ])
        ->assertJsonStructureExact([
            'data'    => [
                'id',
                'company_id',
                'user_id',
                'image_path',
                'description',
                
                'created_at',
                'updated_at',
                'title',
                'date',
                'image_path1',
                'image_path2',
                'image_path3',
                'image_path4',
            ]
            ]);
    }

    /** @test */
    function update_single_daily_photo()
    {
        $payload = [ 
            'image_path'    =>  '1',
            'image_path1'    =>  '1',
                'image_path2'    =>  '1',
                'image_path3'    =>  '1',
                'image_path4'    =>  '1',
            'description'   =>  'Descriptison 1 Updated',
            'title'   =>  'title 1',
            'date'   =>  'date 1',
        ];

        $this->json('patch', '/api/daily_photos/1', $payload, $this->headers)
        ->assertStatus(200)
        ->assertJson([
            'data'    => [
                'image_path'    =>  '1',
                'image_path1'    =>  '1',
                'image_path2'    =>  '1',
                'image_path3'    =>  '1',
                'image_path4'    =>  '1',
                'description'   =>  'Descriptison 1 Updated',
                'title'   =>  'title 1',
                'date'   =>  'date 1',
            ]
            ])
        ->assertJsonStructureExact([
            'data'    => [
                'id',
                'company_id',
                'user_id',
                'image_path',
                'description',
                'created_at',
                'updated_at',
                'title',
                'date',
                'image_path1',
                'image_path2',
                'image_path3',
                'image_path4',
            ]
            ]);
    }
}
