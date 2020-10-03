<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ReferencePlan;
use App\UserReferencePlan;

class UserReferencePlansController extends Controller
{
  public function __construct()
  {
    $this->middleware(['auth:api', 'company']);
  }

  public function index()
  {
    $user_reference_plans = request()->company->user_reference_plans;

    return response()->json([
      'data'     =>  $user_reference_plans,
      'success'   =>  true,
    ], 200);
  }

  public function store(Request $request)
  {
    $request->validate([
      'user_id'             =>  'required',
      'reference_plan_id'   =>  'required',
      'day'                 =>  'required',
      'which_week'          =>  'required',
    ]);

    $userReferencePlan = new UserReferencePlan($request->all());
    $request->company->user_reference_plans()->save($userReferencePlan);

    return response()->json([
      'data'    =>  $userReferencePlan
    ], 201); 
  }

  public function show(UserReferencePlan $userReferencePlan)
  {
    return response()->json([
      'data'   =>  $userReferencePlan
    ], 200);   
  }

  public function update(Request $request, UserReferencePlan $userReferencePlan)
  {

    $userReferencePlan->update($request->all());
      
    return response()->json([
      'data'  =>  $userReferencePlan
    ], 200);
  }

}
