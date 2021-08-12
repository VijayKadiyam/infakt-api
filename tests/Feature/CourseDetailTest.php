<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Company;
use App\Course;
use App\CourseDetail;

class CourseDetailTest extends TestCase
{
    use DatabaseTransactions;
  
  public function setUp()
  {
    parent::setUp();

    $this->company = factory(\App\Company::class)->create([
        'name' => 'test'
        ]);

    $this->user->assignRole(1);
    $this->user->assignCompany($this->company->id);
    $this->headers['company-id'] = $this->company->id;   

    $this->course = factory(Course::class)->create([
      'company_id' =>  $this->company->id
    ]);

    factory(CourseDetail::class)->create([
      'course_id' =>  $this->course->id
    ]);

    $this->payload = [
      'title' =>  'Title 2',
      'description' =>  'Description 2'
    ];
  }

  /** @test */
  function it_requires_following_details()
  {
    $this->json('post', '/api/courses/' . $this->course->id . '/course_details', [], $this->headers)
      ->assertStatus(422)
      ->assertExactJson([
          "errors"  =>  [
            "title"          =>  ["The title field is required."],
            "description"   =>  ["The description field is required."],
          ],
          "message" =>  "The given data was invalid."
        ]);
  }

  /** @test */
  function add_new_course_detail()
  {
    $this->json('post', '/api/courses/' . $this->course->id . '/course_details', $this->payload, $this->headers)
      ->assertStatus(201)
      ->assertJson([
          'data'   =>[
            'title' =>  'Title 2',
          ]
        ])
      ->assertJsonStructureExact([
          'data'   => [
            'title',
            'description',
            'course_id',
            'updated_at',
            'created_at',
            'id'
          ]
        ]);
  }

  /** @test */
  function list_of_course_details()
  {
    $this->disableEH();
    $this->json('GET', '/api/courses/' . $this->course->id . '/course_details',[], $this->headers)
      ->assertStatus(200)
      ->assertJsonStructure([
          'data' => [
            0 =>  [
              'title'
            ] 
          ]
        ]);
    $this->assertCount(1, CourseDetail::all());
  }

  /** @test */
  function show_single_course_detail()
  {

    $this->json('get', '/api/courses/' . $this->course->id . '/course_details/1', [], $this->headers)
      ->assertStatus(200)
      ->assertJson([
          'data'  => [
            'title' =>  'Title 1',
          ]
        ]);
  }

  /** @test */
  function update_single_course_detail()
  {
    $payload = [ 
      'title' =>  'Title 1 Updated',
    ];

    $this->json('patch', '/api/courses/' . $this->course->id . '/course_details/1', $payload, $this->headers)
      ->assertStatus(200)
      ->assertJson([
          'data'    => [
            'title' =>  'Title 1 Updated',
          ]
       ])
      ->assertJsonStructureExact([
          'data'  => [
            'id',
            'course_id',
            'title', 'description', 'no_of_hrs', 'imagepath', 'videolink',
            'created_at',
            'updated_at',
          ]
      ]);
  }

  /** @test */
  function delete_course_detail()
  {
    $this->json('delete', '/api/courses/' . $this->course->id . '/course_details/1', [], $this->headers)
      ->assertStatus(204);

    $this->assertCount(0, CourseDetail::all());
  }
}
