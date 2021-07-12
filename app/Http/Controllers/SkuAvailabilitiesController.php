<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\SkuAvailability;
use Carbon\Carbon;

class SkuAvailabilitiesController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:api', 'company']);
    }

    public function index(Request $request)
    {
      $skuAvailabilities = [];

      $skus = request()->company->skus;
      foreach ($skus as $sku) {
        $skuAvailability = SkuAvailability::where('sku_id', '=', $sku->id)
          ->where('retailer_id', '=', $request->retailerId)
          ->latest()
          ->first();
        if($skuAvailability) {
          $skuAvailabilities[] = [
            'id'        =>  $skuAvailability->id,
            'sku_id'    => $sku->id,
            'name'      =>  $sku->name, 
            'offer_id'  =>  $sku->offer_id,
            'offer'     =>  $sku->offer,
            'is_available'  =>  !!$skuAvailability->is_available,
          ];
        }
        else {
          $skuAvailabilities[] = [
            'sku_id'        => $sku->id,
            'name'      =>  $sku->name,
            'offer_id'      =>  $sku->offer_id,
            'offer'         =>  $sku->offer,
            'is_available'  =>  false
          ];
        }
      }

      return response()->json([
          'data'     => $skuAvailabilities,
          'success'  => true
      ], 200);
    }

    public function store(Request $request)
    {
      $todayDate = Carbon::now()->format('d-m-Y');
      if(!$request->id) {
        $skuAvailability = new SkuAvailability($request->all());
        $request->company->sku_availabilities()->save($skuAvailability);
      } else {
        $skuAvailability = SkuAvailability::where('id', '=', $request->id)
          ->where('date', '=', $todayDate)
          ->first();
        if($skuAvailability)
          $skuAvailability->update($request->all());
        else
          $request->company->sku_availabilities()->save($skuAvailability);
      }
        
        return response()->json([
            'data'    =>  $skuAvailability,
            'success' =>  true
        ], 201);
    }

    public function show(SkuAvailability $skuAvailability)
    {
        return response()->json([
            'data'   =>  $skuAvailability
        ], 200);
    }

    public function update(Request $request, SkuAvailability $skuAvailability)
    {
        $skuAvailability->update($request->all());

        return response()->json([
            'data'  =>  $skuAvailability,
            'success' =>  true
        ], 200);
    }

    public function destroy(SkuAvailability $skuAvailability)
    {
        $skuAvailability->delete();
    }

}
