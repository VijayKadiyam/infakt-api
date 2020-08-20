<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\TravellingWay;

class TravellingWaysController extends Controller
{
  public function __construct()
  {
    $this->middleware(['auth:api', 'company']);
  }

  /*
   * To get all travelling ways
   *
   *@
   */
  public function index()
  {
    $travellingWays = request()->company->travelling_ways;

    return response()->json([
      'data'     =>  $travellingWays,
      'success'   =>  true
    ], 200);
  }

  /*
   * To store a new travelling way
   *
   *@
   */
  public function store(Request $request)
  {
    $request->validate([
      'name'    =>  'required'
    ]);

    $travellingWay = new TravellingWay($request->all());
    $request->company->travelling_ways()->save($travellingWay);

    return response()->json([
      'data'    =>  $travellingWay
    ], 201); 
  }

  /*
   * To view a single travelling way
   *
   *@
   */
  public function show(TravellingWay $travellingWay)
  {
    return response()->json([
      'data'   =>  $travellingWay
    ], 200);   
  }

  /*
   * To update a travelling way
   *
   *@
   */
  public function update(Request $request, TravellingWay $travellingWay)
  {

    $request->validate([
      'name'  =>  'required',
    ]);

    $travellingWay->update($request->all());
    
    return response()->json([
      'data'  =>  $travellingWay
    ], 200);
  }
}
