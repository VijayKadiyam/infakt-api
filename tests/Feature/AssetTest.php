<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Asset;

class AssetTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp()
    {
        parent::setUp();

        $this->company = factory(\App\Company::class)->create([
        'name' => 'test'
        ]);

        $this->retailer = factory(\App\Retailer::class)->create();
        dd();

        // $this->user->assignCompany($this->company->id);
        $this->headers['company-id'] = $this->company->id;

        factory(\App\Asset::class)->create([
        'company_id'  =>  $this->company->id 
        ]);

        $this->payload = [ 
        'company_id'             =>   $this->company->id,
        'retailer_id'            =>   $this->retailer->id,
        'asset_name'             =>   'Asset1',
        'status'                 =>   'Status',
        'description'            =>   'Description'
        ];
    }
    /**
     * A basic feature test example.
     *
     * @return void
     */
    /** @test */
//   function list_of_asset()
//   {
//     $this->disableEH();
//     $this->json('GET', '/api/assets',[], $this->headers)
//       ->assertStatus(200)
//       ->assertJsonStructure([
//           'data' => [
//             0=>[
//               'asset_name',
//               'status',
//               'description'
//             ] 
//           ]
//         ]);
//       $this->assertCount(1, Asset::all());
//   }
}
