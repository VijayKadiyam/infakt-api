<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\ReferencePlan;

class ReferencePlansController extends Controller
{
  public function __construct()
  {
    $this->middleware(['auth:api', 'company']);
  }

  /*
   * To get all reference plans
     *
   *@
   */
  public function index()
  {
    $reference_plans = request()->company->reference_plans;

    return response()->json([
      'data'     =>  $reference_plans
    ], 200);
  }

  /*
   * To store a new reference plan
   *
   *@
   */
  public function store(Request $request)
  {
    $request->validate([
      'name'    =>  'required'
    ]);

    $referencePlan = new ReferencePlan($request->all());
    $request->company->reference_plans()->save($referencePlan);

    return response()->json([
      'data'    =>  $referencePlan
    ], 201); 
  }

  /*
   * To view a single reference plan
   *
   *@
   */
  public function show(ReferencePlan $referencePlan)
  {
    return response()->json([
      'data'   =>  $referencePlan
    ], 200);   
  }

  /*
   * To update a reference plan
   *
   *@
   */
  public function update(Request $request, ReferencePlan $referencePlan)
  {
    $request->validate([
      'name'  =>  'required',
    ]);

    $referencePlan->update($request->all());
      
    return response()->json([
      'data'  =>  $referencePlan
    ], 200);
  }
}
