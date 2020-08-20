<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Plan;
use App\PlanActual;

class PlanActualController extends Controller
{
  public function __construct()
  {
    $this->middleware(['auth:api', 'company']);
  }

  /*
   * To get all plan actuals
     *
   *@
   */
  public function index(Plan $plan)
  {
    $planActuals = $plan->plan_actuals;

    return response()->json([
      'data'     =>  $planActuals,
      'success'   =>  true
    ], 200);
  }

  /*
   * To store a plan actual
   *
   *@
   */
  public function store(Request $request, Plan $plan)
  {
    $request->validate([
      'status'  =>  'required',
      'details' =>  'required'
    ]);

    $planActual = PlanActual::where('plan_id', '=', $plan->id)->first();
    if($planActual) {
      $planActual->update($request->all());
    }
    else {
      $planActual = new PlanActual($request->all());
      $plan->plan_actuals()->save($planActual);
    }

    return response()->json([
      'data'    =>  $planActual,
      'success' =>  true
    ], 201); 
  }

  /*
   * To view a single plan actual
   *
   *@
   */
  public function show(Plan $plan, PlanActual $planActual)
  {
    return response()->json([
      'data'   =>  $planActual
    ], 200);   
  }
  

  /*
   * To update a plan actual 
   *
   *@
   */
  public function update(Request $request, Plan $plan, PlanActual $planActual)
  {
    $request->validate([
      'status'  =>  'required',
      'details' =>  'required'
    ]);

    $planActual->update($request->all());
    
    return response()->json([
      'data'  =>  $planActual,
      'success' =>  true
    ], 200);
  }
}
