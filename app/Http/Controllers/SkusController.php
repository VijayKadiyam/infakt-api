<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Sku;
use App\Product;
use App\Stock;
use App\User;
use App\Order;
use Carbon\Carbon;

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
    } else {
      $skus = request()->company->skus; 
      $count = $skus->count();
    }

    $user = User::find($request->userId);
    // if($user) {
      $stocks = Stock::whereYear('created_at', Carbon::now());
      if($user)
        $stocks = $stocks->where('distributor_id', '=', $user->distributor_id);
      $stocks = $stocks->latest()->get();

      $orders = Order::whereYear('created_at', Carbon::now());
      if($user)
        $orders = $orders->where('distributor_id', '=', $user->distributor_id);
      $orders = $orders->latest()->get();

      foreach ($skus as $sku) {
        $skuStocks = [];
        foreach ($stocks as $stock) {
          if($sku['id'] == $stock['sku_id']) 
            $skuStocks[] = $stock;
        }
        $sku['price'] = sizeof($skuStocks) > 0 ? $skuStocks[0]['price'] : 0;
        $sku['offer_price'] = null;
        if(sizeof($skuStocks) > 0) {
          $sku['price'] = $skuStocks[0]['price'];
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
        $returnedQty = 0; 
        $consumedQty = 0;

        foreach ($orders as $order) {
          foreach ($order->order_details as $detail) {
            if($detail->sku_id == $sku->id && $order->order_type == 'Stock Received') 
              $receivedQty += $detail->qty;
            if($detail->sku_id == $sku->id && $order->order_type == 'Stock Returned') 
              $returnedQty += $detail->qty;
            if($detail->sku_id == $sku->id && $order->order_type == 'Sales') 
              $consumedQty += $detail->qty;
          }
        }
        
        $sku['qty'] = ($totalQty - $consumedQty) > 0 ? ($totalQty - $consumedQty) : 0;

        $sku['opening_stock'] = $totalQty;
        $sku['received_stock'] = $receivedQty;
        $sku['returned_stock'] = $returnedQty;
        $sku['sales_stock'] = $consumedQty;
        $sku['closing_stock'] = ($totalQty - $consumedQty) > 0 ? ($totalQty + $receivedQty + $returnedQty - $consumedQty) : 0;
      }
    // }
    

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
  public function show(Product $product, Sku $skus)
  {
    return response()->json([
      'data'   =>  $skus
    ], 200);   
  }

  /*
   * To update a sku
   *
   *@
   */
  public function update(Request $request, Product $product, Sku $skus)
  {
    $request->validate([
      'name'  =>  'required',
    ]);

    $skus->update($request->all());
      
    return response()->json([
      'data'  =>  $skus
    ], 200);
  }
}
