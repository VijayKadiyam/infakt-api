<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\DamageStock;
use App\Product;

class DamageStocksController extends Controller
{
    public function __construct()
    {
      $this->middleware(['auth:api', 'company']);
    }

    public function masters(Request $request, Product $product)
    {
      $skusController = new SkusController();
      $skusResponse = $skusController->index($request, $product);

      return response()->json([
        'skus'  =>  $skusResponse->getData()->data,
      ], 200);
    }

    public function index(Request $request)
    {
      if($request->search) {
      $damageStock = request()->company->damage_stocks()
        ->where('created_at', 'LIKE', '%' . $request->search . '%')
        ->get();
      }
      else{
        $damageStock = request()->company->damage_stocks;
      }

      return response()->json([
      'data'     => $damageStock,
      'success'  => true
      ], 200);
    }

    public function store(Request $request)
    {
      $request->validate([
          'sku_id'    =>  'required',
          'qty'       =>  'required',

        ]);
    
        $damageStock = new DamageStock($request->all());
        $request->company->damage_stocks()->save($damageStock);
    
        return response()->json([
          'data'    =>  $damageStock,
          'success' =>  true
        ], 201); 
    }

    public function show(DamageStock $damageStock)
    {
      return response()->json([
          'data'   =>  $damageStock
        ], 200);
    }

    public function update(Request $request, DamageStock $damageStock)
    {
      $request->validate([
          'sku_id'  =>  'required',
          'qty'     =>  'required',
        ]);
    
        $damageStock->update($request->all());
        
        return response()->json([
          'data'  =>  $damageStock,
          'success' =>  true
        ], 200);
    }
    public function destroy(DamageStock $damageStock)
    {
      $damageStock->delete(); 
    }
}
