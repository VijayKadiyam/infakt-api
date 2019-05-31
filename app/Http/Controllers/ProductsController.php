<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Product;

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
