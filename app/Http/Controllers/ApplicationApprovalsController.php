<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ApplicationApproval;
use App\UserApplication;

class ApplicationApprovalsController extends Controller
{
  public function __construct()
  {
    $this->middleware(['auth:api']);
  }

  /*
   * To get all application approvals
     *
   *@
   */
  public function index(UserApplication $userApplication)
  {
    $applicationApprovals = $userApplication->application_approvals;

    return response()->json([
      'data'     =>  $applicationApprovals
    ], 200);
  }

  /*
   * To store a new application approval
   *
   *@
   */
  public function store(Request $request, UserApplication $userApplication)
  {
    $request->validate([
      'supervisor_id'  =>  'required',
      'status'         =>  'required',
    ]);

    $applicationApproval = ApplicationApproval::where('user_application_id', '=', $userApplication->id)->first();
    if($applicationApproval != null) {
      $applicationApproval->update($request->all());
    }
    else {
      $applicationApproval = new ApplicationApproval($request->all());
      $userApplication->application_approvals()->save($applicationApproval);
    }

    return response()->json([
      'data'    =>  $applicationApproval
    ], 201); 
  }

  /*
   * To view a single application approvals
   *
   *@
   */
  public function show(UserApplication $userApplication, ApplicationApproval $applicationApproval)
  {
    return response()->json([
      'data'   =>  $applicationApproval
    ], 200);   
  }

  /*
   * To update a application approvales
   *
   *@
   */
  public function update(Request $request, UserApplication $userApplication, ApplicationApproval $applicationApproval)
  {
    $request->validate([
      'supervisor_id'  =>  'required',
      'status'         =>  'required',
    ]);

    $applicationApproval->update($request->all());
      
    return response()->json([
      'data'  =>  $applicationApproval
    ], 200);
  }
}
