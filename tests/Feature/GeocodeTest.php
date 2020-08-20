<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class GeocodeTest extends TestCase
{
  use DatabaseTransactions;
  
  public function setUp()
  {
    parent::setUp();
  }

  /** @test */
  function get_the_geocode()
  {
    $this->disableEH();
    $this->json('GET', '/api/geocode?lat=17&lng=19', [])
      ->assertStatus(200)
      ->assertJsonStructure([
          'data'  =>  [
            
          ]
      ]);
  }
}
