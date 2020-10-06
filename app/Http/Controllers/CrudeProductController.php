<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\CrudeProduct;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ProductImport;
use App\Product;
use App\Sku;
use App\Unit;
use App\Stock;

class CrudeProductController extends Controller
{
  public function __construct()
  {
    $this->middleware(['auth:api', 'company'])
      ->except(['index']);
  }

  public function index()
  {
    return response()->json([
      'data'  =>  CrudeProduct::all()
    ]);
  }

  public function uploadShop(Request $request)
  {
    set_time_limit(0);
    
    if ($request->hasFile('productData')) {
      $file = $request->file('productData');

      Excel::import(new ProductImport, $file);
      
      return response()->json([
        'data'    =>  CrudeProduct::all(),
        'success' =>  true
      ]);
    }
  }

  public function processShop()
  {
    set_time_limit(0);
    
    $crude_products = CrudeProduct::all();

    foreach ($crude_products as $crude_product) {
      $product = Product::where('name', '=', $crude_product->product_name)
        ->first();
      if(!$product) {
        $data = [
          'name'  =>  $crude_product->product_name,
        ];
        $product = new Product($data);
        request()->company->products()->save($product);
      }

      $sku = Sku::where('product_id', '=', $product->id)
        ->where('name', '=', $crude_product->sku_name)
        ->first();
      if(!$sku) {
        $data = [
          'product_id'  =>  $product->id,
          'name'        =>  $crude_product->sku_name
        ];
        $sku = new Sku($data);
        $product->skus()->save($sku);
      }

      $unit = Unit::where('name', '=', $crude_product->unit)
        ->first();
      if(!$unit) {
        $data = [
          'name'  =>  $crude_product->unit
        ];
        $unit = new Unit($data);
        request()->company->units()->save($unit);
      }

      $stock = Stock::where('sku_id', '=', $sku->id)
        ->where('invoice_no', '=', $crude_product->invoice_no)
        ->first();
      if(!$stock) {
        $data = [
          'sku_id'      =>  $sku->id,
          'sku_type_id' =>  '-',
          'invoice_no'  =>  $crude_product->invoice_no,
          'qty'         =>  $crude_product->qty,
          'unit_id'     =>  $unit->id,
          'offer_id'    =>  '-',
          'price'       =>  $crude_product->price,
        ];
        $stock = new Stock($data);
        $sku->stocks()->save($stock);
      }
    }
  }

  public function truncate()
  {
    CrudeProduct::truncate();
  }
}
