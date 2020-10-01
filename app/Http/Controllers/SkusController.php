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

  public function getAll()
  {
    $products = request()->company()->products;

    $skus = [];
    foreach($products as $product) {
      $productSkus = $product->skus;
      foreach($productSkus as $productSku) {
        $skus[] = $productSku;
      }
    }

    return response()->json([
      'data'     =>  $skus,
      'success'   =>  true
    ], 200);
  }

  /*
   * To get all skus
     *
   *@
   */
  public function index(Product $product)
  {
    $skus = $product->skus;

    return response()->json([
      'data'     =>  $skus,
      'success'   =>  true
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
      'name'    =>  'required'
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
