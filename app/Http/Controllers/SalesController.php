<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use APp\Stock;
use App\Sale;
use App\Sku;

class SalesController extends Controller
{
  public function __construct()
  {
    $this->middleware(['auth:api', 'company']);
  }

  /*
   * To get all sales
   *
   *@
   */
  public function all()
  {
    $sales = [];
    for($i = 1; $i <= 31; $i++) {
      $sales[] = Sale::with('retailer', 'sku')
      ->whereMonth('created_at', '=', 2)
      ->whereDay('created_at', '=', $i)
      ->get();
    }
    

    return response()->json([
      'data'     =>  $sales
    ], 200);
  }

  /*
   * To get all sales of stock
     *
   *@
   */
  public function index(Sku $sku)
  {
    $sales = $sku->sales;

    return response()->json([
      'data'     =>  $sales
    ], 200);
  }

  /*
   * To store a new sale
   *
   *@
   */
  public function store(Request $request, Sku $sku)
  {
    $request->validate([
      'qty'    =>  'required',
      'sku_id'  =>   'required',
      'retailer_id'  =>   'required'
    ]);

    $sale = new Sale($request->all());
    $sku->sales()->save($sale);

    return response()->json([
      'data'    =>  $sale,
      'success'  => true
    ], 201); 
  }

  /*
   * To view a single sale
   *
   *@
   */
  public function show(Sku $sku, Sale $sale)
  {
    return response()->json([
      'data'   =>  $sale
    ], 200);   
  }

  /*
   * To update a sale
   *
   *@
   */
  public function update(Request $request, Sku $sku, Sale $sale)
  {
    $request->validate([
      'qty'  =>  'required',
    ]);

    $sale->update($request->all());
      
    return response()->json([
      'data'  =>  $sale,
      'success' =>  true
    ], 200);
  }
}
