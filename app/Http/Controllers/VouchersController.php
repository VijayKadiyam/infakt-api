<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Voucher;

class VouchersController extends Controller
{
  public function __construct()
  {
    $this->middleware(['auth:api', 'company']);
  }

  /*
   * To get all vouchers
     *
   *@
   */
  public function index()
  {
    $vouchers = request()->user()->vouchers;

    if(request()->user_id) {
      $vouchers = Voucher::where('user_id', '=', request()->user_id)
        ->with('user', 'voucher_type')
        ->get();

    }

    if(request()->month) {
      $vouchers = Voucher::where('user_id', '=', request()->user_id)
        ->whereMonth('created_at', '=', request()->month)
        ->with('user', 'voucher_type')
        ->get();
    }
    
    return response()->json([
      'data'     =>  $vouchers,
      'success'   =>  true
    ], 200);
  }

  /*
   * To store a new voucher
   *
   *@
   */
  public function store(Request $request)
  {
    $request->validate([
      'voucher_type_id' =>  'required',
      'amount'  =>  'required',
      'description'  =>  'required'
    ]);

    $voucher = new Voucher($request->all());
    $request->user()->vouchers()->save($voucher);

    return response()->json([
      'data'    =>  $voucher,
      'success' =>  true
    ], 201); 
  }

  /*
   * To view a single voucher
   *
   *@
   */
  public function show(Voucher $voucher)
  {
    return response()->json([
      'data'   =>  $voucher
    ], 200);   
  }

  /*
   * To update a voucher
   *
   *@
   */
  public function update(Request $request, Voucher $voucher)
  {
    $request->validate([
      'voucher_type_id' =>  'required',
      'amount'  =>  'required',
      'description'  =>  'required'
    ]);

    $voucher->update($request->all());
    
    return response()->json([
      'data'  =>  $voucher,
      'success' =>  true
    ], 200);
  }
}
