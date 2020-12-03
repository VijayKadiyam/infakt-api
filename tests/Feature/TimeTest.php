<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class TimeTest extends TestCase
{
  use DatabaseTransactions;
  
  
  public function setUp()
  {
    parent::setUp();
  }

  /** @test */
  function get_the_time()
  {
    // $this->json('GET', '/api/time', [])
    //   ->assertStatus(200)
    //   ->assertJsonStructure([
    //       'data'  =>  [
    //         'datetime'
    //       ]
    //   ]);
  }

}
