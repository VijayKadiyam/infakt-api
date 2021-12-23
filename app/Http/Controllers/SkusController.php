<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Sku;
use App\Product;
use App\Stock;
use App\User;
use App\Order;
use Carbon\Carbon;
use App\UserReferencePlan;

class SkusController extends Controller
{
  public function __construct()
  {
    $this->middleware(['auth:api', 'company']);
  }

  public function masters(Request $request)
  {
    $offersController = new OffersController();
    $offersResponse = $offersController->index($request);

    return response()->json([
      'offers'                 =>  $offersResponse->getData()->data,
    ], 200);
  }

  public function getAll()
  {
    // $products = request()->company->products;

    // $skus = [];
    // foreach($products as $product) {
    //   $productSkus = $product->skus;
    //   foreach($productSkus as $productSku) {
    //     $skus[] = $productSku;
    //   }
    // }

    // return response()->json([
    //   'data'     =>  $skus,
    //   'success'   =>  true
    // ], 200);

    $count = 0;
    if(request()->page && request()->rowsPerPage) {
      $skus = request()->company->skus();
      $count = $skus->count();
      $skus = $skus->paginate(request()->rowsPerPage)->toArray();
      $skus = $skus['data'];
    } else {
      $skus = request()->company->skus; 
      $count = $skus->count();
    }

    return response()->json([
      'data'     =>  $skus,
      'count'    =>   $count
    ], 200);
  }

  /*
   * To get all skus
     *
   *@
   */
  public function index(Request $request, Product $product)
  {
    $count = 0;
    if(request()->page && request()->rowsPerPage) {
      $skus = request()->company->skus();
      $count = $skus->count();
      $skus = $skus->paginate(request()->rowsPerPage)->toArray();
      $skus = $skus['data'];
    } 
    else if(request()->search) {
      $skus = request()->company->skus()
        ->where('name', 'LIKE', '%' . $request->search . '%')
        ->get();
    }
    else {
      $skus = request()->company->skus; 
      $count = $skus->count();
    }

    $user = User::find($request->userId);
    if($user) {
      
      $stocks = [];
      if($user)
        $stocks = Stock::whereYear('created_at', Carbon::now())
          ->whereMonth('created_at', Carbon::now())
          ->where('distributor_id', '=', $user->distributor_id)
          ->latest()->get();

      $orders = [];
      if($user)
        $orders = Order::whereYear('created_at', Carbon::now())
          // ->whereMonth('created_at', Carbon::now())
          ->where('distributor_id', '=', $user->distributor_id)
          ->latest()->get();

      foreach ($skus as $sku) {
        $sku['mrp_price'] = $sku->price;
        $skuStocks = [];
        foreach ($stocks as $stock) {
          if($sku['id'] == $stock['sku_id']) 
            $skuStocks[] = $stock;
        }
        // $sku['price'] = sizeof($skuStocks) > 0 ? $skuStocks[0]['price'] : 0;
        $sku['offer_price'] = null;
        if(sizeof($skuStocks) > 0) {
          // $sku['price'] = $skuStocks[0]['price'];
          if($sku['offer_id'] != null) {
            if($sku['offer']['offer_type']['name'] == 'FLAT') {
              $sku['offer_price'] = $sku['price'] - $sku['offer']['offer'];
            }
            if($sku['offer']['offer_type']['name'] == 'PERCENT') {
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
          $todayDate = Carbon::now()->format('d-m-Y');
          $orderDate = Carbon::parse($order->created_at)->format('d-m-Y');
          if($orderDate != $todayDate) {
            foreach ($order->order_details as $detail) {
              if($detail->sku_id == $sku->id && $order->order_type == 'Opening Stock') 
                $totalQty += $detail->qty;
              if($detail->sku_id == $sku->id && $order->order_type == 'Stock Received') 
                $totalQty += $detail->qty;
              if($detail->sku_id == $sku->id && $order->order_type == 'Purchase Returned') 
                $totalQty -= $detail->qty;
              if($detail->sku_id == $sku->id && $order->order_type == 'Sales') 
                $totalQty -= $detail->qty;
              if($detail->sku_id == $sku->id && $order->order_type == 'Stock Returned') 
                $totalQty += $detail->qty;
            }
          } else {
            foreach ($order->order_details as $detail) {
              if($detail->sku_id == $sku->id && $order->order_type == 'Opening Stock') 
                $totalQty += $detail->qty;
              if($detail->sku_id == $sku->id && $order->order_type == 'Stock Received') 
                $receivedQty += $detail->qty;
              if($detail->sku_id == $sku->id && $order->order_type == 'Purchase Returned') 
                $purchaseReturnedQty += $detail->qty;
              if($detail->sku_id == $sku->id && $order->order_type == 'Sales') 
                $consumedQty += $detail->qty;
              if($detail->sku_id == $sku->id && $order->order_type == 'Stock Returned') 
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
      }
    }

    for($i = 0; $i < sizeof($skus); $i++) {
      for($j = $i; $j < sizeof($skus); $j++) {
        if($skus[$i]['qty'] < $skus[$j]['qty']) {
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
   * To store a new sku
   *
   *@
   */
  public function store(Request $request, Product $product)
  {
    $request->validate([
      'name'        =>  'required',
      'company_id'  =>  'required'
    ]);

    $sku = new Sku($request->all());
    $product->skus()->save($sku);

    return response()->json([
      'data'    =>  $sku
    ], 201); 
  }

  /*
   * To view a single sku
   *
   *@
   */
  public function show(Product $product, $sku)
  {
    $sku = Sku::where('id', '=', $sku)
      ->first();
    
      return response()->json([
      'data'   =>  $sku
    ], 200);
  }

  /*
   * To update a sku
   *
   *@
   */
  public function update(Request $request, Product $product, Sku $sku)
  {
    $request->validate([
      'name'  =>  'required',
    ]);

    $sku->update($request->all());
      
    return response()->json([
      'data'  =>  $sku
    ], 200);
  }
}
