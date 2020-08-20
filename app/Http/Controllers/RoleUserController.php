<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Role;

class RoleUserController extends Controller
{
  /*
   * Assign role to user
   *
   *@
   */
  public function store(Request $request)
  {
    $request->validate([
        'user_id'        =>  'required',
        'role_id'        =>  'required'
    ]);

    $user =  User::find($request->user_id);
    $role =  Role::find($request->role_id);
    $user->assignRole($role->id);
    $roleUser = User::with('roles')->find($request->user_id);

    return response()->json([
    'data'    =>  $roleUser
    ], 201); 
  }
}
