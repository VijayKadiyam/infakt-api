<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\UserAttendance;
use App\UserLocation;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\UserAttendanceMail;
use App\Order;

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
            $orders = Order::whereYear('created_at', Carbon::now())
              ->whereDate('created_at', Carbon::now())
              ->where('distributor_id', '=', $userAttendance->user->distributor_id)
              ->get();
            $check = true;
            array_unshift($userAttendanceData, [
              'store_name'  =>  $userAttendance->user->name ?? '-',
              'ba_name'     =>  $supervisorUser->ba_name ?? '-',
              'present'     =>  'YES',
              'date'        =>  Carbon::parse($request->date)->format('d-m-Y'),
              'time'        => ($userAttendance->login_time ? Carbon::parse($userAttendance->login_time)->format('H:i')  : '') . ' - ' . ($userAttendance->logout_time ? Carbon::parse($userAttendance->logout_time)->format('H:i') : ''),
              'orders_count'  =>  sizeof($orders),
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
            'orders_count'  =>  0
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
      $meeting_count = 0;
      $market_closed_count = 0;
      $half_day_count = 0;
      $holiday_count = 0;
      $work_from_home_count = 0;
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
          case 'MEETING':
            $meeting_count++;
            break;
          case 'MARKET CLOSED':
            $market_closed_count++;
            break;
          case 'HALF DAY':
            $half_day_count++;
            break;
          case 'HOLIDAY':
            $holiday_count++;
            break;
          case 'WORK FROM HOME':
            $work_from_home_count++;
            break;
          default:
            break;
        }
        $user['day_count'] = $day_count;
        $user['present_count'] = $present_count;
        $user['weekly_off_count'] = $weekly_off_count;
        $user['leave_count'] = $leave_count;
        $user['meeting_count'] = $meeting_count;
        $user['market_closed_count'] = $market_closed_count;
        $user['half_day_count'] = $half_day_count;
        $user['holiday_count'] = $holiday_count;
        $user['work_from_home_count'] = $work_from_home_count;
        $user['attendances'][$date] = $attendance;
        $users[] = $user;
      } else {
        if (!isset($users[$user_key]["attendances"][$date])) {

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
            case 'MEETING':
              $users[$user_key]['meeting_count']++;
              break;
            case 'MARKET CLOSED':
              $users[$user_key]['market_closed_count']++;
              break;
            case 'HALF DAY':
              $users[$user_key]['half_day_count']++;
              break;
            case 'HOLIDAY':
              $users[$user_key]['holiday_count']++;
              break;
            case 'WORK FROM HOME':
              $users[$user_key]['work_from_home_count']++;
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
      $meeting_count = 0;
      $market_closed_count = 0;
      $half_day_count = 0;
      $holiday_count = 0;
      $work_from_home_count = 0;
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
          case 'MEETING':
            $meeting_count++;
            break;
          case 'MARKET CLOSED':
            $market_closed_count++;
            break;
          case 'HALF DAY':
            $half_day_count++;
            break;
          case 'HOLIDAY':
            $holiday_count++;
            break;
          case 'WORK FROM HOME':
            $work_from_home_count++;
            break;
          default:
            break;
        }
        $user['day_count'] = $day_count;
        $user['present_count'] = $present_count;
        $user['weekly_off_count'] = $weekly_off_count;
        $user['leave_count'] = $leave_count;
        $user['meeting_count'] = $meeting_count;
        $user['market_closed_count'] = $market_closed_count;
        $user['half_day_count'] = $half_day_count;
        $user['holiday_count'] = $holiday_count;
        $user['work_from_home_count'] = $work_from_home_count;
        $user['absent_count'] = $absent_count;
        $user['attendances'][$date] = $attendance;
        $user['is_defaulter'] = $is_defaulter;
        $is_defaulter = 0;

        $users[] = $user;
        $defaulters[] = $user;
      } else {
        if (!isset($users[$user_key]["attendances"][$date])) {
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
            case 'MEETING':
              $users[$user_key]['meeting_count']++;
              break;
            case 'MARKET CLOSED':
              $users[$user_key]['market_closed_count']++;
              break;
            case 'HALF DAY':
              $users[$user_key]['half_day_count']++;
              break;
            case 'HOLIDAY':
              $users[$user_key]['holiday_count']++;
              break;
            case 'WORK FROM HOME':
              $users[$user_key]['work_from_home_count']++;
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
          $defaulters[$defaulter_user_key]["meeting_count"] = $users[$user_key]["meeting_count"];
          $defaulters[$defaulter_user_key]["market_closed_count"] = $users[$user_key]["market_closed_count"];
          $defaulters[$defaulter_user_key]["half_day_count"] = $users[$user_key]["half_day_count"];
          $defaulters[$defaulter_user_key]["holiday_count"] = $users[$user_key]["holiday_count"];
          $defaulters[$defaulter_user_key]["work_from_home_count"] = $users[$user_key]["work_from_home_count"];
          $defaulters[$defaulter_user_key]["absent_count"] = $users[$user_key]["absent_count"];
          $defaulters[$defaulter_user_key]["weekly_off_count"] = $users[$user_key]["weekly_off_count"];
        }
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
  // GT Channel Attendance
  public function gt_attendances(Request $request)
  {
    ini_set('max_execution_time', 0);
    ini_set('memory_limit', -1);
    $now = Carbon::now()->format('Y-m-d');
    $month =  Carbon::parse($now)->format('m');
    $year =  Carbon::parse($now)->format('Y');
    $users = request()->company->users();
    if ($request->channel) {
      $users = $users
        ->where('channel', 'LIKE', '%' . $request->channel . '%');
    }
    $users = $users->get();
    // return $users;
    $userAttendances = request()->company->user_attendances();

    $channel = $request->channel;
    if ($channel) {
      $userAttendances = $userAttendances->whereHas('user',  function ($q) use ($channel) {
        $q->where('channel', '=', $channel);
      });
    }
    $userAttendances = $userAttendances->where('date', $now);

    $userAttendances = $userAttendances->get();
    $count = $userAttendances->count();
    $userAttendances = $userAttendances->toArray();
    $gt_attandances = [];
    $total_North_App_id = 0;
    $total_South_App_id = 0;
    $total_East_App_id = 0;
    $total_West_App_id = 0;

    $Active_North_Ba_Count = 0;
    $Active_South_Ba_Count = 0;
    $Active_East_Ba_Count = 0;
    $Active_West_Ba_Count = 0;

    $Gap_North_Ba_Count = 0;
    $Gap_South_Ba_Count = 0;
    $Gap_East_Ba_Count = 0;
    $Gap_West_Ba_Count = 0;

    $Absent_North_Ba_Count = 0;
    $Absent_South_Ba_Count = 0;
    $Absent_East_Ba_Count = 0;
    $Absent_West_Ba_Count = 0;
    $Absent_users = [];
    $Gap_users = [];
    foreach ($users as $key => $user) {
      $region = str_replace(" ", "", $user['region']);
      $attendance_key = array_search($user->id, array_column($userAttendances, 'user_id'));
      $User_Attendances = $attendance_key !== false ? $userAttendances[$attendance_key] : "Not Found";
      $user['User_Attendances'] = $User_Attendances;
      if ($attendance_key === false) {
        // Find Entire Month
        $current_month_attendances = $request->company->user_attendances()
          ->whereMonth('date', $month)
          ->whereYear('date', $year)
          ->where('user_id', $user['id'])
          ->get();
        if (empty($current_month_attendances)) {
          // Haven't Punched Once In this Month
          $user['Gap_tag'] = true;
          $Gap_users[] = $user;
        } else {
          // Absent Only Today
          $user['Absent_tag'] = true;
          $Absent_users[] = $user['id'];
        }
      } else {
        $user['Gap_tag'] = false;
        $user['Absent_tag'] = false;
      }
      switch ($region) {
        case 'North':
          $total_North_App_id++;
          if ($user['active'] == true) {
            $Active_North_Ba_Count++;
          }
          if ($user['Gap_tag'] == true) {
            $Gap_North_Ba_Count++;
          }
          if ($user['Absent_tag'] == true) {
            $Absent_North_Ba_Count++;
          }
          break;
        case 'South':
          $total_South_App_id++;
          if ($user['active'] == true) {
            $Active_South_Ba_Count++;
          }
          if ($user['Gap_tag'] == true) {
            $Gap_South_Ba_Count++;
          }
          if ($user['Absent_tag'] == true) {
            $Absent_South_Ba_Count++;
          }
          break;
        case 'East':
          $total_East_App_id++;
          if ($user['active'] == true) {
            $Active_East_Ba_Count++;
          }
          if ($user['Gap_tag'] == true) {
            $Gap_East_Ba_Count++;
          }
          if ($user['Absent_tag'] == true) {
            $Absent_East_Ba_Count++;
          }
          break;
        case 'West':
          $total_West_App_id++;
          if ($user['active'] == true) {
            $Active_West_Ba_Count++;
          }
          if ($user['Gap_tag'] == true) {
            $Gap_West_Ba_Count++;
          }
          if ($user['Absent_tag'] == true) {
            $Absent_West_Ba_Count++;
          }
          break;

        default:
          # code...
          break;
      }
    }

    $North_present_count = 0;
    $North_weekly_off_count = 0;
    $North_leave_count = 0;
    $North_meeting_count = 0;
    $North_market_closed_count = 0;
    $North_half_day_count = 0;
    $North_holiday_count = 0;
    $North_work_from_home_count = 0;

    $South_present_count = 0;
    $South_weekly_off_count = 0;
    $South_leave_count = 0;
    $South_meeting_count = 0;
    $South_market_closed_count = 0;
    $South_half_day_count = 0;
    $South_holiday_count = 0;
    $South_work_from_home_count = 0;

    $East_present_count = 0;
    $East_weekly_off_count = 0;
    $East_leave_count = 0;
    $East_meeting_count = 0;
    $East_market_closed_count = 0;
    $East_half_day_count = 0;
    $East_holiday_count = 0;
    $East_work_from_home_count = 0;

    $West_present_count = 0;
    $West_weekly_off_count = 0;
    $West_leave_count = 0;
    $West_meeting_count = 0;
    $West_market_closed_count = 0;
    $West_half_day_count = 0;
    $West_holiday_count = 0;
    $West_work_from_home_count = 0;

    $Absent_users = [];
    foreach ($userAttendances as $key => $attendance) {
      $user = $attendance['user'];
      $region = str_replace(" ", "", $user['region']);

      switch ($region) {
        case 'North':

          switch ($attendance['session_type']) {
            case 'PRESENT':
              $North_present_count++;
              break;
            case 'WEEKLY OFF':
              $North_weekly_off_count++;
              break;
            case 'LEAVE':
              $North_leave_count++;
              break;
            case 'MEETING':
              $North_meeting_count++;
              break;
            case 'MARKET CLOSED':
              $North_market_closed_count++;
              break;
            case 'HALF DAY':
              $North_half_day_count++;
              break;
            case 'HOLIDAY':
              $North_holiday_count++;
              break;
            case 'WORK FROM HOME':
              $North_work_from_home_count++;
              break;
            default:
              break;
          }
          // $North_Present = $attendance['session_type'] == 'PRESENT' ? $North_Present + 1 : $North_Present;
          break;
        case 'South':

          switch ($attendance['session_type']) {
            case 'PRESENT':
              $South_present_count++;
              break;
            case 'WEEKLY OFF':
              $South_weekly_off_count++;
              break;
            case 'LEAVE':
              $South_leave_count++;
              break;
            case 'MEETING':
              $South_meeting_count++;
              break;
            case 'MARKET CLOSED':
              $South_market_closed_count++;
              break;
            case 'HALF DAY':
              $South_half_day_count++;
              break;
            case 'HOLIDAY':
              $South_holiday_count++;
              break;
            case 'WORK FROM HOME':
              $South_work_from_home_count++;
              break;
            default:
              break;
          }
          break;
        case 'East':

          switch ($attendance['session_type']) {
            case 'PRESENT':
              $East_present_count++;
              break;
            case 'WEEKLY OFF':
              $East_weekly_off_count++;
              break;
            case 'LEAVE':
              $East_leave_count++;
              break;
            case 'MEETING':
              $East_meeting_count++;
              break;
            case 'MARKET CLOSED':
              $East_market_closed_count++;
              break;
            case 'HALF DAY':
              $East_half_day_count++;
              break;
            case 'HOLIDAY':
              $East_holiday_count++;
              break;
            case 'WORK FROM HOME':
              $East_work_from_home_count++;
              break;
            default:
              break;
          }
          break;
        case 'West':

          switch ($attendance['session_type']) {
            case 'PRESENT':
              $West_present_count++;
              break;
            case 'WEEKLY OFF':
              $West_weekly_off_count++;
              break;
            case 'LEAVE':
              $West_leave_count++;
              break;
            case 'MEETING':
              $West_meeting_count++;
              break;
            case 'MARKET CLOSED':
              $West_market_closed_count++;
              break;
            case 'HALF DAY':
              $West_half_day_count++;
              break;
            case 'HOLIDAY':
              $West_holiday_count++;
              break;
            case 'WORK FROM HOME':
              $West_work_from_home_count++;
              break;
            default:
              break;
          }
          break;

        default:
          # code...
          break;
      }
    }

    $gt_attandances['North'] = [
      'total_App_id' => $total_North_App_id,
      'Active_Ba_Count' => $Active_North_Ba_Count,
      'present_count' => $North_present_count,
      'weekly_off_count' => $North_weekly_off_count,
      'leave_count' => $North_leave_count,
      'meeting_count' => $North_meeting_count,
      'market_closed_count' => $North_market_closed_count,
      'half_day_count' => $North_half_day_count,
      'holiday_count' => $North_holiday_count,
      'work_from_home_count' => $North_work_from_home_count,
      'gap' => $Active_North_Ba_Count - ($North_present_count
        + $North_weekly_off_count
        + $North_leave_count
        + $North_meeting_count
        + $North_market_closed_count
        + $North_half_day_count
        + $North_holiday_count
        + $North_work_from_home_count),
      'gap_count' => $Gap_North_Ba_Count,
      'absent_count' => $Absent_North_Ba_Count,
    ];
    $gt_attandances['South'] = [
      'total_App_id' => $total_South_App_id,
      'Active_Ba_Count' => $Active_South_Ba_Count,
      'present_count' => $South_present_count,
      'weekly_off_count' => $South_weekly_off_count,
      'leave_count' => $South_leave_count,
      'meeting_count' => $South_meeting_count,
      'market_closed_count' => $South_market_closed_count,
      'half_day_count' => $South_half_day_count,
      'holiday_count' => $South_holiday_count,
      'work_from_home_count' => $South_work_from_home_count,
      'gap' => $Active_South_Ba_Count - ($South_present_count
        + $South_weekly_off_count
        + $South_leave_count
        + $South_meeting_count
        + $South_market_closed_count
        + $South_half_day_count
        + $South_holiday_count
        + $South_work_from_home_count),
      'gap_count' => $Gap_South_Ba_Count,
      'absent_count' => $Absent_South_Ba_Count,

    ];
    $gt_attandances['East'] = [
      'total_App_id' => $total_East_App_id,
      'Active_Ba_Count' => $Active_East_Ba_Count,
      'present_count' => $East_present_count,
      'weekly_off_count' => $East_weekly_off_count,
      'leave_count' => $East_leave_count,
      'meeting_count' => $East_meeting_count,
      'market_closed_count' => $East_market_closed_count,
      'half_day_count' => $East_half_day_count,
      'holiday_count' => $East_holiday_count,
      'work_from_home_count' => $East_work_from_home_count,
      'gap' => $Active_East_Ba_Count - ($East_present_count
        + $East_weekly_off_count
        + $East_leave_count
        + $East_meeting_count
        + $East_market_closed_count
        + $East_half_day_count
        + $East_holiday_count
        + $East_work_from_home_count),
      'gap_count' => $Gap_East_Ba_Count,
      'absent_count' => $Absent_East_Ba_Count,
    ];
    $gt_attandances['West'] = [
      'total_App_id' => $total_West_App_id,
      'Active_Ba_Count' => $Active_West_Ba_Count,
      'present_count' => $West_present_count,
      'weekly_off_count' => $West_weekly_off_count,
      'leave_count' => $West_leave_count,
      'meeting_count' => $West_meeting_count,
      'market_closed_count' => $West_market_closed_count,
      'half_day_count' => $West_half_day_count,
      'holiday_count' => $West_holiday_count,
      'work_from_home_count' => $West_work_from_home_count,
      'gap' => $Active_West_Ba_Count - ($West_present_count
        + $West_weekly_off_count
        + $West_leave_count
        + $West_meeting_count
        + $West_market_closed_count
        + $West_half_day_count
        + $West_holiday_count
        + $West_work_from_home_count),
      'gap_count' => $Gap_West_Ba_Count,
      'absent_count' => $Absent_West_Ba_Count,
    ];

    return response()->json([
      'data'     =>  $gt_attandances,
      'count' => $count,
      'success' =>  true
    ], 200);
  }
  // Other Channel Attendance
  public function other_channel_attendances(Request $request)
  {
    ini_set('max_execution_time', 0);
    ini_set('memory_limit', -1);
    $now = Carbon::now()->format('Y-m-d');
    $month =  Carbon::parse($now)->format('m');
    $year =  Carbon::parse($now)->format('Y');
    $users = request()->company->users();
    if ($request->channel) {
      $users = $users
        ->where('channel', '=', $request->channel);
    }
    $users = $users->get();
    $userAttendances = request()->company->user_attendances();

    $channel = $request->channel;
    if ($channel) {
      $userAttendances = $userAttendances->whereHas('user',  function ($q) use ($channel) {
        $q->where('channel', '=', $channel);
      });
    }
    $userAttendances = $userAttendances->where('date', $now);

    $userAttendances = $userAttendances->get();
    $count = $userAttendances->count();

    $userAttendances = $userAttendances->toArray();
    // return $userAttendances;
    $other_channel_attandances = [];
    $Channel_Chains = [];
    $Absent_users = [];
    $Gap_users = [];
    foreach ($users as $key => $user) {
      $chain_name = strtoupper(str_replace(" ", "", $user['chain_name']));
      $total_name = 'total_' . $chain_name . '_App_id';
      $Active_name = 'Active_' . $chain_name . '_Ba_Count';
      $Gap_name = 'Gap_' . $chain_name . '_Ba_Count';
      $Absent_name = 'Absent_' . $chain_name . '_Ba_Count';

      $attendance_key = array_search($user->id, array_column($userAttendances, 'user_id'));
      $User_Attendances = $attendance_key !== false ? $userAttendances[$attendance_key] : "Not Found";
      $user['User_Attendances'] = $User_Attendances;
      if ($attendance_key === false) {
        // Find Entire Month
        $current_month_attendances = $request->company->user_attendances()
          ->whereMonth('date', $month)
          ->whereYear('date', $year)
          ->where('user_id', $user['id'])
          ->get();
        if (empty($current_month_attendances)) {
          // Haven't Punched Once In this Month
          $user['Gap_tag'] = true;
          $Gap_users[] = $user;
        } else {
          // Absent Only Today
          $user['Absent_tag'] = true;
          $Absent_users[] = $user['id'];
        }
      } else {
        $user['Gap_tag'] = false;
        $user['Absent_tag'] = false;
      }
      if (!in_array($chain_name, $Channel_Chains)) {
        $Channel_Chains[] = $chain_name;
        $$total_name = 1;
        $$Active_name = 1;
        if ($user['Gap_tag'] == true) {
          $$Gap_name = 1;
        } else {
          $$Gap_name = 0;
        }
        if ($user['Absent_tag'] == true) {
          $$Absent_name = 1;
        } else {
          $$Absent_name = 0;
        }
      } else {
        $$total_name = $$total_name + 1;
        $$Active_name = $$Active_name + 1;
        if ($user['Gap_tag'] == true) {
          $$Gap_name++;
        }
        if ($user['Absent_tag'] == true) {
          $$Absent_name++;
        }
      }
      // $Total[$total_name] = $$total_name;
      // $Active[$Active_name] = $$Active_name;
    }
    // return $Total;
    foreach ($userAttendances as $key => $attendance) {
      $user = $attendance['user'];
      $chain_name = strtoupper(str_replace(" ", "", $user['chain_name']));

      foreach ($Channel_Chains as $key => $chain) {
        if ($chain == $chain_name) {
          // chain=H&G
          $present_name = $chain . '_present_count';
          // present_name= H&G_present_count
          if (!isset($$present_name)) {
            $$present_name =  0;
          }
          $weekly_off_name = $chain . '_weekly_off_count';
          // weekly_off_name= H&G_weekly_off_count
          if (!isset($$weekly_off_name)) {
            $$weekly_off_name =  0;
          }
          $leave_name = $chain . '_leave_count';
          // leave_name= H&G_leave_count
          if (!isset($$leave_name)) {
            $$leave_name =  0;
          }
          $meeting_name = $chain . '_meeting_count';
          // meeting_name= H&G_meeting_count
          if (!isset($$meeting_name)) {
            $$meeting_name =  0;
          }
          $market_closed_name = $chain . '_market_closed_count';
          // market_closed_name= H&G_market_closed_count
          if (!isset($$market_closed_name)) {
            $$market_closed_name =  0;
          }
          $half_day_name = $chain . '_half_day_count';
          // half_day_name= H&G_half_day_count
          if (!isset($$half_day_name)) {
            $$half_day_name =  0;
          }
          $holiday_name = $chain . '_holiday_count';
          // holiday_name= H&G_holiday_count
          if (!isset($$holiday_name)) {
            $$holiday_name =  0;
          }
          $work_from_home_name = $chain . '_work_from_home_count';
          // work_from_home_name= H&G_work_from_home_count
          if (!isset($$work_from_home_name)) {
            $$work_from_home_name =  0;
          }
          $absent_name = $chain . '_absent_count';
          // absent_name= H&G_absent_count
          if (!isset($$absent_name)) {
            $$absent_name =  0;
          }
          switch ($attendance['session_type']) {
            case 'PRESENT':
              $$present_name = $$present_name ? $$present_name + 1 : 1;
              break;
            case 'WEEKLY OFF':
              $$weekly_off_name = $$weekly_off_name ? $$weekly_off_name + 1 : 1;
              // $North_weekly_off_count++;
              break;
            case 'LEAVE':
              // $North_leave_count++;
              $$leave_name = $$leave_name ? $$leave_name + 1 : 1;
              break;
            case 'MEETING':
              $$meeting_name = $$meeting_name ? $$meeting_name + 1 : 1;
              // $North_meeting_count++;
              break;
            case 'MARKET CLOSED':
              $$market_closed_name = $$market_closed_name ? $$market_closed_name + 1 : 1;
              // $North_market_closed_count++;
              break;
            case 'HALF DAY':
              $$half_day_name = $$half_day_name ? $$half_day_name + 1 : 1;
              // $North_half_day_count++;
              break;
            case 'HOLIDAY':
              $$holiday_name = $$holiday_name ? $$holiday_name + 1 : 1;
              // $North_holiday_count++;
              break;
            case 'WORK FROM HOME':
              $$work_from_home_name = $$work_from_home_name ? $$work_from_home_name + 1 : 1;
              // $North_work_from_home_count++;
              break;
            default:
              $$absent_name = $$absent_name ? $$absent_name + 1 : 1;
              break;
          }
        }
      }
    }
    // return $$present_name;

    foreach ($Channel_Chains as $key => $chain) {
      $total_name = 'total_' . $chain . '_App_id';
      $Active_name = 'Active_' . $chain . '_Ba_Count';
      $Gap_name = 'Gap_' . $chain . '_Ba_Count';
      $Absent_name = 'Absent_' . $chain . '_Ba_Count';
      $present_name = $chain . '_present_count';
      // present_name= H&G_present_count
      if (!isset($$present_name)) {
        $$present_name =  0;
      }
      $weekly_off_name = $chain . '_weekly_off_count';
      // weekly_off_name= H&G_weekly_off_count
      if (!isset($$weekly_off_name)) {
        $$weekly_off_name =  0;
      }
      $leave_name = $chain . '_leave_count';
      // leave_name= H&G_leave_count
      if (!isset($$leave_name)) {
        $$leave_name =  0;
      }
      $meeting_name = $chain . '_meeting_count';
      // meeting_name= H&G_meeting_count
      if (!isset($$meeting_name)) {
        $$meeting_name =  0;
      }
      $market_closed_name = $chain . '_market_closed_count';
      // market_closed_name= H&G_market_closed_count
      if (!isset($$market_closed_name)) {
        $$market_closed_name =  0;
      }
      $half_day_name = $chain . '_half_day_count';
      // half_day_name= H&G_half_day_count
      if (!isset($$half_day_name)) {
        $$half_day_name =  0;
      }
      $holiday_name = $chain . '_holiday_count';
      // holiday_name= H&G_holiday_count
      if (!isset($$holiday_name)) {
        $$holiday_name =  0;
      }
      $work_from_home_name = $chain . '_work_from_home_count';
      // work_from_home_name= H&G_work_from_home_count
      if (!isset($$work_from_home_name)) {
        $$work_from_home_name =  0;
      }

      $other_channel_attandances[$chain] = [
        'total_App_id' => $$total_name,
        'Active_Ba_Count' => $$Active_name,
        'present_count' => $$present_name,
        'weekly_off_count' => $$weekly_off_name,
        'leave_count' => $$leave_name,
        'meeting_count' => $$meeting_name,
        'market_closed_count' => $$market_closed_name,
        'half_day_count' => $$half_day_name,
        'holiday_count' => $$holiday_name,
        'work_from_home_count' => $$work_from_home_name,
        'gap' => $$Active_name - ($$present_name
          + $$weekly_off_name
          + $$leave_name
          + $$meeting_name
          + $$market_closed_name
          + $$half_day_name
          + $$holiday_name
          + $$work_from_home_name),
        'gap_count' => $$Gap_name,
        'absent_count' => $$Absent_name,
      ];
    }

    return response()->json([
      'data'     =>  $other_channel_attandances,
      'count' => $count,
      'success' =>  true
    ], 200);
  }
}
