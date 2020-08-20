<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Reason;

class ReasonsController extends Controller
{
  public function __construct()
  {
    $this->middleware(['auth:api', 'company']);
  }

  /*
   * To get all reason
     *
   *@
   */
  public function index()
  {
    $reasons = request()->company->reasons;

    return response()->json([
      'data'     =>  $reasons
    ], 200);
  }

  /*
   * To store a new reason
   *
   *@
   */
  public function store(Request $request)
  {
    $request->validate([
      'name'    =>  'required'
    ]);

    $reason = new Reason($request->all());
    $request->company->reasons()->save($reason);

    return response()->json([
      'data'    =>  $reason
    ], 201); 
  }

  /*
   * To view a single reason
   *
   *@
   */
  public function show(Reason $reason)
  {
    return response()->json([
      'data'   =>  $reason
    ], 200);   
  }

  /** @test */
  function update_single_company_designation()
  {
    $payload = [ 
      'name'  =>  'Reason 1 updated'
    ];

    $this->json('patch', '/api/reasons/1', $payload, $this->headers)
      ->assertStatus(200)
      ->assertJson([
          'data'    => [
            'name'  =>  'Reason 1 updated',
          ]
       ])
      ->assertJsonStructureExact([
          'data'  => [
            'id',
            'company_id',
            'name',
            'created_at',
            'updated_at'
          ]
      ]);
  }

  /*
   * To update a reason
   *
   *@
   */
  public function update(Request $request, Reason $reason)
  {
    $request->validate([
      'name'  =>  'required',
    ]);

    $reason->update($request->all());
      
    return response()->json([
      'data'  =>  $reason
    ], 200);
  }
}
