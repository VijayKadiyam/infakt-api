<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\GeolocatorUserLocation;

class GeolocatorUserLocationsController extends Controller
{
  public function __construct()
  {
    $this->middleware(['auth:api']);
  }

  /*
   * To get all user_locations
     *
   *@
   */
  public function index()
  {
    $user_locations = request()->user()->geolocator_user_locations;

    if(request()->user_id) {
      $user_locations = GeolocatorUserLocation::where('user_id', '=', request()->user_id)
        ->get();
    }

    if(request()->date) {
      $user_locations = GeolocatorUserLocation::where('user_id', '=', request()->user_id)
        ->whereDate('created_at', '=', request()->date)
        ->get();
    }

    return response()->json([
      'data'     =>  $user_locations,
      'success'   =>  true
    ], 200);
  }

  /*
   * To store a new user location
   *
   *@
   */
  public function store(Request $request)
  {
    $request->validate([
      'lat'  =>  'required',
      'long'  =>  'required',
    ]);

    $userLocation = new GeolocatorUserLocation($request->all());

    $request->user()->geolocator_user_locations()->save($userLocation);

    return response()->json([
      'data'    =>  $userLocation,
      'success' =>  true
    ], 201); 
  }

  /*
   * To view a single user location
   *
   *@
   */
  public function show(GeolocatorUserLocation $geolocatorUserLocation)
  {
    return response()->json([
      'data'   =>  $geolocatorUserLocation
    ], 200);   
  }

  /*
   * To update a user location
   *
   *@
   */
  public function update(Request $request, GeolocatorUserLocation $geolocatorUserLocation)
  {
    $request->validate([
      'lat'  =>  'required',
      'long'  =>  'required',
    ]);

    $geolocatorUserLocation->update($request->all());
    
    return response()->json([
      'data'  =>  $geolocatorUserLocation,
      'success' =>  true
    ], 200);
  }
}
