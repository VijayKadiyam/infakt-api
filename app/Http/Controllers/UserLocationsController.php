<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\UserLocation;
use Carbon\Carbon;

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

  public function sendSMS($phone, $name, $date, $time, $lat, $lng, $battery)
  {
    $endpoint = "http://mobicomm.dove-sms.com//submitsms.jsp?user=PousseM&key=fc53bf6154XX&mobile=+91$phone&message=Dear Sir/Madam,%0A%0AName: $name%0ADate: $date%0AI have starting work at $time%0ALocation:$lat-$lng%0ABattery Percent: $battery %&senderid=POUSSE&accusage=1";
    $client = new \GuzzleHttp\Client();
    $client->request('GET', $endpoint);
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
    $userLocation->address = '';

    $geocodesController = new GeocodesController();
    if($userLocation->content['coords']['latitude'])
    {
      $request->request->add(['lat' => $userLocation->content['coords']['latitude']]);
      $request->request->add(['lng' => $userLocation->content['coords']['longitude']]);
      $userLocation->address = json_decode($geocodesController->index($request)->getContent())->data;
    }

    $request->user()->user_locations()->save($userLocation);

    

    if(sizeof($request->user()->supervisors) > 0) {
      $checkLocations = UserLocation::whereDate('created_at', '=', Carbon::parse($userLocation->created_at)->format('Y-m-d'))->get();
      if(sizeof($checkLocations) == 1) {
        $phone = $request->user()->supervisors[0]->phone;
        $name = $request->user()->name;
        $date = Carbon::parse($userLocation->created_at)->format('d-m-Y');
        $time = Carbon::parse($userLocation->created_at)->format('H:m:s');
        $lat = $userLocation->content['coords']['latitude'];
        $lng = $userLocation->content['coords']['longitude'];
        $battery = $userLocation->content['battery']['level'];
        $this->sendSMS($phone, $name, $date, $time, $lat, $lng, $battery);
      }
    }

    // return $userLocation;

    // return Carbon::parse($userLocation->created_at)->format('d-m-Y H:m:s');

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
