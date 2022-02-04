<?php

namespace App\Http\Controllers;

use App\Order;
use Illuminate\Http\Request;
use App\Sku;
use App\Stock;
use App\User;
use Carbon\Carbon;

class StocksController extends Controller
{
  public function __construct()
  {
    $this->middleware(['auth:api', 'company']);
  }

  public function masters()
  {
    $skusController = new SkusController();
    $skusResponse = $skusController->index($request);

    return response()->json([
      'skus'  =>  $skusResponse->getData()->data,
    ]);
  }

  public function all()
  {
    // return request()->all();
    $count = 0;
    if (request()->page && request()->rowsPerPage) {
      $stocks = Stock::with('sku', 'unit', 'distributor', 'offer');
      $count = $stocks->count();
      $stocks = $stocks->paginate(request()->rowsPerPage)->toArray();
      $stocks = $stocks['data'];
    } else {
      $stocks = Stock::with('sku', 'unit', 'distributor', 'offer');
      $count = $stocks->count();
    }

    return response()->json([
      'data'     =>  $stocks,
      'count'     => $count
    ], 200);
  }

  // public function closing_stocks()
  // {
  //   $now = Carbon::now()->format('Y-m-d'); //Closing Stock For todays Log
  //   $count = 0;
  //   if (request()->page && request()->rowsPerPage) {
  //     $stocks = Stock::whereDate('created_at', $now)->with('sku', 'unit', 'distributor', 'offer');
  //     $count = $stocks->count();
  //     $stocks = $stocks->paginate(request()->rowsPerPage)->toArray();
  //     $stocks = $stocks['data'];
  //   } else {
  //     $stocks = Stock::with('sku', 'unit', 'distributor', 'offer');
  //     $count = $stocks->count();
  //   }

  //   return response()->json([
  //     'data'     =>  $stocks,
  //     'count'     => $count
  //   ], 200);
  // }
  public function closing_stocks(Request $request)
  {
    $asd = [];

    // $now = Carbon::now()->format('Y-m-d'); //Closing Stock For todays Log
    $count = 0;
    if (request()->page && request()->rowsPerPage) {
      $skus = request()->company->skus();

      $count = $skus->count();
      $skus = $skus->paginate(request()->rowsPerPage)->toArray();
      $skus = $skus['data'];
    } else if (request()->search) {
      $skus = request()->company->skus()
        ->where('name', 'LIKE', '%' . $request->search . '%')
        ->get();
    } else {
      $skus = request()->company->skus;
      $count = $skus->count();
    }


    $users = $request->company->users();

    if ($request->user_id) {
      $users = $users->where('users.id', '=', $request->user_id);
    }
    
    $users = $users->with('roles')
      ->whereHas('roles',  function ($q) {
        $q->where('name', '!=', 'Admin');
      });

    $users = $users->get();
    foreach ($users as $key => $user) {

      if ($user) {

        $stocks = [];
        if ($user)
          $stocks = Stock::whereYear('created_at', Carbon::now())
            ->whereMonth('created_at', Carbon::now())
            // ->where('distributor_id', '=', 3050)
            ->where('distributor_id', '=', $user->distributor_id)
            ->latest()->get();

        // return $stocks;
        $orders = [];
        if ($user)
          $orders = Order::whereYear('created_at', Carbon::now())
            // ->whereMonth('created_at', Carbon::now())
            // ->where('distributor_id', '=', 3050)
            ->where('distributor_id', '=', $user->distributor_id)
            ->latest()->get();
        // return $orders;
        foreach ($skus as $sku) {
          // return $sku['price'];
          $sku['mrp_price'] = $sku['price'];
          // return $sku['mrp_price'];
          $skuStocks = [];
          foreach ($stocks as $stock) {
            if ($sku['id'] == $stock['sku_id'])
              $skuStocks[] = $stock;
          }
          // $sku['price'] = sizeof($skuStocks) > 0 ? $skuStocks[0]['price'] : 0;
          $sku['offer_price'] = null;
          if (sizeof($skuStocks) > 0) {
            // $sku['price'] = $skuStocks[0]['price'];
            if ($sku['offer_id'] != null) {
              if ($sku['offer']['offer_type']['name'] == 'FLAT') {
                $sku['offer_price'] = $sku['price'] - $sku['offer']['offer'];
              }
              if ($sku['offer']['offer_type']['name'] == 'PERCENT') {
                $sku['offer_price'] = $sku['price'] - ($sku['price'] * $sku['offer']['offer'] / 100);
              }
            }
          }
          $totalQty = 0;
          foreach ($skuStocks as $stock) {
            $totalQty += $stock->qty;
          }
          $receivedQty = 0;
          $purchaseReturnedQty = 0;
          $consumedQty = 0;
          $returnedQty = 0;

          foreach ($orders as $order) {
            // return $order;
            $todayDate = Carbon::now()->format('d-m-Y');
            // $todayDate = '19-01-2022';
            // return $todayDate;
            // $orderDate = '19-01-2022';
            $orderDate = Carbon::parse($order->created_at)->format('d-m-Y');
            if ($orderDate != $todayDate) {
              foreach ($order->order_details as $detail) {
                if ($detail->sku_id == $sku['id'] && $order->order_type == 'Opening Stock')
                  $totalQty += $detail->qty;
                if ($detail->sku_id == $sku['id'] && $order->order_type == 'Stock Received')
                  $totalQty += $detail->qty;
                if ($detail->sku_id == $sku['id'] && $order->order_type == 'Purchase Returned')
                  $totalQty -= $detail->qty;
                if ($detail->sku_id == $sku['id'] && $order->order_type == 'Sales')
                  $totalQty -= $detail->qty;
                if ($detail->sku_id == $sku['id'] && $order->order_type == 'Stock Returned')
                  $totalQty += $detail->qty;
              }
            } else {
              foreach ($order->order_details as $detail) {
                // return $order->order_type;
                if ($detail->sku_id == $sku['id'] && $order->order_type == 'Opening Stock')
                  $totalQty += $detail->qty;
                if ($detail->sku_id == $sku['id'] && $order->order_type == 'Stock Received')
                  $receivedQty += $detail->qty;
                if ($detail->sku_id == $sku['id'] && $order->order_type == 'Purchase Returned')
                  $purchaseReturnedQty += $detail->qty;
                if ($detail->sku_id == $sku['id'] && $order->order_type == 'Sales')
                  $consumedQty += $detail->qty;
                if ($detail->sku_id == $sku['id'] && $order->order_type == 'Stock Returned')
                  $returnedQty += $detail->qty;
              }
            }
          }

          $sku['qty'] = ($totalQty + $receivedQty - $purchaseReturnedQty - $consumedQty + $returnedQty);

          $sku['opening_stock'] = $totalQty;
          $sku['received_stock'] = $receivedQty;
          $sku['purchase_returned_stock'] = $purchaseReturnedQty;
          $sku['sales_stock'] = $consumedQty;
          $sku['returned_stock'] = $returnedQty;
          $sku['closing_stock'] = ($totalQty + $receivedQty - $purchaseReturnedQty - $consumedQty + $returnedQty);
          $sku['user'] = $user;
          $asd[] = $sku;
        }
      }
    }
    $skus = $asd;
    // return $skus;
    for ($i = 0; $i < sizeof($skus); $i++) {
      for ($j = $i; $j < sizeof($skus); $j++) {
        if ($skus[$i]['qty'] < $skus[$j]['qty']) {
          $temp = $skus[$i];
          $skus[$i] = $skus[$j];
          $skus[$j] = $temp;
        }
      }
    }


    return response()->json([
      'data'     =>  $skus,
      'count'    =>   $count,
      'success' =>  true,
    ], 200);
  }

  /*
   * To get all stocks of a sku
     *
   *@
   */
  public function index(Sku $skus)
  {
    $stocks = $skus->stocks;

    return response()->json([
      'data'     =>  $stocks
    ], 200);
  }

  /*
   * To store a new stock
   *
   *@
   */
  public function store(Request $request, Sku $skus)
  {
    $request->validate([
      'qty'    =>  'required',
      'sku_type_id'    =>  'required',
      'offer_id'  =>  'required',
      'price' =>  'required'
    ]);

    $stock = new Stock($request->all());
    $skus->stocks()->save($stock);

    return response()->json([
      'data'    =>  $stock
    ], 201);
  }

  /*
   * To view a single stock
   *
   *@
   */
  public function show(Sku $skus, Stock $stock)
  {
    return response()->json([
      'data'   =>  $stock
    ], 200);
  }

  /*
   * To update a stock
   *
   *@
   */
  public function update(Request $request, Sku $skus, Stock $stock)
  {
    $request->validate([
      'qty'  =>  'required',
    ]);

    $stock->update($request->all());

    return response()->json([
      'data'  =>  $stock
    ], 200);
  }
}
