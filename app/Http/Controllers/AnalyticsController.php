<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\UserReferencePlan;
use App\Order;
use Carbon\Carbon;
use App\ReferencePlan;
use App\UserAttendance;
use App\Target;
use App\FocusedTarget;
use App\Sale;

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

    if ($request->userId && $request->weekNo && $request->day) {
      $user = User::find($request->userId);
      $whichWeek = $request->weekNo / 12;
      if ($user->beat_type_id == 1)
        $whichWeek = 1;
      else if ($user->beat_type_id == 2 && $whichWeek == 3)
        $whichWeek = 1;
      else if ($user->beat_type_id == 2 && $whichWeek == 4)
        $whichWeek = 2;
      $user_reference_plans = UserReferencePlan::where('user_id', '=', $user->id)
        ->where('day', '=', $request->day)
        ->where('which_week', '=', $whichWeek)
        ->get();
      foreach ($user_reference_plans as $user_reference_plan) {
        $referencePlanNames[] = $user_reference_plan->reference_plan->name;
        $totalOutlets = sizeof($user_reference_plan->reference_plan->retailers);
        foreach ($user_reference_plan->reference_plan->retailers as $retailer) {
          $orders = Order::where('retailer_id', '=', $retailer->id)
            ->whereDate('created_at', $request->date)
            ->where('order_type', '=', 'Sales')
            ->where('is_active', '=', 1)
            ->with('order_details')
            ->get();
          if (sizeof($orders) > 0) {
            $ordersTaken++;
            foreach ($orders as $order) {
              if (sizeof($order->order_details) > 0)
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
      ->where('order_type', '=', 'Sales')
      ->where('is_active', '=', 1)
      ->get();

    // Get target
    $current = Carbon::now();
    $currentMonth = $current->month;
    $currentYear = 2022;
    // $currentYear = $current->year;
    $target = Target::where('user_id', '=', $request->userId)
      ->where('month', '=', $currentMonth)
      ->where('year', '=', $currentYear)
      ->first();
    $target = $target ? $target->target : 0;
    $achieved = 0;
    $days = [];

    // Achieved of a month
    foreach ($ordersOfMonth as $order) {
      $achieved += $order->total;
    }

    // Datewise orders
    $daysInMonth = Carbon::createFromDate($request->month)->daysInMonth;
    for ($i = 1; $i <= $daysInMonth; $i++) {
      $date = 2022 . '-' . $request->month . '-' . sprintf("%02d", $i);
      $ordersOfADateTotal = 0;
      foreach ($ordersOfMonth as $order) {
        $orderDate = Carbon::parse($order->created_at)->format('Y-m-d');
        if ($orderDate == $date)
          $ordersOfADateTotal += $order->total;
      }

      if ($ordersOfADateTotal != 0)
        $days[] = [
          'date'    =>  $date,
          'achieved' => $ordersOfADateTotal,
        ];
      else {
        $days[] = [
          'date'    =>  $date,
          'achieved' => 0,
        ];
      }
    }

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

  public function supervisorKpiReport(Request $request)
  {
    $request->validate([
      'supervisorId'  =>  'required',
      'month'   =>  'required',
    ]);


    if ($request->supervisorId) {
      $totalTarget = 0;
      $totalAchieved = 0;
      $achievedDatas = [];
      $supervisorUsers = User::where('supervisor_id', '=', $request->supervisorId)
        ->get();

      foreach ($supervisorUsers as $supervisorUser) {
        // Total orders of a month
        $ordersOfMonth = Order::where('user_id', '=', $supervisorUser->id)
          ->whereMonth('created_at', $request->month)
          ->where('order_type', '=', 'Sales')
          ->where('is_active', '=', 1)
          ->get();

        // Get target
        $current = Carbon::now();
        $currentMonth = $current->month;
        $currentYear = 2022;
        // $currentYear = $current->year;
        $target = Target::where('user_id', '=', $supervisorUser->id)
          ->where('month', '=', $currentMonth)
          ->where('year', '=', $currentYear)
          ->first();
        $target = $target ? $target->target : 0;
        $totalTarget += $target;
        $achieved = 0;

        // Achieved of a month
        foreach ($ordersOfMonth as $order) {
          $achieved += $order->total;
          $totalAchieved += $order->total;
        }

        $achievedDatas[] = [
          'store_name'  =>  $supervisorUser->name,
          'target'  =>  $target,
          'achieved'  =>  $achieved,
          'percent' => $target == 0 ? 0 : ($achieved * 100) / $target,
        ];
      }

      array_unshift($achievedDatas, [
        'store_name'  =>  'Total Target',
        'target'  =>  $totalTarget,
        'achieved'  =>  $totalAchieved,
        'percent' => $totalTarget == 0 ? 0 : ($totalAchieved * 100) / $totalTarget,
      ]);
    }

    return response()->json([
      'data'    =>  $achievedDatas,
      'success' =>  true
    ]);
  }

  public function focussedKpiReport(Request $request)
  {
    $request->validate([
      'userId'  =>  'required',
      'month'   =>  'required',
    ]);

    $user = User::find($request->userId);


    // Get target
    $current = Carbon::now();
    $currentMonth = $current->month;
    $currentYear = 2022;
    // $currentYear = $current->year;
    $targets = FocusedTarget::where('user_id', '=', $request->userId)
      ->where('month', '=', $currentMonth)
      ->where('year', '=', $currentYear)
      ->get();
    foreach ($targets as $target) {
      $category = $target ? $target->category : '';
      $target = $target ? $target->target : 0;
      $achieved = 0;

      $searches = explode("_", $category);


      // Total orders of a month
      $ordersOfMonth = Order::where('user_id', '=', $request->userId)
        ->with('order_details')
        ->whereMonth('created_at', $request->month)
        ->where('order_type', '=', 'Sales')
        ->where('is_active', '=', 1)
        ->get();

      // Achieved of a month
      foreach ($ordersOfMonth as $order) {
        foreach ($order->order_details as $orderDetail) {
          foreach ($searches as $search) {
            if (str_contains($orderDetail->sku->name, strtoupper($search)) && $search != 'serum') {
              $achieved += $target < 100 ?  $orderDetail->qty : $orderDetail->value;
              // $achieved += $orderDetail->value;
            }
          }
        }
      }

      $achievedDatas[] = [
        'store_name'  =>  $user->name,
        'target'  =>  $target,
        'target_category' =>  $category,
        'achieved'  =>  $achieved,
        'percent' => $target == 0 ? 0 : ($achieved * 100) / $target,
      ];
    }
    $achievedDatas[] = [
      'store_name'  => '-',
      'target'  =>  '-',
      'target_category' =>  '-',
      'achieved'  =>  '-',
      'percent' =>  '-',
    ];

    return response()->json([
      'data'    =>  $achievedDatas,
      'success' =>  true
    ]);
  }

  public function supervisorFocussedKpiReport(Request $request)
  {
    $request->validate([
      'supervisorId'  =>  'required',
      'month'   =>  'required',
    ]);


    if ($request->supervisorId) {
      $achievedDatas = [];
      $supervisorUsers = User::where('supervisor_id', '=', $request->supervisorId)
        ->get();

      foreach ($supervisorUsers as $supervisorUser) {


        // Get target
        $current = Carbon::now();
        $currentMonth = $current->month;
        $currentYear = 2022;
        // $currentYear = $current->year;
        $targets = FocusedTarget::where('user_id', '=', $supervisorUser->id)
          ->where('month', '=', $currentMonth)
          ->where('year', '=', $currentYear)
          ->get();
        foreach ($targets as $target) {
          $category = $target ? $target->category : '';
          $target = $target ? $target->target : 0;
          $achieved = 0;

          $searches = explode("_", $category);


          // Total orders of a month
          $ordersOfMonth = Order::where('user_id', '=', $supervisorUser->id)
            ->with('order_details')
            ->whereMonth('created_at', $request->month)
            ->where('order_type', '=', 'Sales')
            ->where('is_active', '=', 1)
            ->get();

          // Achieved of a month
          foreach ($ordersOfMonth as $order) {
            foreach ($order->order_details as $orderDetail) {
              foreach ($searches as $search) {
                if (str_contains($orderDetail->sku->name, strtoupper($search))) {
                  $achieved += $target < 100 ?  $orderDetail->qty : $orderDetail->value;
                  // $achieved += $orderDetail->value;
                }
              }
            }
          }

          $achievedDatas[] = [
            'store_name'  =>  $supervisorUser->name,
            'target'  =>  $target,
            'target_category' =>  $category,
            'achieved'  =>  $achieved,
            'percent' => $target == 0 ? 0 : ($achieved * 100) / $target,
          ];
        }
        $achievedDatas[] = [
          'store_name'  => '-',
          'target'  =>  '-',
          'target_category' =>  '-',
          'achieved'  =>  '-',
          'percent'   =>  0
        ];
      }
    }

    return response()->json([
      'data'    =>  $achievedDatas,
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
      ->where('order_type', '=', 'Sales')
      ->where('is_active', '=', 1)
      ->get();

    // Get target
    $current = Carbon::now();
    $currentMonth = $current->month;
    // $currentYear = $current->year;
    $currentYear = 2022;
    $target = Target::where('user_id', '=', $request->userId)
      ->where('month', '=', $currentMonth)
      ->where('year', '=', $currentYear)
      ->first();
    $target = $target ? $target->target : 0;
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
          if ($order->retailer->id == $retailer->id) {
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
    for ($i = 0; $i < sizeof($outlets); $i++) {
      for ($j = $i + 1; $j < sizeof($outlets); $j++) {
        if ($outlets[$i]['achieved'] < $outlets[$j]['achieved']) {
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
      ->where('order_type', '=', 'Sales')
      ->where('is_active', '=', 1)
      ->get();

    if ($request->month == '01') {
      // Total orders of last month
      $ordersOfLastMonth = Order::where('user_id', '=', $request->userId)
        ->whereMonth('created_at', 10)
        ->where('order_type', '=', 'Sales')
        ->where('is_active', '=', 1)
        ->get();
    } else {
      // Total orders of last month
      $ordersOfLastMonth = Order::where('user_id', '=', $request->userId)
        ->whereMonth('created_at', 02)
        ->where('order_type', '=', 'Sales')
        ->where('is_active', '=', 1)
        ->get();
    }


    // Total orders of last 2 month
    $ordersOfLast2Month = Order::where('user_id', '=', $request->userId)
      ->whereMonth('created_at', 9)
      ->where('order_type', '=', 'Sales')
      ->where('is_active', '=', 1)
      ->get();

    $achieved = 0;
    $achievedLast = 0;
    $achievedLast2 = 0;
    $achievedLast3 = 0;
    $outlets = [];

    $beatIds = explode(',', $request->beatIds);

    // Reference Plans in this month
    $referencePlans = [];
    foreach ($beatIds as $beatId) {
      $beat = ReferencePlan::where('id', '=', $beatId)
        ->with('retailers')
        ->first();
      if ($beat)
        $referencePlans[] = $beat;
    }

    // return response()->json([
    //   'data'  =>  $referencePlans
    // ]);

    // Outlet wise total in this month
    foreach ($referencePlans as $referencePlan) {
      foreach ($referencePlan->retailers as $retailer) {
        $retailerTotal = 0;
        $retailerLastTotal = 0;
        foreach ($ordersOfMonth as $order) {
          if ($order->retailer->id == $retailer->id) {
            $retailerTotal += $order->total;
          }
        }
        foreach ($ordersOfLastMonth as $order) {
          if ($order->retailer->id == $retailer->id) {
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
    for ($i = 0; $i < sizeof($outlets); $i++) {
      for ($j = $i + 1; $j < sizeof($outlets); $j++) {
        if ($outlets[$i]['current_month'] < $outlets[$j]['current_month']) {
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
    // $targetLast = Target::where('user_id', '=', $request->userId)
    //   ->where('month', $request->month - 1)
    //   ->first();
    // if (isset($targetLast))
    //   $achievedLast = $targetLast->achieved;
    foreach ($ordersOfLastMonth as $order) {
      $achievedLast += $order->total;
    }
    // Total achieved in last 2 month
    $targetLast2 = Target::where('user_id', '=', $request->userId)
      ->where('month', $request->month - 2)
      ->first();
    if (isset($targetLast2))
      $achievedLast2 = $targetLast2->achieved;
    // foreach ($ordersOfLast2Month as $order) {
    //   $achievedLast2 += $order->total;
    // }
    // Total achieved in last 3 month
    $targetLast3 = Target::where('user_id', '=', $request->userId)
      ->where('month', 12)
      ->first();
    if (isset($targetLast3))
      $achievedLast3 = $targetLast3->achieved;

    $data = [
      'last_month'    =>  $achievedLast,
      'current_month' =>  $achieved,
      'outlets'       =>  $outlets,
      'months'        =>  [
        [
          'month' =>  $request->month != 3 ? date("F", mktime(0, 0, 0, $request->month - 3, 10)) : 'December',
          'value' =>  $achievedLast3,
        ],
        [
          'month' =>  $request->month != 2 ? date("F", mktime(0, 0, 0, $request->month - 2, 10)) : 'December',
          'value' =>  $achievedLast2,
        ],
        [
          'month' =>  $request->month != 1 ? date("F", mktime(0, 0, 0, $request->month - 1, 10)) : 'January',
          'value' =>  $achievedLast,
        ],
        [
          'month' =>  date("F", mktime(0, 0, 0, $request->month, 10)),
          'value' =>  $achieved,
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
      'userId'    =>  'required',
      'month'     =>  'required',
      'beatIds' =>  'required'
    ]);

    // Total orders of a month
    $ordersOfMonth = Order::where('user_id', '=', $request->userId)
      ->whereMonth('created_at', $request->month)
      ->where('order_type', '=', 'Sales')
      ->where('is_active', '=', 1)
      ->get();

    // Total orders of last month
    $ordersOfLastMonth = Order::where('user_id', '=', $request->userId)
      ->whereMonth('created_at', $request->month != 1 ? $request->month - 1 : 1)
      ->where('order_type', '=', 'Sales')
      ->where('is_active', '=', 1)
      ->get();

    // Total orders of last 2 month
    $ordersOfLast2Month = Order::where('user_id', '=', $request->userId)
      ->whereMonth('created_at', $request->month != 2 ? $request->month - 2 : 1)
      ->where('order_type', '=', 'Sales')
      ->where('is_active', '=', 1)
      ->get();

    // Get target
    $current = Carbon::now();
    $currentMonth = $current->month;
    $currentYear = 2022;
    // $currentYear = $current->year;
    $target = Target::where('user_id', '=', $request->userId)
      ->where('month', '=', $currentMonth)
      ->where('year', '=', $currentYear)
      ->first();
    $target = $target ? $target->target : 0;
    $achieved = 0;
    $achievedLast = 0;
    $achievedLast2 = 0;
    $outlets = [];

    $beatIds = explode(',', $request->beatIds);

    // Reference Plans in a month
    $referencePlans = [];
    foreach ($beatIds as $beatId) {
      $beat = ReferencePlan::where('id', '=', $beatId)
        ->with('retailers')
        ->first();
      if ($beat)
        $referencePlans[] = $beat;
    }

    // Outlet wise total in a month
    foreach ($referencePlans as $referencePlan) {
      foreach ($referencePlan->retailers as $retailer) {
        $retailerTotal = 0;
        $retailerNoOfInv = 0;
        foreach ($ordersOfMonth as $order) {
          if ($order->retailer->id == $retailer->id) {
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
    for ($i = 0; $i < sizeof($outlets); $i++) {
      for ($j = $i + 1; $j < sizeof($outlets); $j++) {
        if ($outlets[$i]['value'] < $outlets[$j]['value']) {
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
    // Total achieved in last 2 month
    foreach ($ordersOfLast2Month as $order) {
      $achievedLast2 += $order->total;
    }

    $data = [
      'outlets'   =>  $outlets,
      'months'        =>  [
        [
          'month' =>  $request->month != 2 ? date("F", mktime(0, 0, 0, $request->month - 2, 10)) : 'January',
          'value' =>  $achievedLast2,
        ],
        [
          'month' =>  $request->month != 1 ? date("F", mktime(0, 0, 0, $request->month - 1, 10)) : 'January',
          'value' =>  $achievedLast,
        ],
        [
          'month' =>  date("F", mktime(0, 0, 0, $request->month, 10)),
          'value' =>  $achieved,
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
    $sales = [];
    $orders = Order::where('user_id', '=', $request->userId)
      // ->where('status', '=', 1)
      ->whereMonth('created_at', $request->month)
      ->where('order_type', '=', 'Sales')
      ->where('is_active', '=', 1)
      ->latest()
      ->get();
    foreach ($orders as $order) {
      $sales[] = [
        'id'          =>  $order->id,
        'invoice_no'  =>  strval($order->id),
        // 'invoice_no'  =>  $order->invoice_no ?? '-',
        'outlet_name' =>  $order->retailer->name,
        'invoice_date' =>  Carbon::parse($order->created_at)->format('d-m-Y'),
        'value'       =>  $order->total,
        'status'      =>  $order->status,
      ];
      // $salesOfAnOrder = Sale::with('retailer')
      //   ->where('order_id', '=', $order->id)
      //   ->get();
      // foreach ($salesOfAnOrder as $sale) {
      //   $sales[] = [
      //     'invoice_no'  =>  $sale->invoice_no,
      //     'outlet_name' =>  $sale->retailer->name,
      //     'invoice_date'=>  Carbon::parse($sale->created_at)->format('d-m-Y'),
      //     'value'       =>  $sale->total_bill_value
      //   ];
      // }
    }

    $data = [
      'invoices' =>  $sales,
    ];


    // $data = [
    //   'invoices' =>  [
    //     [
    //       'invoice_no'  =>  1,
    //       'outlet_name' =>  'Outlet 1',
    //       'invoice_date'=>  '01-11-2020',
    //       'value'       =>  100,
    //     ],
    //   ],
    // ];

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

    $startDay = 1;

    $current = Carbon::now();
    $currentDay = $current->day;
    $currentMonth = $current->month;
    $currentYear = 2022;
    // $currentYear = $current->year;

    $user = User::find($request->userId);
    $doj = null;
    if ($user->doj != null) {
      $doj = Carbon::parse($user->doj);
      $dojDay = $doj->day;
      $dojMonth = $doj->month;
      $dojYear = $doj->year;
      if ($currentYear == $dojYear && $currentMonth == $dojMonth) {
        $startDay = $dojDay;
      }
    }

    // Attendances of current month
    $userAttendances = [];
    for ($i = $startDay; $i <= $currentDay; $i++) {
      $date = 2022 . '-' . $request->month . '-' . sprintf("%02d", $i);

      $userAttendance = UserAttendance::where('date', '=', $date)
        ->where('user_id', '=', $user->id)
        ->first();
      if ($userAttendance)
        $userAttendances[] = $userAttendance;
      else
        $userAttendances[] = [
          'date'        =>  $date,
          'login_time'  =>  null
        ];
    }

    // Attendances of current - 1 month
    // for ($i=1; $i <= 31; $i++) { 
    //   $date = 2021 . '-' . sprintf("%02d", $request->month - 1) . '-' . sprintf("%02d", $i);

    //   $userAttendance = UserAttendance::where('date', '=', $date)
    //     ->first();
    //   if($userAttendance)
    //     $userAttendances[] = $userAttendance;
    //   else
    //     $userAttendances[] = [
    //       'date'        =>  $date,
    //       'login_time'  =>  null
    //     ];
    // }

    // // Attendances of current - 2 month
    // for ($i=1; $i <= 31; $i++) { 
    //   $date = 2021 . '-' . sprintf("%02d", $request->month - 2) . '-' . sprintf("%02d", $i);

    //   $userAttendance = UserAttendance::where('date', '=', $date)
    //     ->first();
    //   if($userAttendance)
    //     $userAttendances[] = $userAttendance;
    //   else
    //     $userAttendances[] = [
    //       'date'        =>  $date,
    //       'login_time'  =>  null
    //     ];
    // }

    // // Attendances of current - 3 month
    // for ($i=1; $i <= 31; $i++) { 
    //   $date = 2021 . '-' . sprintf("%02d", $request->month - 3) . '-' . sprintf("%02d", $i);

    //   $userAttendance = UserAttendance::where('date', '=', $date)
    //     ->first();
    //   if($userAttendance)
    //     $userAttendances[] = $userAttendance;
    //   else
    //     $userAttendances[] = [
    //       'date'        =>  $date,
    //       'login_time'  =>  null
    //     ];
    // }

    // Attendances of current - 4 month
    // for ($i=1; $i <= 31; $i++) { 
    //   $date = 2021 . '-' . sprintf("%02d", $request->month - 3) . '-' . sprintf("%02d", $i);

    //   $userAttendance = UserAttendance::where('date', '=', $date)
    //     ->first();
    //   if($userAttendance)
    //     $userAttendances[] = $userAttendance;
    //   else
    //     $userAttendances[] = [
    //       'date'        =>  $date,
    //       'login_time'  =>  null
    //     ];
    // }

    $totalWorkingHrs = 0;
    $totalDays = 0;
    $present = 0;
    $absent = 0;
    $lessThan5hrs = 0;
    $greaterThan5hrs = 0;
    $attendances = [];

    foreach ($userAttendances as $userAttendance) {
      if ($userAttendance['login_time'] != null) {
        $startTime = Carbon::parse($userAttendance->login_time);
        $finishTime = Carbon::parse($userAttendance->logout_time);
        $totalDuration = round($finishTime->diffInSeconds($startTime) / (60 * 60));
        $totalWorkingHrs += $totalDuration;
        $totalDays++;
        $present++;
        // if($userAttendance->session_type == 'LEAVE') {
        //   $absent++;
        //   $attendances[] = [
        //     'date'    =>  $userAttendance['date'],
        //     'status'  =>  'Leave',
        //     'color'   =>  '#FFA500',
        //   ];
        // }
        // else 
        if ($totalDuration <= 8.5) {
          $lessThan5hrs++;
          $attendances[] = [
            'date'    =>  $userAttendance->date,
            // 'status'  =>  '<8.5 Hrs',
            'status'  =>  $userAttendance->session_type,
            'color'   =>  '#392897',
          ];
          $attendances[] = [
            'date'    =>  $userAttendance->date,
            // 'status'  =>  'IN ' . $userAttendance->login_time,
            'status'  =>  $userAttendance->login_time ?? '',
            'color'   =>  '#D67676',
          ];
          $attendances[] = [
            'date'    =>  $userAttendance->date,
            // 'status'  =>  'O ' . $userAttendance->logout_time,
            'status'  =>  $userAttendance->logout_time ?? '',
            'color'   =>  '#86A9DC',
          ];
        } else {
          $greaterThan5hrs++;
          $attendances[] = [
            'date'    =>  $userAttendance->date,
            // 'status'  =>  '>=8.5 Hrs',
            'status'  =>  $userAttendance->session_type,
            'color'   =>  '#392897',
            // 'color'   =>  '#108108',
          ];
          $attendances[] = [
            'date'    =>  $userAttendance->date,
            // 'status'  =>  'IN ' . $userAttendance->login_time,
            'status'  =>  $userAttendance->login_time ?? '',
            'color'   =>  '#D67676',
          ];
          $attendances[] = [
            'date'    =>  $userAttendance->date,
            // 'status'  =>  'O ' . $userAttendance->logout_time,
            'status'  =>  $userAttendance->logout_time ?? '',
            'color'   =>  '#86A9DC',
          ];
        }
      } else {
        $absent++;
        $attendances[] = [
          'date'    =>  $userAttendance['date'],
          'status'  =>  'ABSENT',
          'color'   =>  '#991111',
        ];
      }
    }

    $data = [
      'total_worked_hrs_day'  => $totalDays != 0 ? "$totalWorkingHrs / $totalDays" : '0',
      'avg_working_hrs'       => $totalDays != 0 ? $totalWorkingHrs / $totalDays . ' Hrs' : '0 Hrs',
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
