<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\User;
use App\UserReferencePlan;
use App\Order;

class DashboardsController extends Controller
{
  public function __construct()
  {
    $this->middleware(['auth:api', 'company']);
  }

  public function ssmDashboard(Request $request)
  {
    $orders = [];
    if($request->userId && $request->date) {
      $orders = request()->company->orders_list()
        ->where('user_id', '=', $request->userId)
        ->whereDate('created_at', $request->date)
        ->get();

      $referencePlanNames = [];
      $totalOutlets = 0;
      $ordersTaken = 0;
      $productiveOrders = 0;
      $ordersNotTaken = 0;
      $totalOrderValue = 0;

      $day = Carbon::parse($request->date)->format('l');
      $day = ($day == 'Monday') ? 
        1 : ($day == 'Tuesday' ?
          2 : ($day == 'Wednesday' ?
            3 : ($day == 'Thursday' ? 
              4 : ($day == 'Friday' ? 
                5 : ($day == 'Saturday' ? 6 : 7)))));

      $weekNo = Carbon::parse($request->date)->weekOfYear;

      if($request->userId && $weekNo && $day) {
        $user = User::find($request->userId);
        $whichWeek = $weekNo / 12;
        if($user->beat_type_id == 1)
          $whichWeek = 1;
        else if($user->beat_type_id == 2 && $whichWeek == 3)
          $whichWeek = 1;
        else if($user->beat_type_id == 2 && $whichWeek == 4)
          $whichWeek = 2;
        $user_reference_plans = UserReferencePlan::where('user_id', '=', $user->id)
          ->where('day', '=', $day)
          ->where('which_week', '=', $whichWeek)
          ->get();
        foreach($user_reference_plans as $user_reference_plan) {
          $referencePlanNames[] = $user_reference_plan->reference_plan->name;
          $totalOutlets = sizeof($user_reference_plan->reference_plan->retailers);
          foreach ($user_reference_plan->reference_plan->retailers as $retailer) {
            $ors = Order::where('retailer_id', '=', $retailer->id)
              ->whereDate('created_at', $request->date)
              ->with('order_details')
              ->get();
            if(sizeof($ors) > 0)  {
              $ordersTaken++;
              foreach ($ors as $order) {
                if(sizeof($order->order_details) > 0)
                  $productiveOrders++;
                $totalOrderValue += $order->total;
              }
            }
          }
        }
      }

      $ordersNotTaken = $totalOutlets - $ordersTaken;

      $userAttendance = request()->company->user_attendances()
        ->where('date', '=', $request->date)
        ->where('user_id', '=', $request->userId)
        ->first();

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
        ],
        'user_attendance'  =>  $userAttendance
      ];
    }

    return response()->json([
      'data'    =>  $data,
      'orders'  =>  $orders,
      'success' =>  true
    ], 200);
  }

  public function soDashboard(Request $request)
  {
    $orders = [];
    if($request->userId && $request->month) {
      $user = User::find($request->userId);
      $users = request()->company->users()
        ->where('so_id', '=', $request->userId)
        ->get();

      $ors = [];
      foreach ($users as $user) {
        $ors = request()->company->orders_list()
          ->where('user_id', '=', 3)
          ->whereMonth('created_at', $request->month)
          ->get();
      }
      foreach ($ors as $or) {
        $orders[] = $or;
      }
    }

    $data = [
      'orders'  =>  $orders,
      'today'   =>  [
        'plan_ssm'        =>  10,
        'actual_ssm'      =>  10,
        'percent_ssm'     =>  '100 %',
        'plan_calls'      =>  20,
        'actual_calls'    =>  20,
        'percent_calls'   =>  '100 %',
        'productive_calls'=> 20,
        'percent_productive'  =>  '100 %',
        'no_of_lines'     =>  5,
        'no_of_lines_sold'=>  5,
        'percent_line_sold' =>  '100 %',
        'target'          =>  1000,
        'achieved'        =>  900,
        'percent_achieved'=>  '90 %'
      ],
      'monthly'   =>  [
        'plan_ssm'        =>  10,
        'actual_ssm'      =>  10,
        'percent_ssm'     =>  '100 %',
        'plan_calls'      =>  20,
        'actual_calls'    =>  20,
        'percent_calls'   =>  '100 %',
        'productive_calls'=> 20,
        'percent_productive'  =>  '100 %',
        'no_of_lines'     =>  5,
        'no_of_lines_sold'=>  5,
        'percent_line_sold' =>  '100 %',
        'target'          =>  1000,
        'achieved'        =>  900,
        'percent_achieved'=>  '90 %'
      ],
      'annual'   =>  [
        'plan_ssm'        =>  10,
        'actual_ssm'      =>  10,
        'percent_ssm'     =>  '100 %',
        'plan_calls'      =>  20,
        'actual_calls'    =>  20,
        'percent_calls'   =>  '100 %',
        'productive_calls'=> 20,
        'percent_productive'  =>  '100 %',
        'no_of_lines'     =>  5,
        'no_of_lines_sold'=>  5,
        'percent_line_sold' =>  '100 %',
        'target'          =>  1000,
        'achieved'        =>  900,
        'percent_achieved'=>  '90 %'
      ]
    ];

    return response()->json([
      'data'    =>  $data,
      'success' =>  true
    ], 200);
  }
}
