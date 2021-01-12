<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Order;
use App\Target;
use Carbon\Carbon;

class GraphsController extends Controller
{
  public function __construct()
  {
    $this->middleware(['auth:api', 'company']);
  }

  public function masters(Request $request)
  {
    $request->validate([
      'month' =>  'required',
      'year'  =>  'required'
    ]);

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

    $years = ['2020', '2021'];

    $counts = $this->getCounts($request);

    $regionWiseSales = $this->getRegionWiseSales($request);

    $salePerformances = $this->getSalePerformances($request);
    
    $topSkus = $this->getTopSkus($request);

    $topPerformers = $this->getTopPerformers($request);

    $topOutlets = $this->getTopOutlets($request);

    return response()->json([
      'months'  =>  $months,
      'years'   =>  $years,
      'counts'  =>  $counts,
      'regionWiseSales'   =>  $regionWiseSales,
      'salePerformances'  =>  $salePerformances,
      'topSkus'           =>  $topSkus,
      'topPerformers'     =>  $topPerformers,
      'topOutlets'        =>  $topOutlets,
    ], 200);
  }

  public function getCounts(Request $request)
  {
    $orders = request()->company->orders_list()
      ->whereMonth('created_at', $request->month)
      ->whereYear('created_at', $request->year)
      ->get();

    $totalSales = 0;
    $saleValue = 0;
    $outletsCovered = 0;
    $productivity = 0;
    foreach ($orders as $order) {
      $totalSales++;
      $saleValue += $order->total;
      if($order->total != 0) {
        $outletsCovered++;
        $productivity++;
      } else {
        $outletsCovered++;
      }
    }
    $counts = [
      'total_sales'     =>  $totalSales,
      'sale_value'      =>  $saleValue,
      'outletsCovered'  =>  $productivity . '/' . $outletsCovered,
      'productivity'    =>  $outletsCovered == 0 ? 0 : round($productivity * 100 / $outletsCovered),
    ];

    return $counts;
  }

  public function getRegionWiseSales(Request $request)
  {
    $regionWiseSales = [];

    $targets = $request->company->targets()
      ->where('month', '=', $request->month)
      ->where('year', '=', $request->year)
      ->get();

    $orders = request()->company->orders_list()
      ->whereMonth('created_at', $request->month)
      ->whereYear('created_at', $request->year)
      ->get();

    // Get all skus
    $allSkus = [];
    $skus = request()->company->skus;
    foreach ($skus as $sku) {
      $allSkus[] = [
        'id'    =>  $sku->id,
        'name'  =>  $sku->name,
      ];
    }

    $regionWiseSales['allSkus'] = $allSkus;

    // North
    $users = request()->company->users()
      ->where('region', '=', 'NORTH')
      ->get();

    $regionWiseSales['north'] = $this->getSingleRegionUsersData($targets, $orders, $users, $skus);

    // East
    $users = request()->company->users()
      ->where('region', '=', 'EAST')
      ->get();

    $regionWiseSales['east'] = $this->getSingleRegionUsersData($targets, $orders, $users, $skus);

    // West
    $users = request()->company->users()
      ->where('region', '=', 'WEST')
      ->get();

    $regionWiseSales['west'] = $this->getSingleRegionUsersData($targets, $orders, $users, $skus);

    // South
    $users = request()->company->users()
      ->where('region', '=', 'SOUTH')
      ->get();

    $regionWiseSales['south'] = $this->getSingleRegionUsersData($targets, $orders, $users, $skus);

    // Central
    $users = request()->company->users()
      ->where('region', '=', 'Central')
      ->get();

    $regionWiseSales['central'] = $this->getSingleRegionUsersData($targets, $orders, $users, $skus);

    return $regionWiseSales;
  }

  public function getSingleRegionUsersData($targets, $orders, $users, $skus)
  {
    $singleRegionSales = [];
    // $regionWiseSales['north']['users'] = $users;
    $singleRegionSales['data'] = [];

    $Target = 0;
    $Achievement = 0;
    $PlanCalls = 0;
    $ActualCalls = 0;
    $ProductiveCalls = 0;

    foreach ($users as $user) {
      // To calculate the target
      foreach ($targets as $target) {
        if($target->user_id == $user->id) 
          $Target += $target->target;
      }
      $UserTarget = 0;
      $UserAchievement = 0;
      $UserPlanCalls = 0;
      $UserActualCalls = 0;
      $UserProductiveCalls = 0;
      $UserSkus = [];
      $reference_plan = null;

      foreach ($orders as $order) {
        if($order->user_id == $user->id) {
          // To get Actual and productive calls
          $Achievement += $order->total;
          $UserAchievement += $order->total;
          if($order->total != 0) {
            $ProductiveCalls++;
          }
          $ActualCalls++;
          $UserActualCalls++;
          // To get Plan calls
          foreach ($order->retailer->reference_plan->retailers as $retailer) {
            $PlanCalls++;
            $UserPlanCalls++;
          }
          // Get SKU Wise total sales of a user
          foreach ($skus as $sku) {
            $checkIfSkuThere = 0;
            foreach ($order->order_details as $order_detail) {
              if($order_detail->sku_id == $sku->id) {
                $checkIfSkuThere = 1;
                $checkIfAdded = 0;
                foreach ($UserSkus as $UserSku) {
                  if($UserSku['id'] == $order_detail->sku_id) {
                    $checkIfAdded = 1;
                    $UserSku['count'] += $order_detail->qty;
                  }
                }
                if($checkIfAdded == 0) {
                  $UserSkus[] = [
                    'id'    =>  $sku->id,
                    'name'  =>  $sku->name, 
                    'count' =>  $order_detail->qty,
                  ];
                }
              }
            }
            if($checkIfSkuThere == 0) {
              $checkIfAdded = 0;
              foreach ($UserSkus as $UserSku) {
                if($UserSku['id'] == $sku->id) {
                  $checkIfAdded = 1;
                }
              }
              if($checkIfAdded == 0) {
                $UserSkus[] = [
                  'id'    =>  $sku->id,
                  'name'  =>  $sku->name, 
                  'count' =>  0,
                ];
              }
            } 
          }
        }
        $reference_plan = $order->retailer->reference_plan;
      }
      // To get the complete data
      $singleRegionSales['data'][] = [
        'rsm'  =>  optional($user->rms)->name,
        'asm'  =>  optional($user->asm)->name,
        'area' =>  $reference_plan != null ? $reference_plan->town : '',
        'so'   =>  optional($user->so)->name,
        'ssm'  =>  $user->name,
        'so_hq'=>  optional($user->so)->address,
        'ssm_market'  =>  $reference_plan != null ? $reference_plan->name : '',
        'distributor' =>  optional($user->distributor)->name,
        'town'        =>  $reference_plan != null ? $reference_plan->town : '',
        'target'      =>  $UserTarget,
        'achievement' =>  $UserAchievement,
        'percent_achieved'  =>  $UserTarget == 0 ? 0 : round($UserAchievement * 100 / $UserTarget),
        'plan_calls'        =>  $UserPlanCalls,
        'actual_calls'      =>  $UserActualCalls,
        'productive_calls'  =>  $UserProductiveCalls,
        'percent_calls'     =>  $UserActualCalls == 0 ? 0 : round($UserProductiveCalls * 100 / $UserActualCalls),
        'skus'              =>  $UserSkus,
      ];
    }
    $singleRegionSales['target'] = $Target;
    $singleRegionSales['achievement'] = $Achievement;
    $singleRegionSales['percent_achieved'] = $Target == 0 ? 0 : round($Achievement * 100 / $Target);
    $singleRegionSales['plan_calls'] = $PlanCalls;
    $singleRegionSales['actual_calls'] = $ActualCalls;
    $singleRegionSales['productive_calls'] = $ProductiveCalls;
    $singleRegionSales['percent_calls'] = $ActualCalls == 0 ? 0 : round($ProductiveCalls * 100 / $ActualCalls);

    return $singleRegionSales;
  }

  public function getSalePerformances(Request $request)
  {
    $ordersLast2Month = request()->company->orders_list()
      ->whereMonth('created_at', $request->month - 1)
      ->whereYear('created_at', $request->year)
      ->get();

    $valueLast2Month = 0;
    foreach ($ordersLast2Month as $order) {
      $valueLast2Month += $order->total;
    }

    $ordersLast1Month = request()->company->orders_list()
      ->whereMonth('created_at', $request->month - 2)
      ->whereYear('created_at', $request->year)
      ->get();

    $valueLast1Month = 0;
    foreach ($ordersLast1Month as $order) {
      $valueLast1Month += $order->total;
    }

    $ordersThisMonth = request()->company->orders_list()
      ->whereMonth('created_at', $request->month)
      ->whereYear('created_at', $request->year)
      ->get();

    $valueThisMonth = 0;
    foreach ($ordersThisMonth as $order) {
      $valueThisMonth += $order->total;
    }

    $salePerformances = [
      [
        'month' =>  date("F", mktime(0, 0, 0, $request->month - 2, 10)),
        'value' =>  $valueLast2Month,
      ],
      [
        'month' =>  date("F", mktime(0, 0, 0, $request->month - 1, 10)),
        'value' =>  $valueLast1Month,
      ],
      [
        'month' =>  date("F", mktime(0, 0, 0, $request->month, 10)),
        'value' =>  $valueThisMonth,
      ]
    ];
    return $salePerformances;
  }

  public function getTopSkus(Request $request)
  {
    $skuDatas = [];
    $orders = request()->company->orders_list()
      ->whereMonth('created_at', $request->month)
      ->whereYear('created_at', $request->year)
      ->get();
    foreach ($orders as $order) {
      foreach ($order->order_details as $detail) {
        $isAdded = 0;
        for($i = 0; $i < sizeof($skuDatas); $i++) {
          if($skuDatas[$i]['sku_id'] == $detail->sku_id) {
            $skuDatas[$i]['value'] = $detail->value;
            $isAdded = 1;
          }
        }
        if($isAdded == 0) {
          $skuDatas[] = [
            'sku_id' => $detail->sku_id,
            'name'   => $detail->sku->name,
            'value'  => $detail->value,
          ];
        }
      }
    }

    for ($i=0; $i < sizeof($skuDatas); $i++) { 
      for ($j=$i + 1; $j < sizeof($skuDatas); $j++) { 
        if($skuDatas[$i]['value'] < $skuDatas[$j]['value']) {
          $temp = $skuDatas[$i];
          $skuDatas[$i] = $skuDatas[$j];
          $skuDatas[$j] = $temp;
        }
      }
    }

    // To get only 5 data
    $topSkus = [];
    for ($i=0; $i < (sizeof($skuDatas) <= 5 ? sizeof($skuDatas) : 5); $i++) { 
      $topSkus[] = $skuDatas[$i];
    }

    return $topSkus;
  }

  public function getTopPerformers(Request $request)
  {
    $usersData = [];
    $orders = request()->company->orders_list()
      ->whereMonth('created_at', $request->month)
      ->whereYear('created_at', $request->year)
      ->get();
    foreach ($orders as $order) {
      $target = Target::where('user_id', '=', $order->user_id)
      ->where('month', '=', $request->month)
      ->where('year', '=', $request->year)
      ->first();
      $isAdded = 0;

      for($i = 0; $i < sizeof($usersData); $i++) {
        if($usersData[$i]['user_id'] == $order->user_id) {
          $usersData[$i]['achieved'] += $order->total;
          if($order->total != 0) {
            $usersData[$i]['coverage']++;
          }
          $usersData[$i]['total']++;
          $usersData[$i]['productivity'] = round($usersData[$i]['coverage'] * 100 / $usersData[$i]['total']) . ' %';
          $isAdded = 1;
        }
      }
      if($isAdded == 0) {
        $usersData[] = [
          'user_id' => $order->user_id,
          'name'    => $order->user->name,
          'target'  => $target != null ? $target->target : 0,
          'achieved'=>  $order->total,
          'coverage'=>  1,
          'total'   =>  1,
          'productivity'  =>  $order->total == 0 ? 0 : 100,
        ];
      }
    }

    for ($i=0; $i < sizeof($usersData); $i++) { 
      for ($j=$i + 1; $j < sizeof($usersData); $j++) { 
        if($usersData[$i]['achieved'] < $usersData[$j]['achieved']) {
          $temp = $usersData[$i];
          $usersData[$i] = $usersData[$j];
          $usersData[$j] = $temp;
        }
      }
    }

    // To get only 5 data
    $topPerformers = [];
    for ($i=0; $i < (sizeof($usersData) <= 5 ? sizeof($usersData) : 5); $i++) { 
      $topPerformers[] = $usersData[$i];
    }

    return $topPerformers;
  }

  public function getTopOutlets(Request $request)
  {
    $outletsData = [];
    $orders = request()->company->orders_list()
      ->whereMonth('created_at', $request->month)
      ->whereYear('created_at', $request->year)
      ->get();
    foreach ($orders as $order) {
      $isAdded = 0;
      for($i = 0; $i < sizeof($outletsData); $i++) {
        if($outletsData[$i]['retailer_id'] == $order->retailer_id) {
          $outletsData[$i]['value'] += $order->total;
          $isAdded = 1;
        }
      }
      if($isAdded == 0) {
        $outletsData[] = [
          'retailer_id' => $order->retailer_id,
          'name'        => $order->retailer->name,
          'value'       =>  $order->total,
        ];
      }
    }

    for ($i=0; $i < sizeof($outletsData); $i++) { 
      for ($j=$i + 1; $j < sizeof($outletsData); $j++) { 
        if($outletsData[$i]['value'] < $outletsData[$j]['value']) {
          $temp = $outletsData[$i];
          $outletsData[$i] = $outletsData[$j];
          $outletsData[$j] = $temp;
        }
      }
    }

    // To get only 5 data
    $topOutlets = [];
    for ($i=0; $i < (sizeof($outletsData) <= 5 ? sizeof($outletsData) : 5); $i++) { 
      $topOutlets[] = $outletsData[$i];
    }

    return $topOutlets;
  }
}
