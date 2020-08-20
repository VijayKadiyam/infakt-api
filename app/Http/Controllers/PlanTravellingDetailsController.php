<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Plan;
use App\PlanTravellingDetail;

class PlanTravellingDetailsController extends Controller
{
  public function __construct()
  {
    $this->middleware(['auth:api', 'company']);
  }

  /*
   * To get all plan travelling details
     *
   *@
   */
  public function index(Plan $plan)
  {
    $planTravellingDetails = $plan->plan_travelling_details;

    return response()->json([
      'data'     =>  $planTravellingDetails,
      'success'   =>  true
    ], 200);
  }

  /*
   * To store a plan travelling details
   *
   *@
   */
  public function store(Request $request, Plan $plan)
  {
    $request->validate([
      'from'              =>  'required',
      'to'                =>  'required',
      'travelling_way_id' =>  'required',
      'transport_mode_id' =>  'required',
    ]);

    $planTravellingDetail = new PlanTravellingDetail($request->all());
    $plan->plan_travelling_details()->save($planTravellingDetail);
    

    return response()->json([
      'data'    =>  $planTravellingDetail,
      'success' =>  true
    ], 201); 
  }

  /*
   * To view a single plan travelling detail
   *
   *@
   */
  public function show(Plan $plan, PlanTravellingDetail $planTravellingDetail)
  {
    return response()->json([
      'data'   =>  $planTravellingDetail
    ], 200);   
  }

  /*
   * To update a plan travelling detail 
   *
   *@
   */
  public function update(Request $request, Plan $plan, PlanTravellingDetail $planTravellingDetail)
  {
    $request->validate([
      'from'              =>  'required',
      'to'                =>  'required',
      'travelling_way_id' =>  'required',
      'transport_mode_id' =>  'required',
    ]);

    $planTravellingDetail->update($request->all());
    
    return response()->json([
      'data'  =>  $planTravellingDetail,
      'success' =>  true
    ], 200);
  }
}
