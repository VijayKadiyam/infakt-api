<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\VoucherType;

class VoucherTypesController extends Controller
{
  public function __construct()
  {
    $this->middleware(['auth:api', 'company']);
  }

  /*
   * To get all voucher types
   *
   *@
   */
  public function index()
  {
    $voucherTypes = request()->company->voucher_types;

    return response()->json([
      'data'     =>  $voucherTypes,
      'success'   =>  true
    ], 200);
  }

  /*
   * To store a new voucher type
   *
   *@
   */
  public function store(Request $request)
  {
    $request->validate([
      'name'    =>  'required',
    ]);

    $voucherType = new VoucherType($request->all());
    $request->company->voucher_types()->save($voucherType);

    return response()->json([
      'data'    =>  $voucherType
    ], 201); 
  }

  /*
   * To view a single voucher type
   *
   *@
   */
  public function show(VoucherType $voucherType)
  {

    return response()->json([
      'data'   =>  $voucherType
    ], 200);   
  }

  /*
   * To update an voucher type
   *
   *@
   */
  public function update(Request $request, VoucherType $voucherType)
  {

    $request->validate([
      'name'  =>  'required',
    ]);

    $voucherType->update($request->all());
    
    return response()->json([
      'data'  =>  $voucherType
    ], 200);
  }
}
