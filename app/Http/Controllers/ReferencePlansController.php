<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\ReferencePlan;
use App\UserReferencePlan;
use App\User;
use App\Order;
use Carbon\Carbon;

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
    $count = 0;
    if($request->userId && $request->weekNo && $request->day) {
      $user = User::find($request->userId);
      $whichWeek = $request->weekNo / 12;
      if($user->beat_type_id == 1)
        $whichWeek = 1;
      else if($user->beat_type_id == 2 && $whichWeek == 3)
        $whichWeek = 1;
      else if($user->beat_type_id == 2 && $whichWeek == 4)
        $whichWeek = 2;
      // $whichWeek = 1;
      // if($user->beat_type_id != null) {
      //   if($user->beat_type_id != 1) {
      //     $whichWeek = $request->weekNo / $user->beat_type_id;
      //   }
      // }
      $user_reference_plans = UserReferencePlan::where('user_id', '=', $user->id)
        ->where('day', '=', $request->day)
        ->where('which_week', '=', $whichWeek)
        ->get();

      $totalOrderValue = 0;
      foreach($user_reference_plans as $user_reference_plan) {
        $referencePlanNames[] = $user_reference_plan->reference_plan->name;
        $totalOutlets = sizeof($user_reference_plan->reference_plan->retailers);
        $rfmtd = 0;
        $rfl3m = 0;
        $ordersTaken = 0;
        foreach ($user_reference_plan->reference_plan->retailers as $retailer) {
          $orders = Order::where('retailer_id', '=', $retailer->id)
            ->whereDate('created_at', Carbon::now())
            // ->whereMonth('created_at', Carbon::now()->month)
            ->with('order_details')
            ->get();
          if(sizeof($orders) > 0)  {
            $ordersTaken++;
            foreach ($orders as $order) {
              $rfmtd += $order->total;
              $totalOrderValue += $order->total;
            }
            $retailer['mtd'] = $rfmtd;
            $retailer['l3m']  = $rfmtd;;
            $retailer['is_done'] = 'Y';
          } else {
            $retailer['mtd'] = 0;
            $retailer['l3m']  = $rfmtd;;
            $retailer['is_done'] = 'N';
          }

        }

        $user_reference_plan->reference_plan['total_outlets'] = $totalOutlets;
        $user_reference_plan->reference_plan['billed_outlets'] = $ordersTaken;
        $user_reference_plan->reference_plan['unbilled_outlets'] = $totalOutlets - $ordersTaken;
        $user_reference_plan->reference_plan['mtd'] = $totalOrderValue;
        $user_reference_plan->reference_plan['l3m'] = $rfmtd;

        $reference_plans[] = $user_reference_plan->reference_plan;
      }
    } else 
      $reference_plans = request()->company->reference_plans;

    $count = sizeof($reference_plans);

    return response()->json([
      'data'     =>  $reference_plans,
      'success'   =>  true,
      'count'     =>  $count,
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
