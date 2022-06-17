<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

use App\Plan;
use App\PlanTravellingDetail;

class UploadTest extends TestCase
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

    $this->plan = factory(Plan::class)->create([
      'user_id'  =>  $this->user->id 
    ]);

    $this->planTravellingDetail = factory(PlanTravellingDetail::class)->create([
      'plan_id'  =>  $this->plan->id 
    ]);


  }

  /** @test */
  function user_must_be_logged_in_before_accessing_the_controller()
  {
    $this->json('post', '/api/upload_profile_image')
      ->assertStatus(401); 
  }

  /** @test */
  function it_requires_following_details()
  {
    $this->json('post', '/api/upload_profile_image', [], $this->headers)
      ->assertStatus(422)
      ->assertExactJson([
          "errors"  =>  [
            "user_id"       =>  ["The user id field is required."],
            "profile_image" =>  ["The profile image field is required."]
          ],
          "message" =>  "The given data was invalid."
        ]);
  }

  /** @test */
  function upload_profile_image_test()
  {
    Storage::fake('public');

    $payLoad = [
      'user_id'       =>  $this->user->id,
      'profile_image' => $profileImage = UploadedFile::fake()->image('random.jpg')
    ];

    $this->json('post', '/api/upload_profile_image', $payLoad, $this->headers)
      ->assertJson([
        'data'  => [
          'image_path'=> 'profileImages/' . $profileImage->hashName(),
        ]
      ]);

    $this->user->refresh();
    $this->assertEquals('profileImages/' . $profileImage->hashName(), $this->user->image_path);

    Storage::disk('public')->assertExists('profileImages/' . $profileImage->hashName());
  }

  /** @test */
  function upload_profile_test()
  {
    Storage::fake('public');

    $profileImage = UploadedFile::fake()->image('random.jpg');
    $name = $profileImage->hashName();

    $payLoad = [
      'image' =>  base64_encode($profileImage),
      'name'  =>  $name
    ];

    $this->json('post', '/api/upload_profile', $payLoad, $this->headers)
      ->assertJson([
        'data'  => [
          'image_path'=> 'profileImages/' . $this->user->id . '/' . $name,
        ]
      ]);

    $this->user->refresh();
    // $this->assertEquals('profileImages/' . $this->user->id . '/' . $name, $this->user->image_path);

    // Storage::disk('public')->assertExists('profileImages/' . $name);
  }

  /** @test */
  function upload_bill_image_test()
  {
    Storage::fake('public');

    $payLoad = [
      'user_id'       =>  $this->user->id,
      'bill_image' => $billImage = UploadedFile::fake()->image('random.jpg')
    ];

    $this->json('post', '/api/upload_bill/' . $this->planTravellingDetail->id, $payLoad, $this->headers)
      ->assertJson([
        'data'  => [
          'image_path' => 'billImages/' . $this->planTravellingDetail->id . '/',
        ]
      ]);
  }

}
