<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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
}
