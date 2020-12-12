<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\UserReferencePlan;
use App\Order;
use Carbon\Carbon;

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
            ->get();
          if(sizeof($orders) > 0)  {
            $ordersTaken++;
            foreach ($orders as $order) {
              $totalOrderValue += $order->total;
            }
          }
        }
      }
    }

    $ordersNotTaken = $totalOutlets - $ordersTaken;

    $data = [
      'beat_names' => $referencePlanNames,
      'total_outlets'  =>  $totalOutlets,
      'orders_not_taken'  =>  $ordersNotTaken,
      'orders_taken'      =>  $ordersTaken, 
      'total_order_value' =>  $totalOrderValue,
      'coverage'          =>  [
        'percent' =>  round(($ordersTaken * 100) / $totalOutlets),
        'value'   =>  $ordersTaken,
        'total'   =>  $totalOutlets
      ],
      'productivity'  =>  [
        'percent' =>  round(($ordersTaken * 100) / $ordersTaken),
        'value'   =>  $ordersTaken,
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

    $ordersOfMonth = Order::where('user_id', '=', $request->userId)
      ->whereMonth('created_at', $request->month)
      ->get();

    $target = 0;
    $achieved = 0;
    $days = [];

    foreach ($ordersOfMonth as $order) {
      $achieved += $order->total;
    }

    $daysInMonth = Carbon::createFromDate($request->month)->daysInMonth;
    for ($i=1; $i <= $daysInMonth; $i++) { 
      $date = 2020 . '-' . $request->month . '-' . $i;
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
      'percent'   =>  round($achieved * 100 / $target),
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
      'userId'  =>  'required',
      'month'   =>  'required',
    ]);

    $ordersOfMonth = Order::where('user_id', '=', $request->userId)
      ->whereMonth('created_at', $request->month)
      ->get();

    $target = 0;
    $achieved = 0;
    $outlets = [];
    $days = [];

    foreach ($ordersOfMonth as $order) {
      $achieved += $order->total;
      $outlets[] = [
        'outlet'    =>  $order->retailer->name,
        'achieved'  =>  $order->total,
      ];
    }
    $target = 2 * $achieved;

    $data = [
      'target'    =>  $target,
      'achieved'  =>  $achieved,
      'percent'   =>  round($achieved * 100 / $target),
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
      'userId'  =>  'required',
      'month'   =>  'required',
    ]);

    $data = [
      'last_month'    =>  2000,
      'current_month' =>  3000,
      'outlets' =>  [
        0 =>  [
          'outlet'  =>  'OUTLET 1',
          'last_month'    =>  200,
          'current_month' =>  100,
        ],
        1 =>  [
          'outlet'  =>  'OUTLET 2',
          'last_month'    =>  201,
          'current_month' =>  101,
        ],
      ],
      'months'  =>  [
        [
          'month' =>  'January',
          'value' =>  1000,
        ],
        [
          'month' =>  'February',
          'value' =>  2000,
        ],
        [
          'month' =>  'March',
          'value' =>  3000,
        ],
        [
          'month' =>  'April',
          'value' =>  4000,
        ],
        [
          'month' =>  'May',
          'value' =>  5000,
        ],
      ]
    ];

    return response()->json([
      'data'    =>  $data,
      'success' =>  true
    ]);
  }

  public function billedOutlet(Request $request)
  {
    $request->validate([
      'userId'  =>  'required',
      'month'   =>  'required',
    ]);

    $data = [
      'outlets' =>  [
        0 =>  [
          'outlet'    =>  'OUTLET 1',
          'no_of_inv' =>  2,
          'value'     =>  100,
        ],
        1 =>  [
          'outlet'    =>  'OUTLET 2',
          'no_of_inv' =>  3,
          'value'   =>  101,
        ],
      ],
      'months'  =>  [
        [
          'month' =>  'January',
          'value' =>  1000,
        ],
        [
          'month' =>  'February',
          'value' =>  2000,
        ],
        [
          'month' =>  'March',
          'value' =>  3000,
        ],
        [
          'month' =>  'April',
          'value' =>  4000,
        ],
        [
          'month' =>  'May',
          'value' =>  5000,
        ],
      ]
    ];

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

    $data = [
      'total_worked_hrs_day'  =>  '23:30:20 / 10',
      'avg_working_hrs'       =>  '02:33:22 Hrs',
      'present'               =>  '10 Days',
      'absent'                =>  '3 Days',
      'less_than_5_hrs'       =>  '2 Days',
      'greater_than_5_hrs'    =>  '3 Days',
      'attendances'  =>  [
        [
          'date'    =>  '2020-12-08',
          'status'  =>  '>=5 Hrs',
          'color'   =>  '#00FF00',
        ],
        [
          'date'    =>  '2020-12-09',
          'status'  =>  '<5 Hrs',
          'color'   =>  '#0000FF',
        ],
        [
          'date'    =>  '2020-12-10',
          'status'  =>  'Absent',
          'color'   =>  '#FF0000',
        ]
      ]
    ];

    return response()->json([
      'data'    =>  $data,
      'success' =>  true
    ]);
  }
}
