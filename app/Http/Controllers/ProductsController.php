<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Product;
use App\Retailer;
use Carbon\Carbon;
use App\User;

class ProductsController extends Controller
{
  public function __construct()
  {
    $this->middleware(['auth:api', 'company']);
  }

  /*
   * To get all products
     *
   *@
   */
  public function index()
  {
    $products = request()->company->products;

    return response()->json([
      'data'     =>  $products,
      'success'   =>  true
    ], 200);
  }

  public function productSkusStocks(Request $request)
  {
    if($request->productId) {
      $product = Product::find($request->productId);
      $skus = $product->skus;

      foreach($skus as $sku) {
        $stocks = $sku->stocks;
        $sales = $sku->sales;

        $totalStockQty = 0;
        foreach($stocks as $stock) 
          $totalStockQty += $stock->qty;

        $totalSaleQty = 0;
        foreach($sales as $sale) 
          $totalSaleQty += $sale->qty;

        $totalQty = $totalStockQty - $totalSaleQty;
        $sku['totalQty'] = $totalQty;
      }
    }

    return response()->json([
      'data'      =>  $skus,
      'success'   =>  true
    ], 200); 
  }

  public function sendOrderSMS(Request $request)
  {
    if($request->outletId && $request->userId && $request->totalAmount) {
      $retailer = Retailer::find($request->outletId);
      $user = User::find($request->userId);
      if($retailer) {
        $userName = $user->name;
        $date = Carbon::now()->format('d-m-Y');
        $uid = $retailer->retailer_code;
        $retailerName = $retailer->name;
        $totalAmount = $request->totalAmount;
        $this->sendSMS($retailer->phone, $userName, $date, $uid, $retailerName, $totalAmount);
      }
    }
  }

  public function sendSMS($phone, $userName, $date, $uid, $retailerName, $totalAmount)
  {
    $endpoint = "http://mobicomm.dove-sms.com//submitsms.jsp?user=PousseM&key=fc53bf6154XX&mobile=+91$phone&message=Name of Sales executive: $userName%0A$date%0AOutlet UID: $uid%0AOutlet name:: $retailerName%0ATotal value of Order Placed: Rs. $totalAmount&senderid=POUSSE&accusage=1";
    $client = new \GuzzleHttp\Client();
    $client->request('GET', $endpoint);
  }

  /*
   * To store a new company designations
   *
   *@
   */
  public function store(Request $request)
  {
    $request->validate([
      'name'    =>  'required'
    ]);

    $product = new Product($request->all());
    $request->company->products()->save($product);

    return response()->json([
      'data'    =>  $product
    ], 201); 
  }

  /*
   * To view a single product
   *
   *@
   */
  public function show(Product $product)
  {
    return response()->json([
      'data'   =>  $product
    ], 200);   
  }

  /*
   * To update a product
   *
   *@
   */
  public function update(Request $request, Product $product)
  {
    $request->validate([
      'name'  =>  'required',
    ]);

    $product->update($request->all());
      
    return response()->json([
      'data'  =>  $product
    ], 200);
  }
}
