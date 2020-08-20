<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\UserApplication;

class UserApplicationsController extends Controller
{
  public function __construct()
  {
    $this->middleware(['auth:api', 'company']);
  }

  /*
   * To get all user applications
     *
   *@
   */
  public function index(Request $request)
  {
    $userApplications = request()->user()->user_applications;

    if($request->user == 'supervisor') {

      $applications = [];
      foreach(request()->user()->users as $user) {
        $uas = UserApplication::where('user_id', '=', $user->id)->with('user', 'company_leave', 'leave_type', 'application_approvals')->latest()->get();
        foreach($uas as $ua) {
          $applications[] = $ua->toArray();
        }
      }
      $userApplications = $applications;
    }

    if($request->user_id) {
      $userApplications = UserApplication::where('user_id', '=', $request->user_id)->with('user', 'company_leave', 'leave_type', 'application_approvals')->latest()->get();
    }

    return response()->json([
      'data'     =>  $userApplications,
      'success'   =>  true
    ], 200);
  }

  /*
   * To store a new user application
   *
   *@
   */
  public function store(Request $request)
  {
    $request->validate([
      'company_leave_id'  =>  'required',
      'from_date'         =>  'required',
      'to_date'           =>  'required',
      'description'       =>  'required',
      'leave_type_id'     =>  'required'
    ]);

    $userApplication = new UserApplication($request->all());
    $request->user()->user_attendances()->save($userApplication);

    return response()->json([
      'data'    =>  $userApplication,
      'success' =>  true
    ], 201); 
  }

  /*
   * To view a single user application
   *
   *@
   */
  public function show(UserApplication $userApplication)
  {
    return response()->json([
      'data'   =>  $userApplication
    ], 200);   
  }

  /*
   * To update a user application
   *
   *@
   */
  public function update(Request $request, UserApplication $userApplication)
  {
    $request->validate([
      'company_leave_id'  =>  'required',
      'from_date'         =>  'required',
      'to_date'           =>  'required', 
      'description'       =>  'required',
      'leave_type_id'     =>  'required'
    ]);

    $userApplication->update($request->all());
      
    return response()->json([
      'data'  =>  $userApplication,
      'success' =>  true
    ], 200);
  }

}
