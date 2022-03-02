<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\UserAttendance;
use App\UserLocation;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\UserAttendanceMail;

class UserAttendancesController extends Controller
{
  public function __construct()
  {
    $this->middleware(['auth:api', 'company']);
  }

  public function masters(Request $request)
  {
    $sessionTypes = ['PRESENT', 'MEETING', 'MARKET CLOSED', 'LEAVE', 'WEEKLY OFF', 'HALF DAY', 'WORK FROM HOME', 'HOLIDAY'];
    $months = [
      ['text'  =>  'JANUARY', 'value' =>  1],
      ['text'  =>  'FEBRUARY', 'value' =>  2],
      ['text'  =>  'MARCH', 'value' =>  3],
      ['text'  =>  'APRIL', 'value' =>  4],
      ['text'  =>  'MAY', 'value' =>  5],
      ['text'  =>  'JUNE', 'value' =>  6],
      ['text'  =>  'JULY', 'value' =>  7],
      ['text'  =>  'AUGUST', 'value' =>  8],
      ['text'  =>  'SEPTEMBER', 'value' =>  9],
      ['text'  =>  'OCTOBER', 'value' =>  10],
      ['text'  =>  'NOVEMBER', 'value' =>  11],
      ['text'  =>  'DECEMBER', 'value' =>  12],
    ];

    $years = ['2020', '2021', '2022'];

    $supervisorsController = new UsersController();
    $request->request->add(['role_id' => 4]);
    $supervisorsResponse = $supervisorsController->index($request);

    $regions = [
      'NORTH',
      'EAST',
      'WEST',
      'SOUTH',
      'CENTRAL'
    ];

    $brands = [
      'MamaEarth',
      'Derma'
    ];
    $channels = [
      'GT',
      'MT',
      'MT - CNC',
      'IIA',
    ];
    $chain_names = [
      'GT',
      'Big Bazar',
      'Dmart',
      'Guardian',
      'H&G',
      'Lee Merche',
      'LuLu',
      'Metro CNC',
      'More Retail',
      'MT',
      'Reliance',
      'Spencer',
      'Walmart',
      'Lifestyle',
      'INCS',
      'Ximivogue',
      'Shopper Stop'
    ];
    return response()->json([
      'months'  =>  $months,
      'years'   =>  $years,
      'session_types' =>  $sessionTypes,
      'supervisors'           =>  $supervisorsResponse->getData()->data,
      'regions'               =>  $regions,
      'brands'               =>  $brands,
      'channels'               =>  $channels,
      'chain_names'               =>  $chain_names,
    ], 200);
  }


  /*
   * To get all user attendances
     *
   *@
   */
  public function index(Request $request)
  {
    $analysis = [];
    $userAttendances = request()->company->user_attendances();

    if ($request->date && $request->month == null && $request->year == null && $request->userId == null) {
      $userAttendances = $userAttendances->where('date', '=', $request->date);
    }
    if ($request->date && $request->month == null && $request->year == null) {
      $userAttendances = $userAttendances->where('date', '=', $request->date);
    }
    if ($request->month) {
      $userAttendances = $userAttendances->whereMonth('date', '=', $request->month);
    }
    if ($request->year) {
      $userAttendances = $userAttendances->whereYear('date', '=', $request->year);
    }
    if ($request->userId) {
      $userAttendances = $userAttendances->where('user_id', '=', $request->userId);
    }

    $userAttendanceData = [];
    if ($request->supervisorId) {
      $supervisorId = $request->supervisorId;
      $supervisorUsers = User::where('supervisor_id', '=', $supervisorId)
        ->get();
      $analysis['supervisorUsersCount'] = $supervisorUsers->count();
      $userAttendances = $userAttendances->whereHas('user',  function ($q) use ($supervisorId) {
        $q->where('supervisor_id', '=', $supervisorId);
      });
      $analysis['supervisorLoginUsersCount'] = $userAttendances->count();
      $analysis['supervisorNotLoginUsersCount'] = $supervisorUsers->count() - $userAttendances->count();
      $analysis['supervisorPercentLoggedIn'] = round(($analysis['supervisorLoginUsersCount'] / $analysis['supervisorUsersCount']) * 100, 2);
      foreach ($supervisorUsers as $supervisorUser) {
        $check = false;
        foreach ($userAttendances->get() as $userAttendance) {
          if ($supervisorUser->id == $userAttendance->user->id) {
            $check = true;
            array_unshift($userAttendanceData, [
              'store_name'  =>  $userAttendance->user->name ?? '-',
              'ba_name'     =>  $supervisorUser->ba_name ?? '-',
              'present'     =>  'YES',
              'date'        =>  Carbon::parse($request->date)->format('d-m-Y'),
              'time'        => ($userAttendance->login_time ? $userAttendance->login_time  : '') . ' - ' . ($userAttendance->logout_time ? $userAttendance->logout_time : ''),
              // 'time'        => '-'
            ]);
          }
        }
        if (!$check) {
          $userAttendanceData[] = [
            'store_name'  =>  $supervisorUser->name ?? '-',
            'ba_name'     =>  $supervisorUser->ba_name ?? '-',
            'present'     =>  'NO',
            'date'        =>  Carbon::parse($request->date)->format('d-m-Y'),
            'time'        =>  '-',
          ];
        }
      }
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
      'analysis'  =>  $analysis,
      'userAttendanceData'  =>  $userAttendanceData,
      'success' =>  true
    ], 200);
  }
  // user Atteandance Without Pagination for client
  public function user_attendance_without_pagination(Request $request)
  {
    $User_Attendances = [];
    $totalWorkingHrs = 0;

    if ($request->is_attendance_filter == 'YES') {

      $userAttendances = request()->company->user_attendances();
      if ($request->userId) {
        $userAttendances = $userAttendances->where('user_id', '=', $request->userId);
      }
      if ($request->month != '' && $request->year != '') {
        // IF Month & Year Filter
        $userAttendances = $userAttendances->whereMonth('date', '=', $request->month);
        $userAttendances = $userAttendances->whereYear('date', '=', $request->year);
      }
      if ($request->from_date != '' && $request->to_date != '') {
        // IF Date Range  Filter
        $userAttendances = $userAttendances->whereBetween('date', [$request->from_date, $request->to_date]);
      }
      $supervisorId = request()->supervisorId;
      if ($supervisorId != '')
        $userAttendances = $userAttendances->whereHas('user',  function ($q) use ($supervisorId) {
          $q->where('supervisor_id', '=', $supervisorId);
        });
      $userAttendances = $userAttendances->get();
      if (count($userAttendances) != 0) {
        foreach ($userAttendances as $attendance) {
          $startTime = 0;
          $finishTime = 0;
          $finishTimeIn24Hrs = 0;
          $totalDuration = 0;
          if ($attendance->logout_time == null) {
            $attendance['logout_time'] = '10:30:00';
            // $attendance['logout_time'] = '22:30:00';
            $finishTime = $attendance['logout_time'];
          }
          $startTime = Carbon::parse($attendance['login_time']);
          $logOutTime = Carbon::parse($attendance['logout_time']);
          $finishTimeIn24Hrs = $logOutTime->modify('+12 hours')->format('H:i:s');
          $finishTime = Carbon::parse($finishTimeIn24Hrs);


          $totalDuration = round($finishTime->diffInSeconds($startTime) / (60 * 60));
          $totalWorkingHrs = $totalDuration;
          $attendance['working_time'] = $totalWorkingHrs;
          $User_Attendances[] = $attendance;
        }
      }
    } else if ($request->supervisorId) {
      $supervisorId = $request->supervisorId;
      $userAttendances = request()->company->user_attendances();
      $userAttendances = $userAttendances->whereHas('user',  function ($q) use ($supervisorId) {
        $q->where('supervisor_id', '=', $supervisorId);
      });
      if ($request->month) {
        $userAttendances = $userAttendances->whereMonth('date', '=', $request->month);
      }
      if ($request->year) {
        $userAttendances = $userAttendances->whereYear('date', '=', $request->year);
      }
      $userAttendances = $userAttendances->get();
      $User_Attendances = $userAttendances;
    } else {
      $supervisors = User::with('roles')
        ->whereHas('roles',  function ($q) {
          $q->where('name', '=', 'SUPERVISOR');
        })->orderBy('name')->get();

      foreach ($supervisors as $supervisor) {

        $users = User::where('supervisor_id', '=', $supervisor->id)->get();

        foreach ($users as $user) {

          $userAttendances = request()->company->user_attendances()->where('user_id', '=', $user->id);

          if ($request->date && $request->month == null && $request->year == null && $request->userId == null) {
            $userAttendances = $userAttendances->where('date', '=', $request->date);
          }
          if ($request->date && $request->month == null && $request->year == null) {
            $userAttendances = $userAttendances->where('date', '=', $request->date);
          }
          if ($request->month) {
            $userAttendances = $userAttendances->whereMonth('date', '=', $request->month);
          }
          if ($request->year) {
            $userAttendances = $userAttendances->whereYear('date', '=', $request->year);
          }
          $userAttendances = $userAttendances->get();
          if (count($userAttendances) != 0) {
            foreach ($userAttendances as $attendance) {
              $startTime = 0;
              $finishTime = 0;
              $finishTimeIn24Hrs = 0;
              $totalDuration = 0;
              if ($attendance->logout_time == null) {
                $attendance['logout_time'] = '10:30:00';
                // $attendance['logout_time'] = '22:30:00';
                $finishTime = $attendance['logout_time'];
              }
              $startTime = Carbon::parse($attendance['login_time']);
              $logOutTime = Carbon::parse($attendance['logout_time']);
              $finishTimeIn24Hrs = $logOutTime->modify('+12 hours')->format('H:i:s');
              $finishTime = Carbon::parse($finishTimeIn24Hrs);


              $totalDuration = round($finishTime->diffInSeconds($startTime) / (60 * 60));
              $totalWorkingHrs = $totalDuration;
              $attendance['working_time'] = $totalWorkingHrs;
              $User_Attendances[] = $attendance;
            }
          }
        }
      }
    }



    return response()->json([
      'data'     =>  $User_Attendances,
      'success' =>  true
    ], 200);
  }

  // user Atteandance for client
  public function user_attendance(Request $request)
  {
    $User_Attendances = [];
    $totalWorkingHrs = 0;

    if ($request->is_attendance_filter == 'YES') {

      $userAttendances = request()->company->user_attendances();
      if ($request->userId) {
        $userAttendances = $userAttendances->where('user_id', '=', $request->userId);
      }
      if ($request->month != '' && $request->year != '') {
        // IF Month & Year Filter
        $userAttendances = $userAttendances->whereMonth('date', '=', $request->month);
        $userAttendances = $userAttendances->whereYear('date', '=', $request->year);
      }
      if ($request->from_date != '' && $request->to_date != '') {
        // IF Date Range  Filter
        $userAttendances = $userAttendances->whereBetween('date', [$request->from_date, $request->to_date]);
      }
      $region = $request->region;
      if ($region) {
        $userAttendances = $userAttendances->whereHas('user',  function ($q) use ($region) {
          $q->where('region', 'LIKE', '%' . $region . '%');
        });
      }
      $channel = $request->channel;
      if ($channel) {
        $userAttendances = $userAttendances->whereHas('user',  function ($q) use ($channel) {
          $q->where('channel', '=', $channel);
        });
      }
      $brand = $request->brand;
      if ($brand) {
        $userAttendances = $userAttendances->whereHas('user',  function ($q) use ($brand) {
          $q->where('brand', 'LIKE', '%' . $brand . '%');
        });
      }

      $supervisorId = request()->supervisorId;
      if ($supervisorId != '')
        $userAttendances = $userAttendances->whereHas('user',  function ($q) use ($supervisorId) {
          $q->where('supervisor_id', '=', $supervisorId);
        });
      if (request()->page && request()->rowsPerPage) {
        $count = $userAttendances->count();
        $userAttendances = $userAttendances->paginate(request()->rowsPerPage)->toArray();
        $userAttendances = $userAttendances['data'];
      } else {
        $userAttendances = $userAttendances->get();
        $count = $userAttendances->count();
      }
      if (count($userAttendances) != 0) {
        foreach ($userAttendances as $attendance) {
          $startTime = 0;
          $finishTime = 0;
          $finishTimeIn24Hrs = 0;
          $totalDuration = 0;
          if ($attendance['logout_time'] == null) {
            $attendance['logout_time'] = '10:30:00';
            // $attendance['logout_time'] = '22:30:00';
            $finishTime = $attendance['logout_time'];
          }
          $startTime = Carbon::parse($attendance['login_time']);
          $logOutTime = Carbon::parse($attendance['logout_time']);
          $finishTimeIn24Hrs = $logOutTime->modify('+12 hours')->format('H:i:s');
          $finishTime = Carbon::parse($finishTimeIn24Hrs);


          $totalDuration = round($finishTime->diffInSeconds($startTime) / (60 * 60));
          $totalWorkingHrs = $totalDuration;
          $attendance['working_time'] = $totalWorkingHrs;
          $User_Attendances[] = $attendance;
        }
      }
    } else if ($request->supervisorId) {
      $supervisorId = $request->supervisorId;
      $userAttendances = request()->company->user_attendances();
      $userAttendances = $userAttendances->whereHas('user',  function ($q) use ($supervisorId) {
        $q->where('supervisor_id', '=', $supervisorId);
      });
      if ($request->month) {
        $userAttendances = $userAttendances->whereMonth('date', '=', $request->month);
      }
      if ($request->year) {
        $userAttendances = $userAttendances->whereYear('date', '=', $request->year);
      }
      $userAttendances = $userAttendances->get();
      $User_Attendances = $userAttendances;
    } else {
      $supervisors = User::with('roles')
        ->whereHas('roles',  function ($q) {
          $q->where('name', '=', 'SUPERVISOR');
        })->orderBy('name')->get();

      foreach ($supervisors as $supervisor) {

        $users = User::where('supervisor_id', '=', $supervisor->id)->get();

        foreach ($users as $user) {

          $userAttendances = request()->company->user_attendances()->where('user_id', '=', $user->id);

          if ($request->date && $request->month == null && $request->year == null && $request->userId == null) {
            $userAttendances = $userAttendances->where('date', '=', $request->date);
          }
          if ($request->date && $request->month == null && $request->year == null) {
            $userAttendances = $userAttendances->where('date', '=', $request->date);
          }
          if ($request->month) {
            $userAttendances = $userAttendances->whereMonth('date', '=', $request->month);
          }
          if ($request->year) {
            $userAttendances = $userAttendances->whereYear('date', '=', $request->year);
          }
          $userAttendances = $userAttendances->get();
          if (count($userAttendances) != 0) {
            foreach ($userAttendances as $attendance) {
              $startTime = 0;
              $finishTime = 0;
              $finishTimeIn24Hrs = 0;
              $totalDuration = 0;
              if ($attendance->logout_time == null) {
                $attendance['logout_time'] = '10:30:00';
                // $attendance['logout_time'] = '22:30:00';
                $finishTime = $attendance['logout_time'];
              }
              $startTime = Carbon::parse($attendance['login_time']);
              $logOutTime = Carbon::parse($attendance['logout_time']);
              $finishTimeIn24Hrs = $logOutTime->modify('+12 hours')->format('H:i:s');
              $finishTime = Carbon::parse($finishTimeIn24Hrs);


              $totalDuration = round($finishTime->diffInSeconds($startTime) / (60 * 60));
              $totalWorkingHrs = $totalDuration;
              $attendance['working_time'] = $totalWorkingHrs;
              $User_Attendances[] = $attendance;
            }
          }
        }
      }
    }



    return response()->json([
      'data'     =>  $User_Attendances,
      'count' => $count,
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

    // $address = $userAttendance->login_address;
    // $phone = '9579862371';
    // $name = $user->name;
    // $date = $userAttendance->date;
    // $time = $userAttendance->login_time;
    // $lat = $userAttendance->login_lat;
    // $lng = $userAttendance->login_lng;
    // $battery = '-';
    // $address = $address;



    // $this->sendSMS($phone, $name, $date, $time, $lat, $lng, $battery, $address);


    $geocodesController = new GeocodesController();
    if ($user->so != null) {
      if ($userAttendance->login_time && $userAttendance->login_lat) {
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

        Mail::to($user->so->email)->send(new UserAttendanceMail($user, $userAttendance));
        $this->sendSMS($phone, $name, $date, $time, $lat, $lng, $battery, $address);
      }
    }
    if (sizeof($user->supervisors) > 0)
      if ($userAttendance->login_time && $userAttendance->login_lat) {
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
    if (sizeof($user->supervisors) > 0) {
      $checkLocation = UserLocation::whereDate('created_at', '=', Carbon::parse($userAttendance->created_at)->format('Y-m-d'))
        ->where('user_id', '=', $user->id)
        ->latest()->first();

      if ($checkLocation) {
        // if($request->user()->id == 375) {
        if ($checkLocation->content['coords']['latitude']) {
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
        if (sizeof($user->supervisors) > 0)
          if ($userAttendance->logout_time && $userAttendance->logout_lat) {
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
    // $endpoint = "http://mobicomm.dove-sms.com//submitsms.jsp?user=PousseM&key=fc53bf6154XX&mobile=+91$phone&message=Your OTP is&senderid=POUSSE&accusage=1";
    // $endpoint = "http://mobicomm.dove-sms.com//submitsms.jsp?user=PousseM&key=fc53bf6154XX&mobile=+91$phone&message=$name%0A$date%0ALogout Time: $time%0ALocation: $address%0ABTRY: $battery %&senderid=POUSSE&accusage=1";
    $endpoint = "http://mobicomm.dove-sms.com//submitsms.jsp?user=PousseM&key=fc53bf6154XX&mobile=+91$phone&message=$name%0A$date%0ATime: $time%0ALocation: $address%0ABTRY: $battery %&senderid=POUSSE&accusage=1";
    $client = new \GuzzleHttp\Client();
    $client->request('GET', $endpoint);
  }

  // User Attendance Month Wise WithOut Pagination
  public function monthly_attendances(Request $request)
  {
    $analysis = [];
    $userAttendances = request()->company->user_attendances();

    if ($request->month) {
      $userAttendances = $userAttendances->whereMonth('date', '=', $request->month);
    }
    if ($request->year) {
      $userAttendances = $userAttendances->whereYear('date', '=', $request->year);
    }
    if ($request->user_id) {
      $userAttendances = $userAttendances->where('user_id', '=', $request->user_id);
    }
    $supervisorId = request()->superVisor_id;
    if ($supervisorId != '')
      $userAttendances = $userAttendances->whereHas('user',  function ($q) use ($supervisorId) {
        $q->where('supervisor_id', '=', $supervisorId);
      });

    $userAttendances = $userAttendances->get();

    $users = [];
    $user_id_log = [];

    foreach ($userAttendances as $key => $attendance) {
      $present_count = 0;
      $weekly_off_count = 0;
      $leave_count = 0;
      $user = $attendance->user->toArray();
      $user_id = $user['id'];
      unset($attendance['user']);
      $user_key = array_search($user_id, array_column($users, 'id'));
      $date = Carbon::parse($attendance->date)->format('j');

      $is_exist = in_array($user_id, $user_id_log);
      if (!$user_key && !$is_exist) {
        $user_id_log[] = $user_id;
        $day_count = 1;
        switch ($attendance->session_type) {
          case 'PRESENT':
            $present_count++;
            break;
          case 'WEEKLY OFF':
            $weekly_off_count++;
            break;
          case 'LEAVE':
            $leave_count++;
            break;

          default:
            break;
        }
        $user['day_count'] = $day_count;
        $user['present_count'] = $present_count;
        $user['weekly_off_count'] = $weekly_off_count;
        $user['leave_count'] = $leave_count;
        $user['attendances'][$date] = $attendance;
        $users[] = $user;
      } else {
        switch ($attendance->session_type) {
          case 'PRESENT':
            $users[$user_key]["present_count"]++;
            break;
          case 'WEEKLY OFF':
            $users[$user_key]['weekly_off_count']++;
            break;
          case 'LEAVE':
            $users[$user_key]['leave_count']++;
            break;

          default:
            #code...
            break;
        }

        $day_count = sizeof($users[$user_key]["attendances"]) + 1;
        $users[$user_key]["attendances"][$date] = $attendance;
        $users[$user_key]['day_count'] = $day_count;
        // $users[$user_key]['present_count'] = $present_count;
      }
    }
    // return $users;
    return response()->json([
      'data'     =>  $users,
      'success' =>  true
    ], 200);
  }
  // User Attendance Month Wise
  public function monthly_attendances1(Request $request)
  {
    $analysis = [];
    $userAttendances = request()->company->user_attendances();

    if ($request->month) {
      $userAttendances = $userAttendances->whereMonth('date', '=', $request->month);
    }
    if ($request->year) {
      $userAttendances = $userAttendances->whereYear('date', '=', $request->year);
    }
    if ($request->user_id) {
      $userAttendances = $userAttendances->where('user_id', '=', $request->user_id);
    }
    $supervisorId = request()->superVisor_id;
    if ($supervisorId != '')
      $userAttendances = $userAttendances->whereHas('user',  function ($q) use ($supervisorId) {
        $q->where('supervisor_id', '=', $supervisorId);
      });

    if (request()->page && request()->rowsPerPage) {
      $count = $userAttendances->count();
      $userAttendances = $userAttendances->paginate(request()->rowsPerPage)->toArray();
      $userAttendances = $userAttendances['data'];
    } else {
      $userAttendances = $userAttendances->get();
      $count = $userAttendances->count();
    }
    $users = [];
    $user_id_log = [];

    foreach ($userAttendances as $key => $attendance) {
      $present_count = 0;
      $weekly_off_count = 0;
      $leave_count = 0;
      $user = $attendance['user'];
      $user_id = $user['id'];
      unset($attendance['user']);
      $user_key = array_search($user_id, array_column($users, 'id'));
      $date = Carbon::parse($attendance['date'])->format('j');

      $is_exist = in_array($user_id, $user_id_log);
      if (!$user_key && !$is_exist) {
        $user_id_log[] = $user_id;
        $day_count = 1;
        switch ($attendance['session_type']) {
          case 'PRESENT':
            $present_count++;
            break;
          case 'WEEKLY OFF':
            $weekly_off_count++;
            break;
          case 'LEAVE':
            $leave_count++;
            break;

          default:
            break;
        }
        $user['day_count'] = $day_count;
        $user['present_count'] = $present_count;
        $user['weekly_off_count'] = $weekly_off_count;
        $user['leave_count'] = $leave_count;
        $user['attendances'][$date] = $attendance;
        $users[] = $user;
      } else {
        switch ($attendance['session_type']) {
          case 'PRESENT':
            $users[$user_key]["present_count"]++;
            break;
          case 'WEEKLY OFF':
            $users[$user_key]['weekly_off_count']++;
            break;
          case 'LEAVE':
            $users[$user_key]['leave_count']++;
            break;

          default:
            #code...
            break;
        }

        $day_count = sizeof($users[$user_key]["attendances"]) + 1;
        $users[$user_key]["attendances"][$date] = $attendance;
        $users[$user_key]['day_count'] = $day_count;
        // $users[$user_key]['present_count'] = $present_count;
      }
    }
    // return $users;
    return response()->json([
      'data'     =>  $users,
      'count' => $count,
      'success' =>  true
    ], 200);
  }
  // Defaulter report WithOut Pagination
  public function defaulters(Request $request)
  {
    $analysis = [];
    $userAttendances = request()->company->user_attendances();
    if ($request->user_id) {
      $userAttendances = $userAttendances->where('user_id', '=', $request->user_id);
    }
    if ($request->month) {
      $currentMonth = Carbon::now()->format('m');

      $daysInMonth = Carbon::now()->month($request->month)->daysInMonth;
      if ($request->month == $currentMonth) {
        $daysInMonth = Carbon::now()->format('d');
      }
      $userAttendances = $userAttendances->whereMonth('date', '=', $request->month);
    }
    if ($request->year) {
      $userAttendances = $userAttendances->whereYear('date', '=', $request->year);
    }
    if ($request->session_type && $request->session_type != "LEAVE") {
      $userAttendances = $userAttendances->where('session_type', '=', $request->session_type);
    }
    $supervisorId = request()->superVisor_id;
    if ($supervisorId != '')
      $userAttendances = $userAttendances->whereHas('user',  function ($q) use ($supervisorId) {
        $q->where('supervisor_id', '=', $supervisorId);
      });
    $userAttendances = $userAttendances->get();

    $users = [];
    $user_id_log = [];
    $defaulters = [];
    $absent_count = 0;
    foreach ($userAttendances as $key => $attendance) {
      $present_count = 0;
      $weekly_off_count = 0;
      $leave_count = 0;
      $diff = 0;
      $user = $attendance->user->toArray();
      unset($attendance['user']);
      $user_id = $user['id'];
      $is_defaulter = 0;
      $defaulter_user_key = '';

      $user_key = array_search($user_id, array_column($users, 'id'));
      $date = Carbon::parse($attendance->date)->format('j');

      $is_exist = in_array($user_id, $user_id_log);
      if (!$user_key && !$is_exist) {
        $user_id_log[] = $user_id;
        $absent_count = 0;
        $day_count = 1;
        switch ($attendance->session_type) {
          case 'PRESENT':
            $present_count++;
            break;
          case 'WEEKLY OFF':
            $weekly_off_count++;
            break;
          case 'LEAVE':
            $leave_count++;
            break;

          default:
            break;
        }
        $user['day_count'] = $day_count;
        $user['present_count'] = $present_count;
        $user['weekly_off_count'] = $weekly_off_count;
        $user['leave_count'] = $leave_count;
        $user['absent_count'] = $absent_count;
        $user['attendances'][$date] = $attendance;
        $user['is_defaulter'] = $is_defaulter;
        $is_defaulter = 0;

        $users[] = $user;
        $defaulters[] = $user;
      } else {
        $previous_log = end($users[$user_key]['attendances']);
        $previous_date = Carbon::parse($previous_log['date'])->format('j');
        $diff = $date - $previous_date;
        switch ($attendance->session_type) {
          case 'PRESENT':
            $users[$user_key]["present_count"]++;
            break;
          case 'WEEKLY OFF':
            $users[$user_key]['weekly_off_count']++;
            break;
          case 'LEAVE':
            $users[$user_key]['leave_count']++;
            break;

          default:
            #code...
            break;
        }
        $day_count = sizeof($users[$user_key]["attendances"]) + 1;
        $users[$user_key]["attendances"][$date] = $attendance;
        $users[$user_key]['day_count'] = $day_count;
        $absent_count = $daysInMonth - $day_count;
        $users[$user_key]['absent_count'] = $absent_count;

        $defaulter_user_key = array_search($user_id, array_column($defaulters, 'id'));
        if ($request->session_type == "LEAVE" && ($absent_count + $users[$user_key]['leave_count']) >= 2) {
          $is_defaulter = 1;
          if ($previous_log && empty($defaulters[$defaulter_user_key]["attendances"][$previous_date])) {
            $defaulters[$defaulter_user_key]["attendances"][$previous_date] = $previous_log;
          }
          $defaulters[$defaulter_user_key]["attendances"][$date] = $attendance;
        } else {
          if ($request->session_type != "LEAVE" && $diff == 1) {
            $is_defaulter = 1;
            if ($previous_log && empty($defaulters[$defaulter_user_key]["attendances"][$previous_date])) {
              $defaulters[$defaulter_user_key]["attendances"][$previous_date] = $previous_log;
            }
            $defaulters[$defaulter_user_key]["attendances"][$date] = $attendance;
          }
        }
        $defaulters[$defaulter_user_key]["is_defaulter"] = $is_defaulter;
        $defaulters[$defaulter_user_key]["present_count"] = $users[$user_key]["present_count"];
        $defaulters[$defaulter_user_key]["leave_count"] = $users[$user_key]["leave_count"];
        $defaulters[$defaulter_user_key]["absent_count"] = $users[$user_key]["absent_count"];
        $defaulters[$defaulter_user_key]["weekly_off_count"] = $users[$user_key]["weekly_off_count"];
      }
    }
    $D = [];
    foreach ($defaulters as $key => $user) {
      if ($user['is_defaulter'] == 1) {
        $D[] = $user;
      }
    }


    return response()->json([
      'data'     =>  $D,
      'success' =>  true
    ], 200);
  }
  // Defaulter report
  public function defaulters1(Request $request)
  {
    $analysis = [];
    $userAttendances = request()->company->user_attendances();
    if ($request->user_id) {
      $userAttendances = $userAttendances->where('user_id', '=', $request->user_id);
    }
    if ($request->month) {
      $userAttendances = $userAttendances->whereMonth('date', '=', $request->month);
    }
    if ($request->year) {
      $userAttendances = $userAttendances->whereYear('date', '=', $request->year);
    }
    if ($request->session_type) {
      $userAttendances = $userAttendances->where('session_type', '=', $request->session_type);
    }
    $supervisorId = request()->superVisor_id;
    if ($supervisorId != '')
      $userAttendances = $userAttendances->whereHas('user',  function ($q) use ($supervisorId) {
        $q->where('supervisor_id', '=', $supervisorId);
      });

    if (request()->page && request()->rowsPerPage) {
      $count = $userAttendances->count();
      $userAttendances = $userAttendances->paginate(request()->rowsPerPage)->toArray();
      $userAttendances = $userAttendances['data'];
    } else {
      $userAttendances = $userAttendances->get();
      $count = $userAttendances->count();
    }
    $users = [];
    $user_id_log = [];
    $defaulters = [];
    foreach ($userAttendances as $key => $attendance) {
      $present_count = 0;
      $weekly_off_count = 0;
      $leave_count = 0;
      $diff = 0;
      $user = $attendance['user'];
      $user_id = $user['id'];
      unset($attendance['user']);
      $is_defaulter = 0;
      $defaulter_user_key = '';

      $user_key = array_search($user_id, array_column($users, 'id'));
      $date = Carbon::parse($attendance['date'])->format('j');

      $is_exist = in_array($user_id, $user_id_log);
      if (!$user_key && !$is_exist) {
        $user_id_log[] = $user_id;
        $day_count = 1;
        switch ($attendance['session_type']) {
          case 'PRESENT':
            $present_count++;
            break;
          case 'WEEKLY OFF':
            $weekly_off_count++;
            break;
          case 'LEAVE':
            $leave_count++;
            break;

          default:
            break;
        }
        $user['day_count'] = $day_count;
        $user['present_count'] = $present_count;
        $user['weekly_off_count'] = $weekly_off_count;
        $user['leave_count'] = $leave_count;
        $user['attendances'][$date] = $attendance;
        $user['is_defaulter'] = $is_defaulter;
        $is_defaulter = 0;

        $users[] = $user;
        $defaulters[] = $user;
      } else {
        $previous_log = end($users[$user_key]['attendances']);
        $previous_date = Carbon::parse($previous_log['date'])->format('j');
        $diff = $date - $previous_date;
        switch ($attendance['session_type']) {
          case 'PRESENT':
            $users[$user_key]["present_count"]++;
            break;
          case 'WEEKLY OFF':
            $users[$user_key]['weekly_off_count']++;
            break;
          case 'LEAVE':
            $users[$user_key]['leave_count']++;
            break;

          default:
            #code...
            break;
        }
        $day_count = sizeof($users[$user_key]["attendances"]) + 1;
        $users[$user_key]["attendances"][$date] = $attendance;
        $users[$user_key]['day_count'] = $day_count;
        $defaulter_user_key = array_search($user_id, array_column($defaulters, 'id'));
        if ($diff == 1) {
          $is_defaulter = 1;
          if ($previous_log && empty($defaulters[$defaulter_user_key]["attendances"][$previous_date])) {
            $defaulters[$defaulter_user_key]["attendances"][$previous_date] = $previous_log;
          }
          $defaulters[$defaulter_user_key]["attendances"][$date] = $attendance;
        }
        if ($key > 10) {
          return $defaulters;
        }
        $defaulters[$defaulter_user_key]["is_defaulter"] = $is_defaulter;
        $defaulters[$defaulter_user_key]["present_count"] = $users[$user_key]["present_count"];
        $defaulters[$defaulter_user_key]["leave_count"] = $users[$user_key]["leave_count"];
        $defaulters[$defaulter_user_key]["weekly_off_count"] = $users[$user_key]["weekly_off_count"];
      }
    }
    return $defaulters;
    $D = [];
    foreach ($defaulters as $key => $user) {
      if ($user['is_defaulter'] == 1) {
        $D[] = $user;
      }
    }


    return response()->json([
      'data'     =>  $D,
      'count' => $count,
      'success' =>  true
    ], 200);
  }
}
