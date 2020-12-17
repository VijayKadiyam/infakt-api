<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\UserReferencePlan;
use App\Order;
use Carbon\Carbon;
use App\ReferencePlan;
use App\UserAttendance;

class AnalyticsController extends Controller
{
  public function __construct()
  {
    $this->middleware(['auth:api', 'company']);
  }

  public function daySummary(Request $request)
  {
    $request->validate([
      'userId'  =>  'required',
      'date'    =>  'required',
    ]);

    $referencePlanNames = [];
    $totalOutlets = 0;
    $ordersTaken = 0;
    $productiveOrders = 0;
    $ordersNotTaken = 0;
    $totalOrderValue = 0;

    if($request->userId && $request->weekNo && $request->day) {
      $user = User::find($request->userId);
      $whichWeek = $request->weekNo / 12;
      if($user->beat_type_id == 1)
        $whichWeek = 1;
      else if($user->beat_type_id == 2 && $whichWeek == 3)
        $whichWeek = 1;
      else if($user->beat_type_id == 2 && $whichWeek == 4)
        $whichWeek = 2;
      $user_reference_plans = UserReferencePlan::where('user_id', '=', $user->id)
        ->where('day', '=', $request->day)
        ->where('which_week', '=', $whichWeek)
        ->get();
      foreach($user_reference_plans as $user_reference_plan) {
        $referencePlanNames[] = $user_reference_plan->reference_plan->name;
        $totalOutlets = sizeof($user_reference_plan->reference_plan->retailers);
        foreach ($user_reference_plan->reference_plan->retailers as $retailer) {
          $orders = Order::where('retailer_id', '=', $retailer->id)
            ->whereDate('created_at', $request->date)
            ->with('order_details')
            ->get();
          if(sizeof($orders) > 0)  {
            $ordersTaken++;
            foreach ($orders as $order) {
              if(sizeof($order->order_details) > 0)
                $productiveOrders++;
              $totalOrderValue += $order->total;
            }
          }
        }
      }
    }

    $ordersNotTaken = $totalOutlets - $ordersTaken;

    $data = [
      'beat_names'      => $referencePlanNames,
      'total_outlets'   =>  $totalOutlets,
      'orders_not_taken'  =>  $ordersNotTaken,
      'orders_taken'      =>  $ordersTaken, 
      'total_order_value' =>  $totalOrderValue,
      'coverage'          =>  [
        'percent' =>  $totalOutlets != 0 ? round(($ordersTaken * 100) / $totalOutlets) : 0,
        'value'   =>  $ordersTaken,
        'total'   =>  $totalOutlets
      ],
      'productivity'  =>  [
        'percent' =>  $ordersTaken != 0 ? round(($productiveOrders * 100) / $ordersTaken) : 0,
        'value'   =>  $productiveOrders,
        'total'   =>  $ordersTaken
      ]
    ];

    return response()->json([
      'data'    =>  $data,
      'success' =>  true
    ]);
  }

  public function kpiReport(Request $request)
  {
    $request->validate([
      'userId'  =>  'required',
      'month'   =>  'required',
    ]);

    // Total orders of a month
    $ordersOfMonth = Order::where('user_id', '=', $request->userId)
      ->whereMonth('created_at', $request->month)
      ->get();

    $target = 0;
    $achieved = 0;
    $days = [];

    // Achieved of a month
    foreach ($ordersOfMonth as $order) {
      $achieved += $order->total;
    }

    // Datewise orders
    $daysInMonth = Carbon::createFromDate($request->month)->daysInMonth;
    for ($i=1; $i <= $daysInMonth; $i++) { 
      $date = 2020 . '-' . $request->month . '-' . sprintf("%02d", $i);
      $ordersOfADateTotal = 0;
      foreach ($ordersOfMonth as $order) {
        $orderDate = Carbon::parse($order->created_at)->format('Y-m-d');
        if($orderDate == $date)
          $ordersOfADateTotal += $order->total;
      }

      if($ordersOfADateTotal != 0)
        $days[] = [
          'date'    =>  $date,
          'achieved' => $ordersOfADateTotal,
        ];
    }
    $target = 2 * $achieved;

    $data = [
      'target'    =>  $target,
      'achieved'  =>  $achieved,
      'percent'   =>  $target != 0 ? round($achieved * 100 / $target) : 0,
      'days'      =>  $days,
    ];

    return response()->json([
      'data'    =>  $data,
      'success' =>  true
    ]);
  }

  public function targetVsAchieved(Request $request)
  {
    $request->validate([
      'userId'    =>  'required',
      'month'     =>  'required',
      'beatIds' =>  'required'
    ]);

    // Total orders of a month
    $ordersOfMonth = Order::where('user_id', '=', $request->userId)
      ->whereMonth('created_at', $request->month)
      ->get();

    $target = 0;
    $achieved = 0;
    $outlets = [];

    $beatIds = explode(',', $request->beatIds);

    // Reference Plans in a month
    $referencePlans = [];
    foreach ($beatIds as $beatId) {
      $beat = ReferencePlan::where('id', '=', $beatId)
        ->with('retailers')
        ->first();
      $referencePlans[] = $beat;
    }

    // Outlet wise total in a month
    foreach ($referencePlans as $referencePlan) {
      foreach ($referencePlan->retailers as $retailer) {
        $retailerTotal = 0;
        foreach ($ordersOfMonth as $order) {
          if($order->retailer->id == $retailer->id) {
            $retailerTotal += $order->total;
          }
        }
        $outlets[] = [
          'outlet'    =>  $retailer->name,
          'achieved'  =>  $retailerTotal,
        ];
      }
    }

    // Outlets in ascending order
    for ($i=0; $i < sizeof($outlets); $i++) { 
      for ($j=$i + 1; $j < sizeof($outlets); $j++) { 
        if($outlets[$i]['achieved'] < $outlets[$j]['achieved']) {
          $temp = $outlets[$i];
          $outlets[$i] = $outlets[$j];
          $outlets[$j] = $temp;
        }
      }
    }
    // Total achieved in a month
    foreach ($ordersOfMonth as $order) {
      $achieved += $order->total;
    }
    $target = 2 * $achieved;

    $data = [
      'target'    =>  $target,
      'achieved'  =>  $achieved,
      'percent'   =>  $target != 0 ? round($achieved * 100 / $target) : 0,
      'outlets'   =>  $outlets
    ];

    return response()->json([
      'data'    =>  $data,
      'success' =>  true
    ]);
  }

  public function salesTrend(Request $request)
  {
    $request->validate([
      'userId'    =>  'required',
      'month'     =>  'required',
      'beatIds' =>  'required'
    ]);

    // Total orders of this month
    $ordersOfMonth = Order::where('user_id', '=', $request->userId)
      ->whereMonth('created_at', $request->month)
      ->get();

    // Total orders of last month
    $ordersOfLastMonth = Order::where('user_id', '=', $request->userId)
      ->whereMonth('created_at', $request->month != 1 ? $request->month - 1 : 1)
      ->get();

    $achieved = 0;
    $achievedLast = 0;
    $outlets = [];

    $beatIds = explode(',', $request->beatIds);

    // Reference Plans in this month
    $referencePlans = [];
    foreach ($beatIds as $beatId) {
      $beat = ReferencePlan::where('id', '=', $beatId)
        ->with('retailers')
        ->first();
      $referencePlans[] = $beat;
    }

    // Outlet wise total in this month
    foreach ($referencePlans as $referencePlan) {
      foreach ($referencePlan->retailers as $retailer) {
        $retailerTotal = 0;
        $retailerLastTotal = 0;
        foreach ($ordersOfMonth as $order) {
          if($order->retailer->id == $retailer->id) {
            $retailerTotal += $order->total;
          }
        }
        foreach ($ordersOfLastMonth as $order) {
          if($order->retailer->id == $retailer->id) {
            $retailerLastTotal += $order->total;
          }
        }
        $outlets[] = [
          'outlet'        =>  $retailer->name,
          'current_month' =>  $retailerTotal,
          'last_month'    =>  $retailerLastTotal,  
        ];
      }
    }

    // Outlets in ascending order
    for ($i=0; $i < sizeof($outlets); $i++) { 
      for ($j=$i + 1; $j < sizeof($outlets); $j++) { 
        if($outlets[$i]['current_month'] < $outlets[$j]['current_month']) {
          $temp = $outlets[$i];
          $outlets[$i] = $outlets[$j];
          $outlets[$j] = $temp;
        }
      }
    }
    // Total achieved in a month
    foreach ($ordersOfMonth as $order) {
      $achieved += $order->total;
    }
    // Total achieved in last month
    foreach ($ordersOfLastMonth as $order) {
      $achievedLast += $order->total;
    }

    $data = [
      'last_month'    =>  $achievedLast,
      'current_month' =>  $achieved,
      'outlets'       =>  $outlets,
      'months'        =>  [
        [
          'month' =>  $request->month != 1 ? strval($request->month - 1) : '1',
          'value' =>  $achieved,
        ],
        [
          'month' =>  strval($request->month),
          'value' =>  $achievedLast,
        ],
      ]
    ];

    // $data = [
    //   'last_month'    =>  2000,
    //   'current_month' =>  3000,
    //   'outlets' =>  [
    //     0 =>  [
    //       'outlet'  =>  'OUTLET 1',
    //       'last_month'    =>  200,
    //       'current_month' =>  100,
    //     ],
    //   ],
    //   'months'  =>  [
    //     [
    //       'month' =>  'January',
    //       'value' =>  1000,
    //     ],
    //   ]
    // ];

    return response()->json([
      'data'    =>  $data,
      'success' =>  true
    ]);
  }

  public function billedOutlet(Request $request)
  {
    $request->validate([
      'userId'    =>  'required',
      'month'     =>  'required',
      'beatIds' =>  'required'
    ]);

    // Total orders of a month
    $ordersOfMonth = Order::where('user_id', '=', $request->userId)
      ->whereMonth('created_at', $request->month)
      ->get();

    // Total orders of last month
    $ordersOfLastMonth = Order::where('user_id', '=', $request->userId)
      ->whereMonth('created_at', $request->month != 1 ? $request->month - 1 : 1)
      ->get();

    $achieved = 0;
    $achievedLast = 0;
    $outlets = [];

    $beatIds = explode(',', $request->beatIds);

    // Reference Plans in a month
    $referencePlans = [];
    foreach ($beatIds as $beatId) {
      $beat = ReferencePlan::where('id', '=', $beatId)
        ->with('retailers')
        ->first();
      $referencePlans[] = $beat;
    }

    // Outlet wise total in a month
    foreach ($referencePlans as $referencePlan) {
      foreach ($referencePlan->retailers as $retailer) {
        $retailerTotal = 0;
        $retailerNoOfInv = 0;
        foreach ($ordersOfMonth as $order) {
          if($order->retailer->id == $retailer->id) {
            $retailerTotal += $order->total;
            $retailerNoOfInv++;
          }
        }
        $outlets[] = [
          'outlet'    =>  $retailer->name,
          'no_of_inv' =>  $retailerNoOfInv,
          'value'     =>  $retailerTotal,
        ];
      }
    }

    // Outlets in ascending order
    for ($i=0; $i < sizeof($outlets); $i++) { 
      for ($j=$i + 1; $j < sizeof($outlets); $j++) { 
        if($outlets[$i]['value'] < $outlets[$j]['value']) {
          $temp = $outlets[$i];
          $outlets[$i] = $outlets[$j];
          $outlets[$j] = $temp;
        }
      }
    }
    // Total achieved in a month
    foreach ($ordersOfMonth as $order) {
      $achieved += $order->total;
    }
    // Total achieved in last month
    foreach ($ordersOfLastMonth as $order) {
      $achievedLast += $order->total;
    }
    $target = 2 * $achieved;

    $data = [
      'outlets'   =>  $outlets,
      'months'        =>  [
        [
          'month' =>  $request->month != 1 ? strval($request->month - 1) : '1',
          'value' =>  $achieved,
        ],
        [
          'month' =>  strval($request->month),
          'value' =>  $achievedLast,
        ],
      ]
    ];

    // $data = [
    //   'outlets' =>  [
    //     0 =>  [
    //       'outlet'    =>  'OUTLET 1',
    //       'no_of_inv' =>  2,
    //       'value'     =>  100,
    //     ],
    //     1 =>  [
    //       'outlet'    =>  'OUTLET 2',
    //       'no_of_inv' =>  3,
    //       'value'   =>  101,
    //     ],
    //   ],
    //   'months'  =>  [
    //     [
    //       'month' =>  'January',
    //       'value' =>  1000,
    //     ],
    //     [
    //       'month' =>  'February',
    //       'value' =>  2000,
    //     ],
    //     [
    //       'month' =>  'March',
    //       'value' =>  3000,
    //     ],
    //     [
    //       'month' =>  'April',
    //       'value' =>  4000,
    //     ],
    //     [
    //       'month' =>  'May',
    //       'value' =>  5000,
    //     ],
    //   ]
    // ];

    return response()->json([
      'data'    =>  $data,
      'success' =>  true
    ]);
  }

  public function invoiceDetail(Request $request)
  {
    $request->validate([
      'userId'  =>  'required',
      'month'   =>  'required',
    ]);

    $data = [
      'invoices' =>  [
        [
          'invoice_no'  =>  1,
          'outlet_name' =>  'Outlet 1',
          'invoice_date'=>  '01-11-2020',
          'value'       =>  100,
        ],
      ],
    ];

    return response()->json([
      'data'    =>  $data,
      'success' =>  true
    ]);
  }

  public function attendanceCalendar(Request $request)
  {
    $request->validate([
      'userId'  =>  'required',
      'month'   =>  'required',
    ]);

    $daysInMonth = Carbon::createFromDate($request->month)->daysInMonth;
    // Attendances of a month
    $userAttendances = [];
    for ($i=1; $i <= $daysInMonth; $i++) { 
      $date = 2020 . '-' . $request->month . '-' . sprintf("%02d", $i);

      $userAttendance = UserAttendance::where('date', '=', $date)
        ->first();
      if($userAttendance)
        $userAttendances[] = $userAttendance;
      else
        $userAttendances[] = [
          'date'        =>  $date,
          'login_time'  =>  null
        ];
    }

    $totalWorkingHrs = 0;
    $totalDays = 0;
    $present = 0;
    $absent = 0;
    $lessThan5hrs = 0;
    $greaterThan5hrs = 0;
    $attendances = [];
    foreach ($userAttendances as $userAttendance) {
      if($userAttendance['login_time'] != null) {
        $startTime = Carbon::parse($userAttendance->login_time);
        $finishTime = Carbon::parse($userAttendance->logout_time);
        $totalDuration = round($finishTime->diffInSeconds($startTime) / (60 * 60));
        $totalWorkingHrs += $totalDuration;
        $totalDays++;
        $present++;
        if($totalDuration < 5){
          $lessThan5hrs++;
          $attendances[] = [
            'date'    =>  $userAttendance->date,
            'status'  =>  '<5 Hrs',
            'color'   =>  '#0000FF',
          ];
        }
        else {
          $greaterThan5hrs++;
          $attendances[] = [
            'date'    =>  $userAttendance->date,
            'status'  =>  '>=5 Hrs',
            'color'   =>  '#00FF00',
          ];
        }
      } else {
        $absent++;
        $attendances[] = [
          'date'    =>  $userAttendance['date'],
          'status'  =>  'Absent',
          'color'   =>  '#FF0000',
        ];
      }
    }

    $data = [
      'total_worked_hrs_day'  =>  "$totalWorkingHrs / $totalDays",
      'avg_working_hrs'       =>  $totalWorkingHrs / $totalDays . ' Hrs',
      'present'               =>  "$present Days",
      'absent'                =>  "$absent Days",
      'less_than_5_hrs'       =>  "$lessThan5hrs Days",
      'greater_than_5_hrs'    =>  "$greaterThan5hrs Days",
      'attendances'           =>  $attendances,
    ];

    return response()->json([
      'data'    =>  $data,
      'success' =>  true
    ]);
  }
}
