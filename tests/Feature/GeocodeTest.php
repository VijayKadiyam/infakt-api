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
    $this->json('GET', '/api/geocode', [])
      ->assertStatus(200)
      ->assertJsonStructure([
          'data'  =>  [
            'datetime'
          ]
      ]);
  }
}
