<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\SkuType;

class SkuTypesController extends Controller
{
  public function __construct()
  {
    $this->middleware(['auth:api', 'company']);
  }

  /*
   * To get all sku types
     *
   *@
   */
  public function index()
  {
    $count = 0;
    if(request()->page && request()->rowsPerPage) {
      $sku_types = request()->company->sku_types();
      $count = $sku_types->count();
      $sku_types = $sku_types->paginate(request()->rowsPerPage)->toArray();
      $sku_types = $sku_types['data'];
    } else {
      $sku_types = request()->company->sku_types; 
      $count = $sku_types->count();
    }

    return response()->json([
      'data'     =>  $sku_types,
      'count'    =>   $count
    ], 200);
  }

  /*
   * To store a new sku types
   *
   *@
   */
  public function store(Request $request)
  {
    $request->validate([
      'name'    =>  'required'
    ]);

    $skuType = new SkuType($request->all());
    $request->company->sku_types()->save($skuType);

    return response()->json([
      'data'    =>  $skuType
    ], 201); 
  }

  /*
   * To view a single sku type
   *
   *@
   */
  public function show(SkuType $sku_type)
  {
    return response()->json([
      'data'   =>  $sku_type
    ], 200);   
  }

  /*
   * To update a sku type
   *
   *@
   */
  public function update(Request $request, SkuType $sku_type)
  {
    $request->validate([
      'name'  =>  'required',
    ]);

    $sku_type->update($request->all());
      
    return response()->json([
      'data'  =>  $sku_type
    ], 200);
  }
}
