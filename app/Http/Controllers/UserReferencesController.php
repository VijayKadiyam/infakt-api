<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\UserReference;

class UserReferencesController extends Controller
{
  public function __construct()
  {
    $this->middleware(['auth:api', 'company']);
  }

  public function index(Request $request, User $user)
  {
    $userReference = $user->user_references;

    return response()->json([
      'data'     =>  $userReference,
      'success'   =>  true
    ], 200);
  }

  public function store(Request $request, User $user)
  {
    $request->validate([
      'name'  =>  'required',
      'company_name'       =>  'required',
      'designation' =>  'required',
      'contact_number'      =>  'required',
    ]);

    $userReference = new UserReference($request->all());
    $user->user_references()->save($userReference);

    return response()->json([
      'data'    =>  $userReference
    ], 201); 
  }

  public function show(User $user, UserReference $userReference)
  {
    return response()->json([
      'data'   =>  $userReference
    ], 200);   
  }

  public function update(Request $request, User $user, UserReference $userReference)
  {
    $request->validate([
      'name'  =>  'required',
      'company_name'       =>  'required',
      'designation' =>  'required',
      'contact_number'      =>  'required',
    ]);

    $userReference->update($request->all());
      
    return response()->json([
      'data'  =>  $userReference
    ], 200);
  }
}
