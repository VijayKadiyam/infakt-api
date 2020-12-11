<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Sku;
use App\Product;

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
  public function index(Product $product)
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

    foreach ($skus as $sku) {
      $sku['price'] = $sku['id'] * 100;
      $sku['offer_price'] = $sku['offer_id'] != null ? $sku['price'] - ($sku['price'] * $sku['offer']['offer'] / 100) : null;
      $sku['qty'] = $sku['id'] * 2;
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
