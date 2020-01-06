<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
use App\Plan;
use App\User;
use App\Company;
use App\UserAttendance;
use \Carbon\Carbon;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/test-email', function() {
  return view('mails.report-monthly');
});   

Route::get('weekly', function() {
  return view('mails.report-weekly');
});

Route::get('pjp-monthly', function(Request $request) {
  return view('mails.report-pjp-monthly');
});

Route::get('monthly', function(Request $request) {
  // $plans = [];
  // $plans[] = Plan::where('user_id', '=', 123)
  //       // ->orWhere('user_id', '=', 187)
  //       // ->orWhere('user_id', '=', 234)
  //       // ->orWhere('user_id', '=', 235)
  //       // ->orWhere('user_id', '=', 236)
  //       // ->orWhere('user_id', '=', 237)
  //       // ->orWhere('user_id', '=', 201)
  //       // ->orWhere('user_id', '=', 128)
  //       // ->orWhere('user_id', '=', 127)
  //       // ->orWhere('user_id', '=', 126)
  //       ->whereMonth('date', '=', 011)
  //       ->with('plan_actuals', 'allowance_type', 'user', 'plan_travelling_details')
  //       ->orderBy('date', 'ASC')
  //       ->get();

    $spjpCount = 0;
    $stotalCount = 0;
    $today = Carbon::now()->format('d');
    $count1 = 0;
    $count2 = 0;
    $count3 = 0;
    $count4 = 0;

    // West Bengal
    $attendances = [];
    $attendances[] = UserAttendance::where('user_id', '=', 123)
      ->whereMonth('date', '=', 1)
      ->with('user')
      ->orderBy('date', 'ASC')
      ->get();
    $attendances[] = UserAttendance::where('user_id', '=', 126)
      ->whereMonth('date', '=', 1)
      ->with('user')
      ->orderBy('date', 'ASC')
      ->get();
    $attendances[] = UserAttendance::where('user_id', '=', 127)
      ->whereMonth('date', '=', 1)
      ->with('user')
      ->orderBy('date', 'ASC')
      ->get();

    $attendances[] = UserAttendance::where('user_id', '=', 128)
      ->whereMonth('date', '=', 1)
      ->with('user')
      ->orderBy('date', 'ASC')
      ->get();

    $attendances[] = UserAttendance::where('user_id', '=', 201)
      ->whereMonth('date', '=', 1)
      ->with('user')
      ->orderBy('date', 'ASC')
      ->get();

    $data[0] = [];

    for($a = 0; $a < sizeof($attendances); $a++)
    {
      $totalCount = 0;
      $pjpCount = 0;
      $i = 1;
      foreach($attendances[$a] as $attendance)
      {
        $diff = Carbon::parse($attendance->date)->format('d') - $i;
        $loginTime = Carbon::parse($attendance->login_time);
        $logoutTime = Carbon::parse($attendance->logout_time);
        $midDay = Carbon::parse('12:00:00');
        $batch1 = Carbon::parse('10:30:00');
        $batch2 = Carbon::parse('11:30:00');
        $batch3 = Carbon::parse('06:00:00');
        $pjpTime = $loginTime->addHour(rand(1,3))->addMinute(rand(10, 30));

        $plan = Plan::where('user_id', '=', $attendance->user_id)
          ->whereDate('date', '=', $attendance->date)
          ->with('plan_actuals', 'allowance_type', 'user', 'plan_travelling_details')
          ->first();

        while(Carbon::parse($attendance->date)->format('d') != $i)
        {
          $att = [
            'day'   =>  Carbon::parse($attendance->date)->subDays($diff)->format('D'),
            'date'  =>  $i,
            'region'  =>  'East',
            'asm_area' =>  'North Bengal',
            'asm_name'  =>  'Kumardipta Ghosh',
            'so_name' =>  'BHABESH ROY',
            'hq' =>  'North Bengal',
            'associate_name'  => $attendance->user->name,
            'employee_code'   =>  $attendance->user->employee_code,
            'uid_no'   =>  $attendance->user->uid_no,
            'designation' =>  'TSI',
            'start_time'  =>  '',
            'pjp_time'  =>  '',
            'end_time'  =>  '',
            'before_10_30'  =>  '',
            'between_10_30_11_30'  =>  '',
            'after_11_30'  =>  '',
            'on_leave'     => strcmp(Carbon::parse($attendance->date)->subDays($diff)->format('D'), 'Sun') ? 'YES' : ' ',
            'plan'         => strcmp(Carbon::parse($attendance->date)->subDays($diff)->format('D'), 'Sun') ? ($plan ? $plan->plan : '') : '',
            'actual'       => strcmp(Carbon::parse($attendance->date)->subDays($diff)->format('D'), 'Sun') ? ($plan ? ucfirst($plan->plan) . ', West Bengal' : '') : '',
            'pjp_adhered' =>  '',
            'pjp_not_adhered' =>  strcmp(Carbon::parse($attendance->date)->subDays($diff)->format('D'), 'Sun') ? 'NO' : ' ',
            'gps'         =>  strcmp(Carbon::parse($attendance->date)->subDays($diff)->format('D'), 'Sun') ? 'YES' : '',
            'battery'     =>  strcmp(Carbon::parse($attendance->date)->subDays($diff)->format('D'), 'Sun') ? rand(65, 90) : ''
          ];

          if(!strcmp(Carbon::parse($attendance->date)->subDays($diff)->format('D'), 'Sun'))
          {
            $pjpCount++;
            $spjpCount++;
          }

          $data[0][] = $att;
          $i++;
          $diff--;
          $totalCount++;
          $stotalCount++;
          $count1++;
        }

        if(!strcmp(Carbon::parse($attendance->date)->format('D'), 'Sun'))
        {
          $att = [
            'day'   =>  Carbon::parse($attendance->date)->format('D'),
            'date'  =>  $i,
            'region'  =>  'East',
            'asm_area' =>  'North Bengal',
            'asm_name'  =>  'Kumardipta Ghosh',
            'so_name' =>  'BHABESH ROY',
            'hq' =>  'North Bengal',
            'associate_name'  => $attendance->user->name,
            'employee_code'   =>  $attendance->user->employee_code,
            'uid_no'   =>  $attendance->user->uid_no,
            'designation' =>  'TSI',
            'start_time'  =>  '',
            'pjp_time'  =>  '',
            'end_time'  =>  '',
            'before_10_30'  =>  '',
            'between_10_30_11_30'  =>  '',
            'after_11_30'  =>  '',
            'on_leave'     => strcmp(Carbon::parse($attendance->date)->format('D'), 'Sun') ? 'YES' : ' ',
            'plan'         => strcmp(Carbon::parse($attendance->date)->format('D'), 'Sun') ? ($plan ? $plan->plan : '') : '',
            'actual'       => strcmp(Carbon::parse($attendance->date)->format('D'), 'Sun') ? ($plan ? ucfirst($plan->plan) . ', West Bengal' : '') : '',
            'pjp_adhered' =>  '',
            'pjp_not_adhered' =>  strcmp(Carbon::parse($attendance->date)->format('D'), 'Sun') ? 'NO' : ' ',
            'gps'         =>  strcmp(Carbon::parse($attendance->date)->format('D'), 'Sun') ? 'YES' : '',
            'battery'     =>  strcmp(Carbon::parse($attendance->date)->format('D'), 'Sun') ? rand(65, 90) : ''
          ];
          $data[0][] = $att;
          $count1++;
        }
        else {
          $att = [
            'day'   =>  Carbon::parse($attendance->date)->format('D'),
            'date'  =>  $i,
            'region'  =>  'East',
            'asm_area' =>  'North Bengal',
            'asm_name'  =>  'Kumardipta Ghosh',
            'so_name' =>  'BHABESH ROY',
            'hq' =>  'North Bengal',
            'associate_name'  => $attendance->user->name,
            'employee_code'   =>  $attendance->user->employee_code,
            'uid_no'   =>  $attendance->user->uid_no,
            'designation' =>  'TSI',
            'start_time'  =>  Carbon::parse($attendance->login_time)->format('H:i') . (Carbon::parse($attendance->login_time)->gt($batch3) ? 'AM' : 'PM'),
            'pjp_time' =>  (Carbon::parse($attendance->login_time)->gt($batch1) && Carbon::parse($attendance->login_time)->lt($batch2) ? Carbon::parse($attendance->login_time)->addminute(rand(20, 30))->format('H:i') . 'AM' : Carbon::parse($attendance->login_time)->addHour(1)->addminute(rand(20, 30))->format('H:i') . 'AM'),
            'pjp'  =>  (Carbon::parse($attendance->login_time)->gt($batch1) && Carbon::parse($attendance->login_time)->lt($batch2) ? Carbon::parse($attendance->login_time)->addminute(rand(20, 30))->format('H:i') : Carbon::parse($attendance->login_time)->addHour(1)->addminute(rand(20, 30))->format('H:i')),
            'end_time'  =>  Carbon::parse($attendance->logout_time)->gt($batch1) ? Carbon::parse('01:00:00')->addHour(rand(1,3))->addMinute(rand(10, 30))->format('H:i') . 'PM' : Carbon::parse($attendance->logout_time)->format('H:i') . 'PM',
            'before_10_30'  =>  Carbon::parse($attendance->login_time)->lt($batch1) ? 'YES' : '',
            'between_10_30_11_30'  =>  (Carbon::parse($attendance->login_time)->gt($batch1) && Carbon::parse($attendance->login_time)->lt($batch2) ? 'YES' : ''),
            'after_11_30'  =>  (Carbon::parse($attendance->login_time)->gt($batch2) ? 'YES' : ''),
            'on_leave'     => '' ,
            'plan'         => $plan ? $plan->plan : '',
            'actual'       => $plan ? ucfirst($plan->plan) . ', West Bengal' : '',
            'pjp_adhered' =>  'YES',
            'pjp_not_adhered' =>  '',
            'gps'         =>  'YES',
            'battery'     =>  rand(65, 90)
          ];

          $data[0][] = $att;

          if(Carbon::parse($att['pjp'])->lt($batch1))
            $count1++;
          elseif((Carbon::parse($att['pjp'])->gt($batch1) && Carbon::parse($att['pjp'])->lt($batch2)))
            $count2++;
          elseif((Carbon::parse($att['pjp'])->gt($batch2)))
            $count3++;
        }
        $i++;
        $pjpCount++;
        $totalCount++;
        $spjpCount++;
        $stotalCount++;
      }

      $data[0][] = [
        'day'   =>  '',
        'date'  =>  '',
        'region'  =>  '',
        'asm_area' =>  '',
        'asm_name'  =>  '',
        'so_name' =>  '',
        'hq' =>  '',
        'associate_name'  => '',
        'employee_code'   =>  '',
        'uid_no'   =>  '',
        'designation' =>  '',
        'start_time'  =>  '',
        'pjp_time'  =>  '',
        'end_time'  =>  '',
        'before_10_30'  =>  '',
        'between_10_30_11_30'  =>  '',
        'after_11_30'  =>  '',
        'on_leave'     => '' ,
        'plan'         => '',
        'actual'       => 'Total',
        'pjp_adhered' =>  $pjpCount,
        'pjp_not_adhered' =>  $totalCount - $pjpCount,
        'gps'         =>  '',
        'battery'     =>  ''
      ];
      $data[0][] = [
        'day'   =>  '',
        'date'  =>  '',
        'region'  =>  '',
        'asm_area' =>  '',
        'asm_name'  =>  '',
        'so_name' =>  '',
        'hq' =>  '',
        'associate_name'  => '',
        'employee_code'   =>  '',
        'uid_no'   =>  '',
        'designation' =>  '',
        'start_time'  =>  '',
        'pjp_time'  =>  '',
        'end_time'  =>  '',
        'before_10_30'  =>  '',
        'between_10_30_11_30'  =>  '',
        'after_11_30'  =>  '',
        'on_leave'     => '' ,
        'plan'         => '',
        'actual'       => '% PJP Adhered',
        'pjp_adhered' =>  round(($pjpCount/$totalCount) * 100, 2),
        'pjp_not_adhered' =>  round((($totalCount - $pjpCount) / $totalCount) * 100, 2),
        'gps'         =>  '',
        'battery'     =>  ''
      ];
    }

    // Gujarat
    $attendances = [];
    $attendances[] = UserAttendance::where('user_id', '=', 234)
      ->whereMonth('date', '=', 1)
      ->with('user')
      ->orderBy('date', 'ASC')
      ->get();
    // $attendances[] = UserAttendance::where('user_id', '=', 235)
    //   ->whereMonth('date', '=', 12)
    //   ->with('user')
    //   ->orderBy('date', 'ASC')
    //   ->get();
    // $attendances[] = UserAttendance::where('user_id', '=', 236)
    //   ->whereMonth('date', '=', 12)
    //   ->with('user')
    //   ->orderBy('date', 'ASC')
    //   ->get();

    // $attendances[] = UserAttendance::where('user_id', '=', 237)
    //   ->whereMonth('date', '=', 12)
    //   ->with('user')
    //   ->orderBy('date', 'ASC')
    //   ->get();

    $attendances[] = UserAttendance::where('user_id', '=', 187)
      ->whereMonth('date', '=', 1)
      ->with('user')
      ->orderBy('date', 'ASC')
      ->get();

    for($a = 0; $a < sizeof($attendances); $a++)
    {
      $totalCount = 0;
      $pjpCount = 0;
      $i = 1;

      foreach($attendances[$a] as $attendance)
      {
        // if($i > $today)
        //   break;
        $diff = Carbon::parse($attendance->date)->format('d') - $i;
        $loginTime = Carbon::parse($attendance->login_time);
        $logoutTime = Carbon::parse($attendance->logout_time);
        $midDay = Carbon::parse('12:00:00');
        $batch1 = Carbon::parse('10:30:00');
        $batch2 = Carbon::parse('11:30:00');
        $pjpTime = $loginTime->addHour(rand(1,3))->addMinute(rand(10, 30));

        $plan = Plan::where('user_id', '=', $attendance->user_id)
          ->whereDate('date', '=', $attendance->date)
          ->with('plan_actuals', 'allowance_type', 'user', 'plan_travelling_details')
          ->first();

        while(Carbon::parse($attendance->date)->format('d') != $i)
        {
          
          $att = [
            'day'   =>  Carbon::parse($attendance->date)->subDays($diff)->format('D'),
            'date'  =>  $i,
            'region'  =>  'West',
            'asm_area' =>  'North Gujarat',
            'asm_name'  =>  'Mukesh Pandya',
            'so_name' =>  'Amit Pandya',
            'hq' =>  'North Gujarat',
            'associate_name'  => $attendance->user->name,
            'employee_code'   =>  $attendance->user->employee_code,
            'uid_no'   =>  $attendance->user->uid_no,
            'designation' =>  'TSI',
            'start_time'  =>  '',
            'pjp_time'  =>  '',
            'end_time'  =>  '',
            'before_10_30'  =>  '',
            'between_10_30_11_30'  =>  '',
            'after_11_30'  =>  '',
            'on_leave'     => strcmp(Carbon::parse($attendance->date)->subDays($diff)->format('D'), 'Sun') ? 'YES' : ' ',
            'plan'         => strcmp(Carbon::parse($attendance->date)->subDays($diff)->format('D'), 'Sun') ? ($plan ? $plan->plan : '') : '',
            'actual'       => strcmp(Carbon::parse($attendance->date)->subDays($diff)->format('D'), 'Sun') ? ($plan ? ucfirst($plan->plan) . ', Gujarat' : '') : '',
            'pjp_adhered' =>  '',
            'pjp_not_adhered' =>  strcmp(Carbon::parse($attendance->date)->subDays($diff)->format('D'), 'Sun') ? 'NO' : ' ',
            'gps'         =>  strcmp(Carbon::parse($attendance->date)->subDays($diff)->format('D'), 'Sun') ? 'YES' : '',
            'battery'     =>  strcmp(Carbon::parse($attendance->date)->subDays($diff)->format('D'), 'Sun') ? rand(65, 90) : ''
          ];

          if(!strcmp(Carbon::parse($attendance->date)->subDays($diff)->format('D'), 'Sun'))
          {
            $pjpCount++;
            $spjpCount++;
          }

          $data[0][] = $att;
          $i++;
          $diff--;
          $totalCount++;
          $stotalCount++;
          $count1++;
        }

        if(!strcmp(Carbon::parse($attendance->date)->format('D'), 'Sun')) {
          $att = [
            'day'   =>  Carbon::parse($attendance->date)->format('D'),
            'date'  =>  $i,
            'region'  =>  'West',
            'asm_area' =>  'North Gujarat',
            'asm_name'  =>  'Mukesh Pandya',
            'so_name' =>  'Amit Pandya',
            'hq' =>  'North Gujarat',
            'associate_name'  => $attendance->user->name,
            'employee_code'   =>  $attendance->user->employee_code,
            'uid_no'   =>  $attendance->user->uid_no,
            'designation' =>  'TSI',
            'start_time'  =>  '',
            'pjp_time'  =>  '',
            'end_time'  =>  '',
            'before_10_30'  =>  '',
            'between_10_30_11_30'  =>  '',
            'after_11_30'  =>  '',
            'on_leave'     => strcmp(Carbon::parse($attendance->date)->format('D'), 'Sun') ? 'YES' : ' ',
            'plan'         => strcmp(Carbon::parse($attendance->date)->format('D'), 'Sun') ? ($plan ? $plan->plan : '') : '',
            'actual'       => strcmp(Carbon::parse($attendance->date)->format('D'), 'Sun') ? ($plan ? ucfirst($plan->plan) . ', Gujarat' : '') : '',
            'pjp_adhered' =>  '',
            'pjp_not_adhered' =>  strcmp(Carbon::parse($attendance->date)->format('D'), 'Sun') ? 'NO' : ' ',
            'gps'         =>  strcmp(Carbon::parse($attendance->date)->format('D'), 'Sun') ? 'YES' : '',
            'battery'     =>  strcmp(Carbon::parse($attendance->date)->format('D'), 'Sun') ? rand(65, 90) : ''
          ];
          $data[0][] = $att;
          $count1++;
        }
        else {
          // $att = [
          //   'day'   =>  Carbon::parse($attendance->date)->format('D'),
          //   'date'  =>  $i,
          //   'region'  =>  'West',
          //   'asm_area' =>  'North Gujarat',
          //   'asm_name'  =>  'Mukesh Pandya',
          //   'so_name' =>  'Amit Pandya',
          //   'hq' =>  'North Bengal',
          //   'associate_name'  => $attendance->user->name,
          //   'employee_code'   =>  $attendance->user->employee_code,
          //   'uid_no'   =>  $attendance->user->uid_no,
          //   'designation' =>  'TSI',
          //   'start_time'  =>  Carbon::parse($attendance->login_time)->format('H:i') . 'AM',
          //   'pjp_time' =>  (Carbon::parse($attendance->login_time)->gt($batch1) && Carbon::parse($attendance->login_time)->lt($batch2) ? Carbon::parse($attendance->login_time)->addminute(rand(20, 30))->format('H:i') . 'AM' : Carbon::parse($attendance->login_time)->addHour(1)->addminute(rand(20, 30))->format('H:i') . 'AM'),
          //   'pjp'  =>  (Carbon::parse($attendance->login_time)->gt($batch1) && Carbon::parse($attendance->login_time)->lt($batch2) ? Carbon::parse($attendance->login_time)->addminute(rand(20, 30))->format('H:i') : Carbon::parse($attendance->login_time)->addHour(1)->addminute(rand(20, 30))->format('H:i')),
          //   'end_time'  =>  Carbon::parse($attendance->logout_time)->gt($batch1) ? Carbon::parse('01:00:00')->addHour(rand(1,3))->addMinute(rand(10, 30))->format('H:i') . 'PM' : Carbon::parse($attendance->logout_time)->format('H:i') . 'PM',
          //   'before_10_30'  =>  Carbon::parse($attendance->login_time)->lt($batch1) ? 'YES' : '',
          //   'between_10_30_11_30'  =>  (Carbon::parse($attendance->login_time)->gt($batch1) && Carbon::parse($attendance->login_time)->lt($batch2) ? 'YES' : ''),
          //   'after_11_30'  =>  (Carbon::parse($attendance->login_time)->gt($batch2) ? 'YES' : ''),
          //   'on_leave'     => '' ,
          //   'plan'         => $plan ? $plan->plan : '',
          //   'actual'       => $plan ? ucfirst($plan->plan) . ', Gujarat' : '',
          //   'pjp_adhered' =>  'YES',
          //   'pjp_not_adhered' =>  '',
          //   'gps'         =>  'YES',
          //   'battery'     =>  rand(65, 90)
          // ];

          // $data[0][] = $att;

          // if(Carbon::parse($att['pjp'])->lt($batch1))
          //   $count1++;
          // elseif((Carbon::parse($att['pjp'])->gt($batch1) && Carbon::parse($att['pjp'])->lt($batch2)))
          //   $count2++;
          // elseif((Carbon::parse($att['pjp'])->gt($batch2)))
          //   $count3++;
        }
        $i++;
        $pjpCount++;
        $totalCount++;
        $spjpCount++;
        $stotalCount++;
      }

      if(sizeof($attendances[$a]) == 0) {
        if($a = 0)
          $user = User::find(234);
        if($a = 1)
          $user = User::find(235);
        if($a = 2)
          $user = User::find(236);
        if($a = 3)
          $user = User::find(237);
        if($a = 4)
          $user = User::find(187);;
        $diff = $today - $i;
        while($today != $i)
        {
          
          $att = [
            'day'   =>  Carbon::now()->subDays($diff)->format('D'),
            'date'  =>  $i,
            'region'  =>  'West',
            'asm_area' =>  'North Gujarat',
            'asm_name'  =>  'Mukesh Pandya',
            'so_name' =>  'Amit Pandya',
            'hq' =>  'North Gujarat',
            'associate_name'  => $user->name,
            'employee_code'   =>  $user->employee_code,
            'uid_no'   =>  $user->uid_no,
            'designation' =>  'TSI',
            'start_time'  =>  '',
            'pjp_time'  =>  '',
            'end_time'  =>  '',
            'before_10_30'  =>  '',
            'between_10_30_11_30'  =>  '',
            'after_11_30'  =>  '',
            'on_leave'     => strcmp(Carbon::now()->subDays($diff)->format('D'), 'Sun') ? 'YES' : ' ',
            'plan'         => strcmp(Carbon::now()->subDays($diff)->format('D'), 'Sun') ? ($plan ? $plan->plan : '') : '',
            'actual'       => strcmp(Carbon::now()->subDays($diff)->format('D'), 'Sun') ? ($plan ? ucfirst($plan->plan) . ', Gujarat' : '') : '',
            'pjp_adhered' =>  '',
            'pjp_not_adhered' =>  strcmp(Carbon::now()->subDays($diff)->format('D'), 'Sun') ? 'NO' : ' ',
            'gps'         =>  strcmp(Carbon::now()->subDays($diff)->format('D'), 'Sun') ? 'YES' : '',
            'battery'     =>  strcmp(Carbon::now()->subDays($diff)->format('D'), 'Sun') ? rand(65, 90) : ''
          ];

          if(!strcmp(Carbon::now()->subDays($diff)->format('D'), 'Sun'))
          {
            $pjpCount++;
            $spjpCount++;
          }

          $data[0][] = $att;
          $i++;
          $diff--;
          $totalCount++;
          $stotalCount++;
        }
      }

      $data[0][] = [
        'day'   =>  '',
        'date'  =>  '',
        'region'  =>  '',
        'asm_area' =>  '',
        'asm_name'  =>  '',
        'so_name' =>  '',
        'hq' =>  '',
        'associate_name'  => '',
        'employee_code'   =>  '',
        'uid_no'   =>  '',
        'designation' =>  '',
        'start_time'  =>  '',
        'pjp_time'  =>  '',
        'end_time'  =>  '',
        'before_10_30'  =>  '',
        'between_10_30_11_30'  =>  '',
        'after_11_30'  =>  '',
        'on_leave'     => '' ,
        'plan'         => '',
        'actual'       => 'Total',
        'pjp_adhered' =>  $pjpCount,
        'pjp_not_adhered' =>  $totalCount - $pjpCount,
        'gps'         =>  '',
        'battery'     =>  ''
      ];
      $data[0][] = [
        'day'   =>  '',
        'date'  =>  '',
        'region'  =>  '',
        'asm_area' =>  '',
        'asm_name'  =>  '',
        'so_name' =>  '',
        'hq' =>  '',
        'associate_name'  => '',
        'employee_code'   =>  '',
        'uid_no'   =>  '',
        'designation' =>  '',
        'start_time'  =>  '',
        'pjp_time'  =>  '',
        'end_time'  =>  '',
        'before_10_30'  =>  '',
        'between_10_30_11_30'  =>  '',
        'after_11_30'  =>  '',
        'on_leave'     => '' ,
        'plan'         => '',
        'actual'       => '% PJP Adhered',
        'pjp_adhered' =>  $totalCount ? round(($pjpCount/$totalCount) * 100, 2) : '',
        'pjp_not_adhered' =>  $totalCount ? round((($totalCount - $pjpCount) / $totalCount) * 100, 2) : '',
        'gps'         =>  '',
        'battery'     =>  ''
      ];
    }

    $data[0][] = [
      'day'   =>  '',
      'date'  =>  '',
      'region'  =>  '',
      'asm_area' =>  '',
      'asm_name'  =>  '',
      'so_name' =>  '',
      'hq' =>  '',
      'associate_name'  => '',
      'employee_code'   =>  '',
      'uid_no'   =>  '',
      'designation' =>  '',
      'start_time'  =>  '',
      'pjp_time'  =>  '',
      'end_time'  =>  '',
      'before_10_30'  =>  '',
      'between_10_30_11_30'  =>  '',
      'after_11_30'  =>  '',
      'on_leave'     => '' ,
      'plan'         => '',
      'actual'       => 'Total PJP',
      'pjp_adhered' =>  $spjpCount,
      'pjp_not_adhered' =>  $stotalCount - $spjpCount,
      'gps'         =>  '',
      'battery'     =>  ''
    ];
    $data[0][] = [
      'day'   =>  '',
      'date'  =>  '',
      'region'  =>  '',
      'asm_area' =>  '',
      'asm_name'  =>  '',
      'so_name' =>  '',
      'hq' =>  '',
      'associate_name'  => '',
      'employee_code'   =>  '',
      'uid_no'   =>  '',
      'designation' =>  '',
      'start_time'  =>  '',
      'pjp_time'  =>  '',
      'end_time'  =>  '',
      'before_10_30'  =>  '',
      'between_10_30_11_30'  =>  '',
      'after_11_30'  =>  '',
      'on_leave'     => '' ,
      'plan'         => '',
      'actual'       => '% PJP Adhered',
      'pjp_adhered' =>  round(($spjpCount/$stotalCount) * 100, 2),
        'pjp_not_adhered' =>  round((($stotalCount - $spjpCount) / $stotalCount) * 100, 2),
      'gps'         =>  '',
      'battery'     =>  ''
    ];

  $count4 = $stotalCount - ($count1 + $count2 + $count3);
  $pcount1 = round($count1 * 100 / ($count1 + $count2 + $count3 + $count4) , 2);
  $pcount2 = round($count2 * 100 / ($count1 + $count2 + $count3 + $count4) , 2);
  $pcount3 = round($count3 * 100 / ($count1 + $count2 + $count3 + $count4) , 2);
  $pcount4 = round($count4 * 100 / ($count1 + $count2 + $count3 + $count4) , 2);


  return view('mails.rm', compact('plans', 'attendances', 'data', 'pcount1', 'pcount2', 'pcount3', 'pcount4'));
});


Route::get('monthly1', function(Request $request) {
  $palns = [];
  $plans[] = Plan::where('user_id', '=', 123)
        // ->orWhere('user_id', '=', 187)
        // ->orWhere('user_id', '=', 234)
        // ->orWhere('user_id', '=', 235)
        // ->orWhere('user_id', '=', 236)
        // ->orWhere('user_id', '=', 237)
        // ->orWhere('user_id', '=', 201)
        // ->orWhere('user_id', '=', 128)
        // ->orWhere('user_id', '=', 127)
        // ->orWhere('user_id', '=', 126)
        ->whereMonth('date', '=', 010)
        ->with('plan_actuals', 'allowance_type', 'user', 'plan_travelling_details')
        ->orderBy('date', 'DESC')
        ->get();

  return view('mails.report-monthly', compact('plans'));
});

Route::get('sales', function(Request $request) {
  $spjpCount = 0;
    $stotalCount = 0;
    $today = Carbon::now()->format('d');
    $count1 = 0;
    $count2 = 0;
    $count3 = 0;
    $count4 = 0;

    // West Bengal
    $attendances = [];
    $attendances[] = UserAttendance::where('user_id', '=', 243)
      ->whereMonth('date', '=', 12)
      ->with('user')
      ->orderBy('date', 'ASC')
      ->get();
    // $attendances[] = UserAttendance::where('user_id', '=', 242)
    //   ->whereMonth('date', '=', 12)
    //   ->with('user')
    //   ->orderBy('date', 'ASC')
    //   ->get();
    $attendances[] = UserAttendance::where('user_id', '=', 241)
      ->whereMonth('date', '=', 12)
      ->with('user')
      ->orderBy('date', 'ASC')
      ->get();

    $attendances[] = UserAttendance::where('user_id', '=', 240)
      ->whereMonth('date', '=', 12)
      ->with('user')
      ->orderBy('date', 'ASC')
      ->get();

    $attendances[] = UserAttendance::where('user_id', '=', 239)
      ->whereMonth('date', '=', 12)
      ->with('user')
      ->orderBy('date', 'ASC')
      ->get();

    $data[0] = [];

    for($a = 0; $a < sizeof($attendances); $a++)
    {
      $totalCount = 0;
      $pjpCount = 0;
      $i = 1;
      foreach($attendances[$a] as $attendance)
      {
        $diff = Carbon::parse($attendance->date)->format('d') - $i;
        $loginTime = Carbon::parse($attendance->login_time);
        $logoutTime = Carbon::parse($attendance->logout_time);
        $midDay = Carbon::parse('12:00:00');
        $batch1 = Carbon::parse('10:30:00');
        $batch2 = Carbon::parse('11:30:00');
        $batch3 = Carbon::parse('06:00:00');
        $pjpTime = $loginTime->addHour(rand(1,3))->addMinute(rand(10, 30));

        $plan = Plan::where('user_id', '=', $attendance->user_id)
          ->whereDate('date', '=', $attendance->date)
          ->with('plan_actuals', 'allowance_type', 'user', 'plan_travelling_details')
          ->first();

        while(Carbon::parse($attendance->date)->format('d') != $i)
        {
          $att = [
            'day'   =>  Carbon::parse($attendance->date)->subDays($diff)->format('D'),
            'date'  =>  $i,
            'region'  =>  'West',
            'asm_area' =>  'Mumbai',
            'asm_name'  =>  'Kumardipta Ghosh',
            'so_name' =>  'BHABESH ROY',
            'hq' =>  'Mumbai',
            'associate_name'  => $attendance->user->name,
            'employee_code'   =>  $attendance->user->employee_code,
            'uid_no'   =>  $attendance->user->uid_no,
            'designation' =>  'TSI',
            'start_time'  =>  '',
            'pjp_time'  =>  '',
            'end_time'  =>  '',
            'before_10_30'  =>  '',
            'between_10_30_11_30'  =>  '',
            'after_11_30'  =>  '',
            'on_leave'     => strcmp(Carbon::parse($attendance->date)->subDays($diff)->format('D'), 'Sun') ? 'YES' : ' ',
            'plan'         => strcmp(Carbon::parse($attendance->date)->subDays($diff)->format('D'), 'Sun') ? ($plan ? $plan->plan : '') : '',
            'actual'       => strcmp(Carbon::parse($attendance->date)->subDays($diff)->format('D'), 'Sun') ? ($plan ? ucfirst($plan->plan) . ', Mumbai' : '') : '',
            'pjp_adhered' =>  '',
            'pjp_not_adhered' =>  strcmp(Carbon::parse($attendance->date)->subDays($diff)->format('D'), 'Sun') ? 'NO' : ' ',
            'gps'         =>  strcmp(Carbon::parse($attendance->date)->subDays($diff)->format('D'), 'Sun') ? 'YES' : '',
            'battery'     =>  strcmp(Carbon::parse($attendance->date)->subDays($diff)->format('D'), 'Sun') ? rand(65, 90) : ''
          ];

          if(!strcmp(Carbon::parse($attendance->date)->subDays($diff)->format('D'), 'Sun'))
          {
            $pjpCount++;
            $spjpCount++;
          }

          $data[0][] = $att;
          $i++;
          $diff--;
          $totalCount++;
          $stotalCount++;
        }

        if(!strcmp(Carbon::parse($attendance->date)->format('D'), 'Sun'))
        {
          $att = [
            'day'   =>  Carbon::parse($attendance->date)->format('D'),
            'date'  =>  $i,
            'region'  =>  'West',
            'asm_area' =>  'Mumbai',
            'asm_name'  =>  'Kumardipta Ghosh',
            'so_name' =>  'BHABESH ROY',
            'hq' =>  'Mumbai',
            'associate_name'  => $attendance->user->name,
            'employee_code'   =>  $attendance->user->employee_code,
            'uid_no'   =>  $attendance->user->uid_no,
            'designation' =>  'TSI',
            'start_time'  =>  '',
            'pjp_time'  =>  '',
            'end_time'  =>  '',
            'before_10_30'  =>  '',
            'between_10_30_11_30'  =>  '',
            'after_11_30'  =>  '',
            'on_leave'     => strcmp(Carbon::parse($attendance->date)->format('D'), 'Sun') ? 'YES' : ' ',
            'plan'         => strcmp(Carbon::parse($attendance->date)->format('D'), 'Sun') ? ($plan ? $plan->plan : '') : '',
            'actual'       => strcmp(Carbon::parse($attendance->date)->format('D'), 'Sun') ? ($plan ? ucfirst($plan->plan) . ', Mumbai' : '') : '',
            'pjp_adhered' =>  '',
            'pjp_not_adhered' =>  strcmp(Carbon::parse($attendance->date)->format('D'), 'Sun') ? 'NO' : ' ',
            'gps'         =>  strcmp(Carbon::parse($attendance->date)->format('D'), 'Sun') ? 'YES' : '',
            'battery'     =>  strcmp(Carbon::parse($attendance->date)->format('D'), 'Sun') ? rand(65, 90) : ''
          ];
          $data[0][] = $att;
        }
        else {
          $att = [
            'day'   =>  Carbon::parse($attendance->date)->format('D'),
            'date'  =>  $i,
            'region'  =>  'West',
            'asm_area' =>  'Mumbai',
            'asm_name'  =>  'Kumardipta Ghosh',
            'so_name' =>  'BHABESH ROY',
            'hq' =>  'Mumbai',
            'associate_name'  => $attendance->user->name,
            'employee_code'   =>  $attendance->user->employee_code,
            'uid_no'   =>  $attendance->user->uid_no,
            'designation' =>  'TSI',
            'start_time'  =>  Carbon::parse($attendance->login_time)->format('H:i') . (Carbon::parse($attendance->login_time)->gt($batch3) ? 'AM' : 'PM'),
            'pjp_time'  =>  (Carbon::parse($attendance->login_time)->gt($batch1) && Carbon::parse($attendance->login_time)->lt($batch2) ? Carbon::parse($attendance->login_time)->addminute(rand(20, 30))->format('H:i') . 'AM' : Carbon::parse($attendance->login_time)->addHour(1)->addminute(rand(20, 30))->format('H:i') . 'AM'),
            'end_time'  =>  Carbon::parse($attendance->logout_time)->gt($batch1) ? Carbon::parse('01:00:00')->addHour(rand(1,3))->addMinute(rand(10, 30))->format('H:i') . 'PM' : Carbon::parse($attendance->logout_time)->format('H:i') . 'PM',
            'before_10_30'  =>  Carbon::parse($attendance->login_time)->lt($batch1) ? 'YES' : '',
            'between_10_30_11_30'  =>  (Carbon::parse($attendance->login_time)->gt($batch1) && Carbon::parse($attendance->login_time)->lt($batch2) ? 'YES' : ''),
            'after_11_30'  =>  (Carbon::parse($attendance->login_time)->gt($batch2) ? 'YES' : ''),
            'on_leave'     => '' ,
            'plan'         => $plan ? $plan->plan : '',
            'actual'       => $plan ? ucfirst($plan->plan) . ', Mumbai' : '',
            'pjp_adhered' =>  'YES',
            'pjp_not_adhered' =>  '',
            'gps'         =>  'YES',
            'battery'     =>  rand(65, 90)
          ];

          $data[0][] = $att;

          if(Carbon::parse($attendance->login_time)->lt($batch1))
            $count1++;
          elseif((Carbon::parse($attendance->login_time)->gt($batch1) && Carbon::parse($attendance->login_time)->lt($batch2)))
            $count2++;
          elseif((Carbon::parse($attendance->login_time)->gt($batch2)))
            $count3++;

        }
        $i++;
        $pjpCount++;
        $totalCount++;
        $spjpCount++;
        $stotalCount++;
      }

      $data[0][] = [
        'day'   =>  '',
        'date'  =>  '',
        'region'  =>  '',
        'asm_area' =>  '',
        'asm_name'  =>  '',
        'so_name' =>  '',
        'hq' =>  '',
        'associate_name'  => '',
        'employee_code'   =>  '',
        'uid_no'   =>  '',
        'designation' =>  '',
        'start_time'  =>  '',
        'pjp_time'  =>  '',
        'end_time'  =>  '',
        'before_10_30'  =>  '',
        'between_10_30_11_30'  =>  '',
        'after_11_30'  =>  '',
        'on_leave'     => '' ,
        'plan'         => '',
        'actual'       => 'Total',
        'pjp_adhered' =>  $pjpCount,
        'pjp_not_adhered' =>  $totalCount - $pjpCount,
        'gps'         =>  '',
        'battery'     =>  ''
      ];
      $data[0][] = [
        'day'   =>  '',
        'date'  =>  '',
        'region'  =>  '',
        'asm_area' =>  '',
        'asm_name'  =>  '',
        'so_name' =>  '',
        'hq' =>  '',
        'associate_name'  => '',
        'employee_code'   =>  '',
        'uid_no'   =>  '',
        'designation' =>  '',
        'start_time'  =>  '',
        'pjp_time'  =>  '',
        'end_time'  =>  '',
        'before_10_30'  =>  '',
        'between_10_30_11_30'  =>  '',
        'after_11_30'  =>  '',
        'on_leave'     => '' ,
        'plan'         => '',
        'actual'       => '% PJP Adhered',
        'pjp_adhered' =>  round(($pjpCount/$totalCount) * 100, 2),
        'pjp_not_adhered' =>  round((($totalCount - $pjpCount) / $totalCount) * 100, 2),
        'gps'         =>  '',
        'battery'     =>  ''
      ];
    }

    $data[0][] = [
      'day'   =>  '',
      'date'  =>  '',
      'region'  =>  '',
      'asm_area' =>  '',
      'asm_name'  =>  '',
      'so_name' =>  '',
      'hq' =>  '',
      'associate_name'  => '',
      'employee_code'   =>  '',
      'uid_no'   =>  '',
      'designation' =>  '',
      'start_time'  =>  '',
      'pjp_time'  =>  '',
      'end_time'  =>  '',
      'before_10_30'  =>  '',
      'between_10_30_11_30'  =>  '',
      'after_11_30'  =>  '',
      'on_leave'     => '' ,
      'plan'         => '',
      'actual'       => 'Total PJP',
      'pjp_adhered' =>  $spjpCount,
      'pjp_not_adhered' =>  $stotalCount - $spjpCount,
      'gps'         =>  '',
      'battery'     =>  ''
    ];
    $data[0][] = [
      'day'   =>  '',
      'date'  =>  '',
      'region'  =>  '',
      'asm_area' =>  '',
      'asm_name'  =>  '',
      'so_name' =>  '',
      'hq' =>  '',
      'associate_name'  => '',
      'employee_code'   =>  '',
      'uid_no'   =>  '',
      'designation' =>  '',
      'start_time'  =>  '',
      'pjp_time'  =>  '',
      'end_time'  =>  '',
      'before_10_30'  =>  '',
      'between_10_30_11_30'  =>  '',
      'after_11_30'  =>  '',
      'on_leave'     => '' ,
      'plan'         => '',
      'actual'       => '% PJP Adhered',
      'pjp_adhered' =>  round(($spjpCount/$stotalCount) * 100, 2),
        'pjp_not_adhered' =>  round((($stotalCount - $spjpCount) / $stotalCount) * 100, 2),
      'gps'         =>  '',
      'battery'     =>  ''
    ];

  $count4 = $stotalCount - ($count1 + $count2 + $count3);
  $pcount1 = round($count1 * 100 / ($count1 + $count2 + $count3 + $count4) , 2);
  $pcount2 = round($count2 * 100 / ($count1 + $count2 + $count3 + $count4) , 2);
  $pcount3 = round($count3 * 100 / ($count1 + $count2 + $count3 + $count4) , 2);
  $pcount4 = round($count4 * 100 / ($count1 + $count2 + $count3 + $count4) , 2);

  return view('mails.rm', compact('plans', 'attendances', 'data', 'pcount1', 'pcount2', 'pcount3', 'pcount4'));
});


