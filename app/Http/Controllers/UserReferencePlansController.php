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

  public function masters(Request $request)
  {
    $request->request->add(['search' => 'all']);
    $usersController = new UsersController();
    $usersResponse = $usersController->index($request);

    $referencePlanController = new ReferencePlansController();
    $referencePlanResponse = $referencePlanController->index($request);

    $days = [
      [ 'id'    =>  1,
        'value' => 'Monday'
      ], 
      [ 'id'    =>  2,
        'value' => 'Tuesday'
      ], 
      [ 'id'    =>  3,
        'value' => 'Wednesday'
      ],
      [ 'id'    =>  4,
        'value' => 'Thursday'
      ], 
      [ 'id'    =>  5,
        'value' => 'Friday'
      ], 
      [ 'id'    =>  6,
        'value' => 'Saturday'
      ]
    ];

    $weeks = [1, 2, 3, 4];

    return response()->json([
      'users'           =>  $usersResponse->getData()->data,
      'reference_plans' =>  $referencePlanResponse->getData()->data,
      'days'            =>  $days,
      'weeks'           =>  $weeks
    ], 200);
  }

  public function index()
  {
    // $user_reference_plans = request()->company->user_reference_plans;

    // return response()->json([
    //   'data'     =>  $user_reference_plans,
    //   'success'   =>  true,
    // ], 200);

    $count = 0;
    if(request()->page && request()->rowsPerPage) {
      $user_reference_plans = request()->company->user_reference_plans();
      $count = $user_reference_plans->count();
      $user_reference_plans = $user_reference_plans->paginate(request()->rowsPerPage)->toArray();
      $user_reference_plans = $user_reference_plans['data'];
    } else {
      $user_reference_plans = request()->company->user_reference_plans; 
      $count = $user_reference_plans->count();
    }

    return response()->json([
      'data'     =>  $user_reference_plans,
      'count'    =>   $count
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
