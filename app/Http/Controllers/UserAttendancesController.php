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

  public function masters(Request $request)
  {
    $sessionTypes = ['PRESENT', 'MEETING', 'MARKET CLOSED', 'LEAVE', 'WEEKLY OFF', 'HALF DAY'];

    return response()->json([
      'session_types' =>  $sessionTypes,
    ], 200);
  }


  /*
   * To get all user attendances
     *
   *@
   */
  public function index(Request $request)
  {
    $userAttendances = request()->company->user_attendances();

    if($request->date && $request->month == null && $request->year == null && $request->userId == null) {
      $userAttendances = $userAttendances->where('date', '=', $request->date);
    }
    if($request->month) {
      $userAttendances = $userAttendances->whereMonth('date', '=', $request->month);
    }
    if($request->year) {
      $userAttendances = $userAttendances->whereYear('date', '=', $request->year);
    }
    if($request->userId) {
      $userAttendances = $userAttendances->where('user_id', '=', $request->userId);
    }
    $userAttendances = $userAttendances->get();

    // else if($request->month && $request->userid) {
    //   $userAttendances = UserAttendance::with('user_attendance_breaks')
    //                       ->whereMonth('date', '=', $request->month)
    //                       ->where('user_id', '=', $request->userid)
    //                       ->latest()->get();
    // }
    // else if($request->month) {
    //   $userAttendances = UserAttendance::with('user_attendance_breaks')
    //                       ->whereMonth('date', '=', $request->month)
    //                       ->where('user_id', '=', $request->user()->id)->latest()->get();
    // }

    // if($request->searchDate) {
    //   $date = $request->searchDate;
    //   $userAttendances = request()->company->users()->with(['user_attendances' => function($q) use($date) {
    //       $q->where('date', '=', $date);
    //     }])->get();
    // }


    // if($request->fromDate & $request->toDate) {
    //   $fromDate = date($request->fromDate);
    //   $toDate = date($request->toDate);
    //   $userAttendances = request()->company->users()->with(['user_attendances' => function($q) use($fromDate, $toDate) {
    //       $q->whereBetween('date', [$fromDate, $toDate]);
    //     }])->get();
    // }


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
    $userAttendance->company_id = request()->company->id;
    $request->user()->user_attendances()->save($userAttendance);

    $user = User::find($userAttendance->user_id);

    $address = $userAttendance->login_address;
    $phone = '9967832161';
    $name = $user->name;
    $date = $userAttendance->date;
    $time = $userAttendance->login_time;
    $lat = $userAttendance->login_lat;
    $lng = $userAttendance->login_lng;
    $battery = '-';
    $address = $address;
    
    $this->sendSMS($phone, $name, $date, $time, $lat, $lng, $battery, $address);


    $geocodesController = new GeocodesController();
    if($user->so != null) {
      if($userAttendance->login_time && $userAttendance->login_lat)
      {
        // $request->request->add(['lat' => $userAttendance->login_lat]);
        // $request->request->add(['lng' => $userAttendance->login_lng]);

        // $address = json_decode($geocodesController->index($request)->getContent())->data;
        $address = $userAttendance->login_address;
        $phone = $user->so->phone;
        $name = $user->name;
        $date = $userAttendance->date;
        $time = $userAttendance->login_time;
        $lat = $userAttendance->login_lat;
        $lng = $userAttendance->login_lng;
        $battery = '-';
        $address = $address;
        
        $this->sendSMS($phone, $name, $date, $time, $lat, $lng, $battery, $address);
      }
    }
    if(sizeof($user->supervisors) > 0)
      if($userAttendance->login_time && $userAttendance->login_lat)
      {
        $request->request->add(['lat' => $userAttendance->login_lat]);
        $request->request->add(['lng' => $userAttendance->login_lng]);

        $address = json_decode($geocodesController->index($request)->getContent())->data;
        $phone = $user->supervisors[0]->phone;
        $name = $user->name;
        $date = $userAttendance->date;
        $time = $userAttendance->login_time;
        $lat = $userAttendance->login_lat;
        $lng = $userAttendance->login_lng;
        $battery = '-';
        $address = $address;
        
        $this->sendSMS($phone, $name, $date, $time, $lat, $lng, $battery, $address);
      }

    // $user = User::find($request->user()->id);

    // $geocodesController = new GeocodesController();
    // if(sizeof($user->supervisors) > 0) {
    //   $checkLocation = UserLocation::whereDate('created_at', '=', Carbon::parse($userAttendance->created_at)->format('Y-m-d'))
    //     ->where('user_id', '=', $user->id)
    //     ->latest()->first();
      
    //   if($checkLocation) {
    //   // if($request->user()->id == 375) {
    //     if($checkLocation->content['coords']['latitude'])
    //     {
    //       $request->request->add(['lat' => $checkLocation->content['coords']['latitude']]);
    //       $request->request->add(['lng' => $checkLocation->content['coords']['longitude']]);
    //     }
    //     $address = json_decode($geocodesController->index($request)->getContent())->data;
    //     $checkLocation->address = $address;
    //     $checkLocation->update();
    //     // $phone = $user->supervisors[0]->phone;
    //     $name = $user->name;
    //     $date = Carbon::parse($checkLocation->created_at)->format('d-m-Y');
    //     $time = $userAttendance->login_time;
    //     $lat = $checkLocation->content['coords']['latitude'];
    //     $lng = $checkLocation->content['coords']['longitude'];
    //     $battery = $checkLocation->content['battery']['level'];
    //     $address = $checkLocation->address;
        
    //     $this->sendSMS($phone, $name, $date, $time, $lat, $lng, $battery, $address);
    //     $this->sendSMS('9820704909', $name, $date, $time, $lat, $lng, $battery, $address);
    //     $this->sendSMS('9579862371', $name, $date, $time, $lat, $lng, $battery, $address);
    //   }
    // }

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
        $battery = $checkLocation->content['battery']['level'] * 100;
        $address = $checkLocation->address;
        
        $this->sendSMS($phone, $name, $date, $time, $lat, $lng, $battery, $address);
        $this->sendSMS('9820704909', $name, $date, $time, $lat, $lng, $battery, $address);
        $this->sendSMS('9579862371', $name, $date, $time, $lat, $lng, $battery, $address);
      } else {
        if(sizeof($user->supervisors) > 0)
          if($userAttendance->logout_time && $userAttendance->logout_lat)
          {
            $request->request->add(['lat' => $userAttendance->logout_lat]);
            $request->request->add(['lng' => $userAttendance->logout_lng]);

            $address = json_decode($geocodesController->index($request)->getContent())->data;
            $phone = $user->supervisors[0]->phone;
            $name = $user->name;
            $date = $userAttendance->date;
            $time = $userAttendance->logout_time;
            $lat = $userAttendance->logout_lat;
            $lng = $userAttendance->logout_lng;
            $battery = $userAttendance->battery;
            $address = $address;
            
            $this->sendSMS($phone, $name, $date, $time, $lat, $lng, $battery, $address);
          }
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
    $endpoint = "http://mobicomm.dove-sms.com//submitsms.jsp?user=PousseM&key=fc53bf6154XX&mobile=+91$phone&message=Your OTP is&senderid=POUSSE&accusage=1";
    // $endpoint = "http://mobicomm.dove-sms.com//submitsms.jsp?user=PousseM&key=fc53bf6154XX&mobile=+91$phone&message=$name%0A$date%0ALogout Time: $time%0ALocation: $address%0ABTRY: $battery %&senderid=POUSSE&accusage=1";
    $client = new \GuzzleHttp\Client();
    $client->request('GET', $endpoint);
  }
}
