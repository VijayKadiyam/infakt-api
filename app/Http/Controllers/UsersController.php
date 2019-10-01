<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Role;

class UsersController extends Controller
{
  public function __construct()
  {
    $this->middleware(['auth:api', 'company']);
  }

  /*
   * To get all the users
   *
   *@
   */
  public function index(Request $request)
  {
    $role = 3;
    $users = [];
    if($request->search == 'all')
      $users = $request->company->users()->with('roles')
        ->whereHas('roles',  function($q) {
          $q->where('name', '!=', 'Admin');
        })->latest()->get();
    else 
      if($request->role_id) {
        $role = Role::find($request->role_id);
        $users = $request->company->users()
          ->whereHas('roles', function($q) use($role) { 
            $q->where('name', '=', $role->name);
          })->latest()->get();
      }

    return response()->json([
          'data'  =>  $users
      ], 200);
  }

  /*
   * To store a new company user
   *
   *@
   */
  public function store(Request $request)
  {
    $request->validate([
      'name'                    => ['required', 'string', 'max:255'],
      'email'                   => ['required', 'string', 'email', 'max:255', 'unique:users'],
      'phone'                   => ['required', 'unique:users'],
      'doj'                     =>  'required',
      'dob'                     =>  'required',
      'company_designation_id'  =>  'required',
    ]);

    $user  = $request->all();
    $user['password'] = bcrypt('123456');
    $user['password_backup'] = bcrypt('123456');
    // $password = mt_rand(100000, 999999);
    // $user['password'] = $password;
    // $user['password_backup'] = $password;

    $user = new User($user);
    $user->save();

    return response()->json([
      'data'     =>  $user
    ], 201); 
  }

  /*
   * To show particular user
   *
   *@
   */
  public function show($id)
  {
    $user = User::where('id' , '=', $id)
      ->with('roles', 'companies', 'company_designation', 'company_state_branch', 'supervisors')->first();

    return response()->json([
      'data'  =>  $user,
      'success' =>  true
    ], 200); 
  }

  /*
   * To update user details
   *
   *@
   */
  public function update(Request $request, User $user)
  {
    $request->validate([
      'name'                    => ['required', 'string', 'max:255'],
      'email'                   => ['required', 'string', 'email', 'max:255'],
      'phone'                   =>  'required',
      'doj'                     =>  'required',
      'dob'                     =>  'required',
      'company_designation_id'  =>  'required',
    ]);

    $user->update($request->all());
    
    return response()->json([
      'data'  =>  $user,
      'success' =>  true
    ], 200);
  }

  /*
   * To check or update unique id
   *
   *@
   */
  public function checkOrUpdateUniqueID(Request $request, User $user)
  {
    if($user->unique_id == null | $user->unique_id == '') {
      $user->update($request->all());
    }

    return response()->json([
      'data'  =>  $user,
      'success' =>  $user->unique_id == $request->unique_id ? true : false
    ], 200);
  }
}