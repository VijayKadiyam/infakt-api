<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Sku;
use App\Stock;
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
  public function closing_stocks()
  {
    $now = Carbon::now()->format('Y-m-d'); //Closing Stock For todays Log
    $count = 0;
    if (request()->page && request()->rowsPerPage) {
      $stocks = Stock::whereDate('created_at', $now)->with('sku', 'unit', 'distributor', 'offer');
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
