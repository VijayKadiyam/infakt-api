<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\ReferencePlan;
use App\UserReferencePlan;
use App\User;

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
  public function index(Request $request)
  {
    $reference_plans = [];

    if($request->userId && $request->weekNo && $request->day) {
      $user = User::find($request->userId);
      $whichWeek = 1;
      if($user->beat_type_id != null) {
        if($user->beat_type_id != 1) {
          $whichWeek = $request->weekNo / $user->beat_type_id;
        }
      }
      $user_reference_plans = UserReferencePlan::where('user_id', '=', $user->id)
        ->where('day', '=', $request->day)
        ->where('which_week', '=', $whichWeek)
        ->get();
      foreach($user_reference_plans as $user_reference_plan) {
        $reference_plans[] = $user_reference_plan->reference_plan;
      }
    } else 
      $reference_plans = request()->company->reference_plans;

    return response()->json([
      'data'     =>  $reference_plans,
      'success'   =>  true,
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
