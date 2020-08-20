<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Plan;

class PlansController extends Controller
{
  public function __construct()
  {
    $this->middleware(['auth:api', 'company']);
  }

  /*
   * To get all plans
     *
   *@
   */
  public function index()
  {
    $plans = request()->user()->plans;

    if(request()->user_id) {
      $plans = Plan::where('user_id', '=', request()->user_id)
        ->with('plan_actuals', 'allowance_type', 'user', 'plan_travelling_details')
        ->orderBy('date', 'ASC')
        ->get();
    }

    if(request()->month) {
      $plans = Plan::where('user_id', '=', request()->user_id)
        ->whereMonth('date', '=', request()->month)
        ->with('plan_actuals', 'allowance_type', 'user', 'plan_travelling_details')
        ->orderBy('date', 'ASC')
        ->get();

      // dd($plans->toArray());
    }

    return response()->json([
      'data'     =>  $plans,
      'success'   =>  true
    ], 200);
  }
  /*
   * To store a new plan
   *
   *@
   */
  public function store(Request $request)
  {
    $request->validate([
      'allowance_type_id' =>  'required',
      'date'  =>  'required',
      'plan'  =>  'required'
    ]);

    $plan = new Plan($request->all());
    $request->user()->plans()->save($plan);

    return response()->json([
      'data'    =>  $plan,
      'success' =>  true
    ], 201); 
  }

  /*
   * To view a single plan
   *
   *@
   */
  public function show(Plan $plan)
  {
    return response()->json([
      'data'   =>  $plan
    ], 200);   
  }

  /*
   * To update a plan
   *
   *@
   */
  public function update(Request $request, Plan $plan)
  {
    $request->validate([
      'date'  =>  'required',
      'plan'  =>  'required'
    ]);

    $plan->update($request->all());
    
    return response()->json([
      'data'  =>  $plan,
      'success' =>  true
    ], 200);
  }
}
