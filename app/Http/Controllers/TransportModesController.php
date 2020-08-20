<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\TransportMode;

class TransportModesController extends Controller
{
  public function __construct()
  {
    $this->middleware(['auth:api', 'company']);
  }

  /*
   * To get all transport modes
   *
   *@
   */
  public function index()
  {
    $transportModes = request()->company->transport_modes;

    return response()->json([
      'data'     =>  $transportModes,
      'success' =>  true
    ], 200);
  }

  /*
   * To store a new transport mode
   *
   *@
   */
  public function store(Request $request)
  {
    $request->validate([
      'name'    =>  'required'
    ]);

    $transportMode = new TransportMode($request->all());
    $request->company->transport_modes()->save($transportMode);

    return response()->json([
      'data'    =>  $transportMode
    ], 201); 
  }

  /*
   * To view a single transport mode
   *
   *@
   */
  public function show(TransportMode $transportMode)
  {
    return response()->json([
      'data'   =>  $transportMode
    ], 200);   
  }

  /*
   * To update a company designation
   *
   *@
   */
  public function update(Request $request, TransportMode $transportMode)
  {

    $request->validate([
      'name'  =>  'required',
    ]);

    $transportMode->update($request->all());
    
    return response()->json([
      'data'  =>  $transportMode
    ], 200);
  }
}
