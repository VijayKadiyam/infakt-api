<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\UserLocation;

class UserLocationsController extends Controller
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
    $user_locations = request()->user()->user_locations;

    if(request()->user_id) {
      $user_locations = UserLocation::where('user_id', '=', request()->user_id)
        ->get();
    }

    if(request()->date) {
      $user_locations = UserLocation::where('user_id', '=', request()->user_id)
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
      'content'  =>  'required'
    ]);

    $userLocation = new UserLocation($request->all());

    $geocodesController = new GeocodesController();
    if($userLocation->content['coords']['latitude'])
    {
      $request->request->add(['lat' => $userLocation->content['coords']['latitude']]);
      $request->request->add(['lng' => $userLocation->content['coords']['longitude']]);
      $userLocation->address = json_decode($geocodesController->index($request)->getContent())->data;
    }


    // return ($userLocation);
    $request->user()->user_locations()->save($userLocation);

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
  public function show(UserLocation $userLocation)
  {
    return response()->json([
      'data'   =>  $userLocation
    ], 200);   
  }

  /*
   * To update a user location
   *
   *@
   */
  public function update(Request $request, UserLocation $userLocation)
  {
    $request->validate([
      'content'  =>  'required'
    ]);

    $userLocation->update($request->all());
    
    return response()->json([
      'data'  =>  $userLocation,
      'success' =>  true
    ], 200);
  }
}
