<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\ReferencePlan;

class DistributorReferencePlanController extends Controller
{
  public function store(Request $request)
  {
    $request->validate([
      'reference_plan_id' =>  'required',
      'distributor_id'    =>  'required'
    ]);

    $referencePlan =  ReferencePlan::find($request->reference_plan_id);
    $distributor =  User::find($request->distributor_id);
    if($request->op == 'assign')
      $distributor->assignReferencePlan($referencePlan->id);
    if($request->op == 'unassign')
      $distributor->unassignReferencePlan($referencePlan->id);
    $distributorReferencePlan = User::with('reference_plans')->find($request->distributor_id);

    return response()->json([
    'data'    =>  $distributorReferencePlan
    ], 201); 
  }
}
