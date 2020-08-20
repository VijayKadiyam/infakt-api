<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;

class SupervisorUsersController extends Controller
{
  public function __construct()
  {
    $this->middleware('auth:api');
  }

  /*
   * Assign role to user
   *
   *@
   */
  public function store(Request $request)
  {
    $request->validate([
      'supervisor_id' =>  'required',
      'user_id'       =>  'required'
    ]);

    $user       =  User::find($request->user_id);
    $supervisor =  User::find($request->supervisor_id);
    $user->assignSupervisor($supervisor->id);
    $supervisorUser = User::with('supervisors')->find($request->user_id);

    return response()->json([
    'data'    =>  $supervisorUser
    ], 201); 
  }
}
