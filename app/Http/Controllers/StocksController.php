<?php

namespace App\Http\Controllers;

use App\DailyOrderSummary;
use App\Order;
use Illuminate\Http\Request;
use App\Sku;
use App\Stock;
use App\User;
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

  // public function closing_stocks()
  // {
  //   $now = Carbon::now()->format('Y-m-d'); //Closing Stock For todays Log
  //   $count = 0;
  //   if (request()->page && request()->rowsPerPage) {
  //     $stocks = Stock::whereDate('created_at', $now)->with('sku', 'unit', 'distributor', 'offer');
  //     $count = $stocks->count();
  //     $stocks = $stocks->paginate(request()->rowsPerPage)->toArray();
  //     $stocks = $stocks['data'];
  //   } else {
  //     $stocks = Stock::with('sku', 'unit', 'distributor', 'offer');
  //     $count = $stocks->count();
  //   }

  //   return response()->json([
  //     'data'     =>  $stocks,
  //     'count'     => $count
  //   ], 200);
  // }
  // public function closing_stocks(Request $request)
  // {
  //   $asd = [];

  //   // $now = Carbon::now()->format('Y-m-d'); //Closing Stock For todays Log
  //   $count = 0;
  //   if (request()->page && request()->rowsPerPage) {
  //     $skus = request()->company->skus();

  //     $count = $skus->count();
  //     $skus = $skus->paginate(request()->rowsPerPage)->toArray();
  //     $skus = $skus['data'];
  //   } else if (request()->search) {
  //     $skus = request()->company->skus()
  //       ->where('name', 'LIKE', '%' . $request->search . '%')
  //       ->get();
  //   } else {
  //     $skus = request()->company->skus;
  //     $count = $skus->count();
  //   }


  //   $users = $request->company->users();

  //   if ($request->user_id) {
  //     $users = $users->where('users.id', '=', $request->user_id);
  //   }

  //   $users = $users->with('roles')
  //     ->whereHas('roles',  function ($q) {
  //       $q->where('name', '!=', 'Admin');
  //     });

  //   $users = $users->get();
  //   foreach ($users as $key => $user) {

  //     // $user = User::find($request->userId);
  //     if ($user) {
  //       $dailyOrderSummaries = DailyOrderSummary::where('user_id', '=', $user->id)
  //         // ->where('sku_id', '=', $sku->id)
  //         ->get();
  //       foreach ($skus as $sku) {
  //         $isSku = 0;
  //         foreach ($dailyOrderSummaries as $dailyOrderSummary) {
  //           if ($dailyOrderSummary->sku_id == $sku->id) {
  //             $isSku = 1;
  //             $sku['qty'] = (int) $dailyOrderSummary->closing_stock;
  //             $sku['opening_stock'] = (int)$dailyOrderSummary->opening_stock;
  //             $sku['received_stock'] = (int)$dailyOrderSummary->received_stock;
  //             $sku['purchase_returned_stock'] = (int)$dailyOrderSummary->purchase_returned_stock;
  //             $sku['sales_stock'] = (int)$dailyOrderSummary->sales_stock;
  //             $sku['returned_stock'] = (int)$dailyOrderSummary->returned_stock;
  //             $sku['closing_stock'] = (int)$dailyOrderSummary->closing_stock;
  //           }
  //         }
  //         if ($isSku == 0) {
  //           $sku['qty'] = 0;
  //           $sku['opening_stock'] = 0;
  //           $sku['received_stock'] = 0;
  //           $sku['purchase_returned_stock'] = 0;
  //           $sku['sales_stock'] = 0;
  //           $sku['returned_stock'] = 0;
  //           $sku['closing_stock'] = 0;
  //         }
  //       }
  //     }
  //     for ($i = 0; $i < sizeof($skus); $i++) {
  //       for ($j = $i; $j < sizeof($skus); $j++) {
  //         if ($skus[$i]['qty'] < $skus[$j]['qty']) {
  //           $temp = $skus[$i];
  //           $skus[$i] = $skus[$j];
  //           $skus[$j] = $temp;
  //         }
  //       }
  //     }
  //   }
  //   // $skus = $asd;
  //   // return $skus;
  //   // for ($i = 0; $i < sizeof($skus); $i++) {
  //   //   for ($j = $i; $j < sizeof($skus); $j++) {
  //   //     if ($skus[$i]['qty'] < $skus[$j]['qty']) {
  //   //       $temp = $skus[$i];
  //   //       $skus[$i] = $skus[$j];
  //   //       $skus[$j] = $temp;
  //   //     }
  //   //   }
  //   // }


  //   return response()->json([
  //     'data'     =>  $skus,
  //     'count'    =>   $count,
  //     'success' =>  true,
  //   ], 200);
  // }
  /*
   * To get all Closing stocks direct From Daily Order Summary
     *
   *@
   */
  public function closing_stocks(Request $request)
  {
    $now = Carbon::now()->format('Y-m-d'); //Closing Stock For todays Log
    $count = 0;
    $dailyOrderSummaries = $request->company->daily_order_summaries()
      ->whereDate('created_at', '=', $now)
      ->latest()
      ->orderBy('closing_stock', 'DESC');

    $region = $request->region;
    if ($region) {
      $dailyOrderSummaries = $dailyOrderSummaries->whereHas('user',  function ($q) use ($region) {
        $q->where('region', 'LIKE', '%' . $region . '%');
      });
    }
    $channel = $request->channel;
    if ($channel) {
      $dailyOrderSummaries = $dailyOrderSummaries->whereHas('user',  function ($q) use ($channel) {
        $q->where('channel', 'LIKE', '%' . $channel . '%');
      });
    }
    $brand = $request->brand;
    if ($brand) {
      $dailyOrderSummaries = $dailyOrderSummaries->whereHas('user',  function ($q) use ($brand) {
        $q->where('brand', 'LIKE', '%' . $brand . '%');
      });
    }
    $supervisor_id = $request->supervisor_id;
    if ($supervisor_id != '')
      $dailyOrderSummaries = $dailyOrderSummaries->whereHas('user',  function ($q) use ($supervisor_id) {
        $q->where('supervisor_id', '=', $supervisor_id);
      });
    $user_id = $request->user_id;
    if ($user_id != '')
      $dailyOrderSummaries = $dailyOrderSummaries->whereHas('user',  function ($q) use ($user_id) {
        $q->where('id', '=', $user_id);
      });

    $count = $dailyOrderSummaries->count();
    if (request()->page && request()->rowsPerPage) {
      $dailyOrderSummaries = $dailyOrderSummaries->paginate(request()->rowsPerPage)->toArray();
      $dailyOrderSummaries = $dailyOrderSummaries['data'];
    } else {
      $dailyOrderSummaries = $dailyOrderSummaries->get();
    }
    return response()->json([
      'data'     =>  $dailyOrderSummaries,
      'count'    =>   $count,
      'success' =>  true,
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
