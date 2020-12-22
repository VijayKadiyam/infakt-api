<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\CrudeSale;
use App\Imports\SaleImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Retailer;
use App\Order;
use App\Sku;
use App\Sale;

class CrudeSalesController extends Controller
{
  public function __construct()
  {
    $this->middleware(['auth:api', 'company'])
      ->except(['index']);
  }

  public function index()
  {
    return response()->json([
      'data'  =>  CrudeSale::all()
    ]);
  }

  public function uploadSale(Request $request)
  {
    set_time_limit(0);
    
    if ($request->hasFile('sales')) {
      $file = $request->file('sales');

      Excel::import(new SaleImport, $file);
      
      return response()->json([
        'data'    =>  CrudeSale::all(),
        'success' =>  true
      ]);
    }
  }

  public function processSale()
  {
    set_time_limit(0);
    
    $crude_sales = CrudeSale::all();

    foreach($crude_sales as $sale) {
      if($sale->outlet_name) {
        $retailer = Retailer::where('retailer_code', '=', $sale->uid)
          ->first();
        if($retailer) {
          $order = Order::where('retailer_id', '=', $retailer->id)
            ->where('status', '=', 0)
            ->first();
          $sku = request()->company->skus()
            ->where('name', '=', $sale->sku)
            ->first();
          if($order && $sku) {
            $data = [
              'company_id'  =>  request()->company->id,
              'user_id'     =>  $order->user_id,
              'invoice_no'  =>  $sale->invoice_no,
              'retailer_id' =>  $order->retailer_id,
              'order_id'    =>  $order->id,
              'sku_id'      =>  $sku->id,
              'qty'         =>  0,
              'quantity'    =>  $sale->qty,
              'unit_price'  =>  $sale->unit_price,
              'bill_value'  =>  $sale->bill_value,
              'sku_type'    =>  $sale->sku_type,
              'offer'       =>  $sale->offer,
              'offer_type'  =>  $sale->offer_type,
              'offer_amount'=>  $sale->offer_amount,
              'total_bill_value'  =>  $sale->total_bill_value,
              'qty_returned'      =>  $sale->qty_returned,
              'final_bill_value'  =>  $sale->final_bill_value,
            ];
            $newSale = new Sale($data);
            $newSale->save();

            $order->status = 1;
            $order->update();
          }
        }
      }
    }

    return $crude_sales;
  }

  public function truncate()
  {
    CrudeSale::truncate();
  }
}
