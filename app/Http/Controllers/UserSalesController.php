<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\UserSale;

class UserSalesController extends Controller
{
  public function __construct()
  {
    $this->middleware(['auth:api', 'company']);
  }

  /*
   * To get all user sales
   *
   *@
   */
  public function index(Request $request)
  {
    $userSales = request()->user()->user_sales;

    if($request->month) {
      $userSales = UserSale::whereMonth('date', '=', $request->month);
      $sales = [];
      
      if($request->user_id) {
        $sales = $userSales->with('user')->where('user_id', '=', $request->user_id)
          ->latest()->get();
      }
      else {
        foreach($request->company->users as $user) {
          $uss = $userSales->with('user')->where('user_id', '=', $user->id)
              ->latest()->get();
          foreach($uss as $us) {
            $sales[] = $us->toArray();
          }
          // Again assign userSales to default
          $userSales = UserSale::whereMonth('date', '=', $request->month);
        }
      }

      $userSales = $sales;
    }

    return response()->json([
      'data'     =>  $userSales
    ], 200);
  }

  /*
   * To store a new user sale
   *
   *@
   */
  public function store(Request $request)
  {
    $request->validate([
      'date'          =>  'required',
      'amount'        =>  'required',
      'customer_name' =>  'required',
      'phone_no'      =>  'required',
    ]);

    $userSale = new UserSale($request->all());
    $request->user()->user_sales()->save($userSale);

    return response()->json([
      'data'    =>  $userSale
    ], 201); 
  }

  /*
   * To view a single user sale
   *
   *@
   */
  public function show(UserSale $userSale)
  {
    return response()->json([
      'data'   =>  $userSale
    ], 200);   
  }

  /*
   * To update a user sale
   *
   *@
   */
  public function update(Request $request, UserSale $userSale)
  {
    $request->validate([
      'date'        =>  'required',
      'amount'      =>  'required'  
    ]);

    $userSale->update($request->all());
      
    return response()->json([
      'data'  =>  $userSale
    ], 200);
  }
}
