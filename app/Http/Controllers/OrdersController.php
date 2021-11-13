<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Order;
use App\OrderDetail;
use App\User;
use Carbon\Carbon;

class OrdersController extends Controller
{
  public function __construct()
  {
    $this->middleware(['auth:api', 'company'])
      ->except(['generateInvoice']);
  }

  public function index(Request $request)
  {
    $count = 0;
    if ($request->userId && $request->date) {
      $orders = request()->company->orders_list()
        ->where('user_id', '=', $request->userId)
        ->whereDate('created_at', $request->date)
        ->get();
    } else if (request()->page && request()->rowsPerPage) {
      $orders = request()->company->orders_list();
      $count = $orders->count();
      $orders = $orders->paginate(request()->rowsPerPage)->toArray();
      $orders = $orders['data'];
    } else if (request()->page && request()->rowsPerPage && $request->distributorId) {
      $orders = request()->company->orders_list()
        ->where('distributor_id', '=', $request->distributorId);
      $count = $orders->count();
      $orders = $orders->paginate(request()->rowsPerPage)->toArray();
      $orders = $orders['data'];
    } else {
      $orders = request()->company->orders_list;
      $count = $orders->count();
    }

    return response()->json([
      'data'     =>  $orders,
      'count'    =>   $count,
      'success'   =>  true
    ], 200);
  }

  public function store(Request $request)
  {
    $request->validate([
      'distributor_id'  =>  'required',
      'user_id'         =>  'required',
      'retailer_id'     =>  'required',
      'status'          =>  'required',
      'order_details.*.sku_id'  =>  'required',
      'order_details.*.qty'     =>  'required',
      'order_details.*.value'   =>  'required',
    ]);

    if ($request->id == null || $request->id == '') {
      // Save Order
      $order = new Order($request->all());
      $request->company->orders_list()->save($order);

      // Save Order Details
      if (isset($request->order_details))
        foreach ($request->order_details as $detail) {
          $order_detail = new OrderDetail($detail);
          $order->order_details()->save($order_detail);
        }

      // ---------------------------------------------------
    } else {
      // Update Order
      $order = Order::find($request->id);
      $order->update($request->all());

      // Check if Order Detail deleted
      if (isset($request->order_details))
        $orderDetailIdResponseArray = array_pluck($request->order_details, 'id');
      else
        $orderDetailIdResponseArray = [];
      $orderId = $order->id;
      $orderDetailIdArray = array_pluck(OrderDetail::where('order_id', '=', $orderId)->get(), 'id');
      $differenceOrderDetailIds = array_diff($orderDetailIdArray, $orderDetailIdResponseArray);
      // Delete which is there in the database but not in the response
      if ($differenceOrderDetailIds)
        foreach ($differenceOrderDetailIds as $differenceOrderDetailId) {
          $orderDetail = OrderDetail::find($differenceOrderDetailId);
          $orderDetail->delete();
        }

      // Update Order Details
      if (isset($request->order_details))
        foreach ($request->order_details as $detail) {
          if (!isset($detail['id'])) {
            $order_detail = new OrderDetail($detail);
            $order->order_details()->save($order_detail);
          } else {
            $order_detail = OrderDetail::find($detail['id']);
            $order_detail->update($detail);
          }
        }

      // ---------------------------------------------------
    }

    $order->order_details = $order->order_details;

    return response()->json([
      'data'    =>  $order,
      'success' =>  true
    ], 201);
  }

  public function show(Order $order)
  {
    return response()->json([
      'data'   =>  $order,
      'success' =>  true
    ], 200);
  }

  public function update(Request $request, Order $order)
  {
    $order->update($request->all());

    return response()->json([
      'data'  =>  $order
    ], 200);
  }

  public function deleteMultipleOrders()
  {
    $orders = Order::whereDate('created_at', '<=', '2021-10-31')
      ->delete();

    $order_details = OrderDetail::whereDate('created_at', '<=', '2021-10-31')
      ->delete();

    return response()->json([
      'orders'  =>  $orders,
      'order_details'  =>  $order_details,
    ]);
  }

  public function deleteOrder($id)
  {
    $order = Order::where('id', '=', $id)->first();
    $order->is_active = 0;
    $order->update();

    if ($order) {
      $orderDetails = OrderDetail::where('order_id', '=', $order->id)->get();
      foreach ($orderDetails as $orderDetail) {
        $order_Detail = OrderDetail::where('id', '=', $orderDetail->id)->first();
        $order_Detail->is_active = 0;
        $order_Detail->update();
      }
    }
    return response()->json([
      'data'  =>  $order
    ], 200);
  }

  public function deleteOrderDetail($id)
  {
    $orderDetails = OrderDetail::where('id', '=', $id)->first();
    $orderDetails->is_active = 0;
    $orderDetails->update();


    return response()->json([
      'data'  =>  $orderDetails
    ], 200);
  }

  public function generateInvoice(Request $request)
  {
    $orderId = $request->orderId;

    $order = Order::where('id', '=', $orderId)
      ->first();
    $order->status = 1;
    $order->update();

    dd($order->toArray());
  }

  public function offtakes(Request $request)
  {
    $count = 0;
    $orders = [];
    if ($request->userId) {
      $orders = request()->company->orders_list()
        ->where('user_id', '=', $request->userId)
        ->where('is_active', '=', 1);
      if ($request->date) {
        $orders = $orders->whereDate('created_at', $request->date);
      }
      if ($request->month) {
        $orders = $orders->whereMonth('created_at', '=', $request->month);
      }
      if ($request->year) {
        $orders = $orders->whereYear('created_at', '=', $request->year);
      }

      $orders = $orders->get();
    } else {
      $supervisors = User::with('roles')
        ->whereHas('roles',  function ($q) {
          $q->where('name', '=', 'SUPERVISOR');
        })->orderBy('name')->get();

      foreach ($supervisors as $supervisor) {

        $users = User::where('supervisor_id', '=', $supervisor->id)->get();

        foreach ($users as $user) {

          $ors = request()->company->orders_list()
            ->where('user_id', '=', $user->id)
            ->where('is_active', '=', 1)
            ->with('order_details')
            ->whereHas('order_details',  function ($q) {
              $q->groupBy('sku_id');
            });
          if ($request->date) {
            $ors = $ors->whereDate('created_at', $request->date);
          }
          if ($request->month) {
            $ors = $ors->whereMonth('created_at', '=', $request->month);
          }
          if ($request->year) {
            $ors = $ors->whereYear('created_at', '=', $request->year);
          }
          $ors = $ors->get();
          if (count($ors) != 0) {
            foreach ($ors as $order)
              $orders[] = $order;
          }
        }
      }
    }

    if($request->raw == 'YES') {
      return response()->json([
        'count'    =>   sizeof($orders),
        'data'     =>  $orders,
        'success'   =>  true
      ], 200);
    }

    // Once we have list of all the orders
    // retailer_id
    $finalOrders = [];

    for ($i = 1; $i <= 31; $i++) {
      // To check single day orders
      $ordersOfADay = [];
      foreach ($orders as $or) {
        // var_dump(Carbon::parse($or->created_at)->format('d'));
        if (Carbon::parse($or->created_at)->format('d') == sprintf("%02d", $i)) {
          $ordersOfADay[] = $or;
        }
      }
      // End To check single day orders

      if (sizeof($ordersOfADay) > 0) {
        $singleDaySalesOrders = [];
        $singleDayStockReceived = [];
        $singleDayStockReturned = [];

        $salesOrder = [
          'order_details' => [],
        ];
        $stockReceived = [
          'order_details' => [],
        ];
        $stockReturned = [
          'order_details' => [],
        ];

        $salesOrdersOfAllRetailersOfADay = [];
        $stockReceivedOfAllRetailersOfADay = [];
        $stockReturnedOfAllRetailersOfADay = [];

        foreach ($ordersOfADay as $order) {

          // Sales
          // Check if this date and this store is already there in the singleDaySalesOrders
          if ($order->order_type == 'Sales') {

            for ($k = 0; $k < sizeof($salesOrdersOfAllRetailersOfADay); $k++) {
              if ($salesOrdersOfAllRetailersOfADay[$k]['user_id'] == $order->user_id)
                $salesOrder = $salesOrdersOfAllRetailersOfADay[$k];
            }

            foreach ($order->order_details as $orderDetail) {
              $salesOrder['id'] = $order->id;
              $salesOrder['distributor_id'] = $order->distributor_id;
              $salesOrder['retailer_id'] = $order->retailer_id;
              $salesOrder['user_id'] = $order->user_id;
              $salesOrder['status'] = $order->status;
              $salesOrder['order_type'] = $order->order_type;
              $salesOrder['created_at'] = Carbon::parse($order->created_at)->format('d-m-Y');
              $salesOrder['user'] = $order->user;
              $orderDetailOfSkuAlreadyThere = false;
              for ($k = 0; $k < sizeof($salesOrder['order_details']); $k++) {
                if ($salesOrder['order_details'][$k]['sku_id'] == $orderDetail['sku_id']) {
                  // var_dump(1234);
                  $orderDetailOfSkuAlreadyThere = true;
                  $salesOrder['order_details'][$k]['qty'] += $orderDetail['qty'];
                  $salesOrder['order_details'][$k]['value'] += $orderDetail['value'];
                }
              }
              if (!$orderDetailOfSkuAlreadyThere)
                $salesOrder['order_details'][]  = $orderDetail;
            }
            // End Foreach order_details

            $isSalesOrderOfSingleRetailersOfADay = 0;
            for ($j = 0; $j < sizeof($salesOrdersOfAllRetailersOfADay); $j++) {
              if ($salesOrdersOfAllRetailersOfADay[$j]['user_id'] == $salesOrder['user_id']) {
                $salesOrdersOfAllRetailersOfADay[$j] = $salesOrder;
                $isSalesOrderOfSingleRetailersOfADay = 1;
              }
            }
            if ($isSalesOrderOfSingleRetailersOfADay == 0)
              $salesOrdersOfAllRetailersOfADay[] = $salesOrder;
            $salesOrder = [
              'order_details' => [],
            ];
          }
          // End Sales


          // // Stock Received
          // // Check if this date and this store is already there in the singleDayStockReceived
          // if($order->order_type == 'Stock Received') {

          //   foreach($stockReceivedOfAllRetailersOfADay as $stockReceivedOfSingleRetailerOfADay) {
          //     if($stockReceivedOfSingleRetailerOfADay['retailer_id'] == $order->retailer_id) 
          //       $stockReceived = $stockReceivedOfSingleRetailerOfADay;
          //   }

          //   // End singleDayStockReceived Foreach
          //   foreach($order->order_details as $orderDetail) {
          //     $stockReceived['id'] = $order->id;
          //     $stockReceived['distributor_id'] = $order->distributor_id;
          //     $stockReceived['retailer_id'] = $order->retailer_id;
          //     $stockReceived['user_id'] = $order->user_id;
          //     $stockReceived['status'] = $order->status;
          //     $stockReceived['order_type'] = $order->order_type;
          //     $stockReceived['created_at'] = Carbon::parse($order->created_at)->format('d-m-Y');
          //     $stockReceived['user'] = $order->user;
          //     $orderDetailOfSkuAlreadyThere = false;
          //     foreach($stockReceived['order_details'] as $stockRecDetail) {
          //       if($stockRecDetail['sku_id'] == $orderDetail['sku_id']) {
          //         $orderDetailOfSkuAlreadyThere = true;
          //         $stockRecDetail['qty'] += $orderDetail['qty'];
          //         $stockRecDetail['value'] += $orderDetail['value'];
          //       }
          //     }
          //     if(!$orderDetailOfSkuAlreadyThere) 
          //       $stockReceived['order_details'][]  = $orderDetail;
          //   }
          //   // End Foreach order_details

          //   $isStockReceivedOfSingleRetailersOfADay = 0;
          //   foreach($stockReceivedOfAllRetailersOfADay as $stockReceivedOfSingleRetailerOfADay) {
          //     if($stockReceivedOfSingleRetailerOfADay['retailer_id'] == $stockReceived['retailer_id']) {
          //       $stockReceivedOfSingleRetailerOfADay = $stockReceived;
          //       $isStockReceivedOfSingleRetailersOfADay = 1;
          //     }
          //   }
          //   if($isStockReceivedOfSingleRetailersOfADay == 0) 
          //     $stockReceivedOfSingleRetailerOfADay[] = $stockReceived;

          // }
          // // End Stock Received

          // // Stock Returned
          // // Check if this date and this store is already there in the singleDayStockReturned
          // if($order->order_type == 'Stock Returned') {
          //   foreach($singleDayStockReturned as $singleDayStockRet) {
          //     if($singleDayStockRet['retailer_id'] == $order->retailer_id && $singleDayStockRet['order_type'] == 'Stock Returned')
          //       $stockReturned = $singleDayStockRet;
          //   }
          //   // End singleDayStockReturned Foreach
          //   foreach($order->order_details as $orderDetail) {
          //     $stockReturned['id'] = $order->id;
          //     $stockReturned['distributor_id'] = $order->distributor_id;
          //     $stockReturned['retailer_id'] = $order->retailer_id;
          //     $stockReturned['user_id'] = $order->user_id;
          //     $stockReturned['status'] = $order->status;
          //     $stockReturned['order_type'] = $order->order_type;
          //     $stockReturned['created_at'] = Carbon::parse($order->created_at)->format('d-m-Y');
          //     $stockReturned['user'] = $order->user;
          //     $orderDetailOfSkuAlreadyThere = false;
          //     foreach($stockReturned['order_details'] as $stockRetDetail) {
          //       if($stockRetDetail['sku_id'] == $orderDetail['sku_id']) {
          //         $orderDetailOfSkuAlreadyThere = true;
          //         $stockRetDetail['qty'] += $orderDetail['qty'];
          //         $stockRetDetail['value'] += $orderDetail['value'];
          //       }
          //     }
          //     if(!$orderDetailOfSkuAlreadyThere) 
          //       $stockReturned['order_details'][]  = $orderDetail;
          //   }
          //   // End Foreach order_details
          // }
          // // End Stock Returned

        }
        // End $orders of a day Foreach

        foreach ($salesOrdersOfAllRetailersOfADay as $salesOrderOfSingleRetailerOfADay) {
          if (sizeof(($salesOrderOfSingleRetailerOfADay['order_details'])) > 0)
            $finalOrders[] = $salesOrderOfSingleRetailerOfADay;
        }

        // foreach($stockReceivedOfAllRetailersOfADay as $stockReceivedOfSingleRetailerOfADay) {
        //   if(sizeof(($stockReceivedOfSingleRetailerOfADay['order_details'])) > 0)
        //     $finalOrders[] = $stockReceivedOfSingleRetailerOfADay;
        // }
        // $stockReceived = [
        //   'order_details' => [],
        // ];
        // if(sizeof(($stockReceived['order_details'])) > 0)
        //   $finalOrders[] = $stockReceived;

        // if(sizeof(($stockReturned['order_details'])) > 0)
        //   $finalOrders[] = $stockReturned;
        // $stockReturned = [
        //   'order_details' => [],
        // ];
      }
    }


    // Old code

    // return $ors->get();

    // if($request->userId && $request->date) {
    //   $orders = request()->company->orders_list()
    //     ->where('user_id', '=', $request->userId)
    //     ->whereDate('created_at', $request->date)
    //     ->get();
    // }
    // else if(request()->page && request()->rowsPerPage) {
    //   $orders = request()->company->orders_list();
    //   $count = $orders->count();
    //   $orders = $orders->paginate(request()->rowsPerPage)->toArray();
    //   $orders = $orders['data'];
    // } 
    // else if(request()->page && request()->rowsPerPage && $request->distributorId) {
    //   $orders = request()->company->orders_list()
    //     ->where('distributor_id', '=', $request->distributorId);
    //   $count = $orders->count();
    //   $orders = $orders->paginate(request()->rowsPerPage)->toArray();
    //   $orders = $orders['data'];
    // } else {
    //   $orders = request()->company->orders_list; 
    //   $count = $orders->count();
    // }

    return response()->json([
      'count'    =>   sizeof($finalOrders),
      'data'     =>  $finalOrders,
      'success'   =>  true
    ], 200);
  }

  public function daily_offtake_counts(Request $request)
  {
    $count = 0;
    $orders = [];
    if ($request->userId) {
      $orders = request()->company->orders_list()
        ->where('user_id', '=', $request->userId);
      // ->where('is_active', '=', 1);
      if ($request->date) {
        $orders = $orders->whereDate('created_at', $request->date);
      }
      if ($request->month) {
        $orders = $orders->whereMonth('created_at', '=', $request->month);
      }
      if ($request->year) {
        $orders = $orders->whereYear('created_at', '=', $request->year);
      }

      $orders = $orders->get();
    } else {
      $supervisors = User::with('roles')
        ->whereHas('roles',  function ($q) {
          $q->where('name', '=', 'SUPERVISOR');
        })->orderBy('name')->get();
      $Oftake_users = [];
      foreach ($supervisors as $supervisor) {

        $users = User::where('supervisor_id', '=', $supervisor->id)->get();
        $offtake_count = 0;
        foreach ($users as $user) {

          $ors = request()->company->orders_list()
            ->where('user_id', '=', $user->id);
          // ->where('is_active', '=', 1)
          // ->with('order_details')
          // ->whereHas('order_details',  function ($q) {
          //   $q->groupBy('sku_id');
          // });
          if ($request->month) {
            $ors = $ors->whereMonth('created_at', '=', $request->month);
          }
          if ($request->year) {
            $ors = $ors->whereYear('created_at', '=', $request->year);
          }
          // $ors = $ors->groupBy(function ($date) {
          //   return Carbon::parse($date->created_at)->format('D'); // grouping by years
          // })->get();
          $ors = $ors->get();
          $order_date_list = [];
          if (count($ors) != 0) {
            foreach ($ors as $key => $order) {
              $order_date_list[] = Carbon::parse($order->created_at)->format('d');
            }
            $array = array_unique($order_date_list);
            $offtake_count = sizeof($array);
          }

          $user['Offtake_count'] = $offtake_count;
          $Oftake_users[] = $user;
        }
      }
    }

    return response()->json([
      'data'     =>  $Oftake_users,
      'success'   =>  true
    ], 200);
  }
}
