<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\UserAttendance;
use App\UserLocation;
use App\User;
use Carbon\Carbon;

class UserAttendancesController extends Controller
{
  public function __construct()
  {
    $this->middleware(['auth:api', 'company']);
  }

  /*
   * To get all user attendances
     *
   *@
   */
  public function index(Request $request)
  {
    $userAttendances = request()->user()->user_attendances;

    if($request->date) {
      $userAttendances = $userAttendances->where('date', '=', $request->date)->first();
    }

    if($request->month && $request->userid) {
      $userAttendances = UserAttendance::with('user_attendance_breaks')
                          ->whereMonth('date', '=', $request->month)
                          ->where('user_id', '=', $request->userid)
                          ->latest()->get();
    }
    else if($request->month) {
      $userAttendances = UserAttendance::with('user_attendance_breaks')
                          ->whereMonth('date', '=', $request->month)
                          ->where('user_id', '=', $request->user()->id)->latest()->get();
    }

    if($request->searchDate) {
      $date = $request->searchDate;
      $userAttendances = request()->company->users()->with(['user_attendances' => function($q) use($date) {
          $q->where('date', '=', $date);
        }])->get();
    }


    if($request->fromDate & $request->toDate) {
      $fromDate = date($request->fromDate);
      $toDate = date($request->toDate);
      $userAttendances = request()->company->users()->with(['user_attendances' => function($q) use($fromDate, $toDate) {
          $q->whereBetween('date', [$fromDate, $toDate]);
        }])->get();
    }


    return response()->json([
      'data'     =>  $userAttendances,
      'success' =>  true
    ], 200);
  }

  /*
   * To store a new user attendance
   *
   *@
   */
  public function store(Request $request)
  {
    $request->validate([
      'date'        =>  'required',
      'login_time'  =>  'required',
      // 'logout_time' =>  'required',
      // 'login_lat'   =>  'required',
      // 'login_lng'   =>  'required',
      // 'logout_lat'  =>  'required',
      // 'logout_lng'  =>  'required'
    ]); 

    $userAttendance = new UserAttendance($request->all());
    $request->user()->user_attendances()->save($userAttendance);



    return response()->json([
      'data'    =>  $userAttendance,
      'success' =>  true
    ], 201); 
  }

  /*
   * To view a single user attendance
   *
   *@
   */
  public function show(UserAttendance $userAttendance)
  {
    return response()->json([
      'data'   =>  $userAttendance
    ], 200);   
  }

  /*
   * To update a user attendance
   *
   *@
   */
  public function update(Request $request, UserAttendance $userAttendance)
  {
    $request->validate([
      'date'        =>  'required',
      'login_time'  =>  'required',
      // 'logout_time' =>  'required',
      // 'login_lat'   =>  'required',
      // 'login_lng'   =>  'required',
      // 'logout_lat'  =>  'required',
      // 'logout_lng'  =>  'required'    
    ]);

    $userAttendance->update($request->all());

    $user = User::find($userAttendance->user_id);

    $geocodesController = new GeocodesController();
    if(sizeof($user->supervisors) > 0) {
      $checkLocation = UserLocation::whereDate('created_at', '=', Carbon::parse($userAttendance->created_at)->format('Y-m-d'))
        ->where('user_id', '=', $user->id)
        ->latest()->first();
      
      if($checkLocation) {
      // if($request->user()->id == 375) {
        if($checkLocation->content['coords']['latitude'])
        {
          $request->request->add(['lat' => $checkLocation->content['coords']['latitude']]);
          $request->request->add(['lng' => $checkLocation->content['coords']['longitude']]);
        }
        $address = json_decode($geocodesController->index($request)->getContent())->data;
        $checkLocation->address = $address;
        $checkLocation->update();
        $phone = $user->supervisors[0]->phone;
        $name = $user->name;
        $date = Carbon::parse($checkLocation->created_at)->format('d-m-Y');
        $time = $userAttendance->logout_time;
        $lat = $checkLocation->content['coords']['latitude'];
        $lng = $checkLocation->content['coords']['longitude'];
        $battery = $checkLocation->content['battery']['level'];
        $address = $checkLocation->address;
        
        $this->sendSMS($phone, $name, $date, $time, $lat, $lng, $battery, $address);
        $this->sendSMS('9820704909', $name, $date, $time, $lat, $lng, $battery, $address);
        $this->sendSMS('9579862371', $name, $date, $time, $lat, $lng, $battery, $address);
      }
    }

    // $userLocation = UserLocation::whereDate('created_at', '=', Carbon::parse($date)->format('Y-m-d'))->first();
      
    return response()->json([
      'data'  =>  $userAttendance,
      'success' =>  true
    ], 200);
  }

  public function sendSMS($phone, $name, $date, $time, $lat, $lng, $battery, $address)
  {
    $endpoint = "http://mobicomm.dove-sms.com//submitsms.jsp?user=PousseM&key=fc53bf6154XX&mobile=+91$phone&message=Dear Sir/Madam,%0A%0AName: $name%0ADate: $date%0ALogout Time: $time%0ALocation:$lat-$lng%0A$address%0ABattery Percent: $battery %&senderid=POUSSE&accusage=1";
    $client = new \GuzzleHttp\Client();
    $client->request('GET', $endpoint);
  }
}
