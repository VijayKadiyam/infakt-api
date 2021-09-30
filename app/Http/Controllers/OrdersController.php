<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Order;
use App\OrderDetail;
use App\User;

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
    if($request->userId && $request->date) {
      $orders = request()->company->orders_list()
        ->where('user_id', '=', $request->userId)
        ->whereDate('created_at', $request->date)
        ->get();
    }
    else if(request()->page && request()->rowsPerPage) {
      $orders = request()->company->orders_list();
      $count = $orders->count();
      $orders = $orders->paginate(request()->rowsPerPage)->toArray();
      $orders = $orders['data'];
    } 
    else if(request()->page && request()->rowsPerPage && $request->distributorId) {
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

    if($request->id == null || $request->id == '') {
      // Save Order
      $order = new Order($request->all());
      $request->company->orders_list()->save($order);

      // Save Order Details
      if(isset($request->order_details))
        foreach($request->order_details as $detail) {
          $order_detail = new OrderDetail($detail);
          $order->order_details()->save($order_detail);
        }

      // ---------------------------------------------------
    } else {
      // Update Order
      $order = Order::find($request->id);
      $order->update($request->all());

      // Check if Order Detail deleted
      if(isset($request->order_details))
        $orderDetailIdResponseArray = array_pluck($request->order_details , 'id');
      else
        $orderDetailIdResponseArray = [];
      $orderId = $order->id;
      $orderDetailIdArray = array_pluck(OrderDetail::where('order_id','=',$orderId)->get(),'id');
      $differenceOrderDetailIds = array_diff($orderDetailIdArray, $orderDetailIdResponseArray);
      // Delete which is there in the database but not in the response
      if($differenceOrderDetailIds)
        foreach($differenceOrderDetailIds as $differenceOrderDetailId)
        {
          $orderDetail = OrderDetail::find($differenceOrderDetailId);
          $orderDetail->delete();
        }

      // Update Order Details
      if(isset($request->order_details))
        foreach($request->order_details as $detail) {
          if(!isset($detail['id'])) {
            $order_detail = new OrderDetail($detail);
            $order->order_details()->save($order_detail);
          }
          else {
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
    if($request->userId) {
      $orders = request()->company->orders_list()
        ->where('user_id', '=', $request->userId);

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
            ->where('user_id', '=', $user->id);

          if ($request->date) {
            $ors = $ors->whereDate('created_at', $request->date);
          }
          if ($request->month) {
            $ors = $ors->whereMonth('date', '=', $request->month);
          }
          if ($request->year) {
            $ors = $ors->whereYear('date', '=', $request->year);
          }
          $ors = $ors->get();
          if (count($ors) != 0) {
            foreach ($ors as $order)
              $orders[] = $order;
          }
        }
      }
    }
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
      'data'     =>  $orders,
      'count'    =>   $count,
      'success'   =>  true
    ], 200);
  }
}
