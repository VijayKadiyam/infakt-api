<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Sku;
use App\Stock;

class StocksController extends Controller
{
  public function __construct()
  {
    $this->middleware(['auth:api', 'company']);
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
