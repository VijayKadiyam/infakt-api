<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Permission;

class PermissionsController extends Controller
{
  public function __construct()
  {
    $this->middleware('auth:api');
  }

  /*
   * To get all permissions
     *
   *@
   */
  public function index(Request $request)
  {
    $permissions = Permission::get();

    return response()->json([
      'data'  =>  $permissions
    ], 200);
  }

  /*
   * To store a new permission
   *
   *@
   */
  public function store(Request $request)
  {
    $request->validate([
      'name'  =>  'required'
    ]);

    $permission = new Permission(request()->all());
    $permission->save();

    return response()->json([
      'data'  =>  $permission
    ], 201); 
  }

  /*
   * To view a single permission
   *
   *@
   */
  public function show(Permission $permission)
  {
    return response()->json([
      'data'  =>  $permission
    ], 200);   
  }

  /*
   * To update a permission
   *
   *@
   */
  public function update(Request $request, Permission $permission)
  {
    $request->validate([
      'name'  =>  'required',
    ]);

    $permission->update($request->all());
      
    return response()->json([
      'data'  =>  $permission
    ], 200);
  }
}
