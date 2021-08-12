<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Company;
use App\Course;

class CourseTest extends TestCase
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

    factory(Course::class)->create([
      'company_id' =>  $this->company->id
    ]);

    $this->payload = [
      'course_name' =>  'Name 2'
    ];
  }

  /** @test */
  function it_requires_following_details()
  {
    $this->json('post', '/api/courses', [], $this->headers)
      ->assertStatus(422)
      ->assertExactJson([
          "errors"  =>  [
            "course_name" =>  ["The course name field is required."],
          ],
          "message" =>  "The given data was invalid."
        ]);
  }

  /** @test */
  function add_new_course()
  {
    $this->json('post', '/api/courses', $this->payload, $this->headers)
      ->assertStatus(201)
      ->assertJson([
          'data'   =>[
            'course_name' =>  'Name 2'
          ]
        ])
      ->assertJsonStructureExact([
          'data'   => [
            'course_name',
            'company_id',
            'updated_at',
            'created_at',
            'id'
          ]
        ]);
  }

  /** @test */
  function list_of_courses()
  {
    $this->disableEH();
    $this->json('GET', '/api/courses',[], $this->headers)
      ->assertStatus(200)
      ->assertJsonStructure([
          'data' => [
            0 =>  [
              'course_name'
            ] 
          ]
        ]);
    $this->assertCount(1, Course::all());
  }

  /** @test */
  function show_single_course()
  {

    $this->json('get', "/api/courses/1", [], $this->headers)
      ->assertStatus(200)
      ->assertJson([
          'data'  => [
            'course_name' =>  'Name 1'
          ]
        ]);
  }

  /** @test */
  function update_single_course()
  {
    $payload = [ 
      'course_name' =>  'Name 2 Updated'
    ];

    $this->json('patch', '/api/courses/1', $payload, $this->headers)
      ->assertStatus(200)
      ->assertJson([
          'data'    => [
            'course_name' =>  'Name 2 Updated'
          ]
       ])
      ->assertJsonStructureExact([
          'data'  => [
            'id',
            'company_id',
            'course_name', 'description', 'instructor', 'no_of_hrs', 'imagepath',
            'created_at',
            'updated_at',
          ]
      ]);
  }

  /** @test */
  function delete_course()
  {
    $this->json('delete', '/api/courses/1', [], $this->headers)
      ->assertStatus(204);

    $this->assertCount(0, Course::all());
  }
}
