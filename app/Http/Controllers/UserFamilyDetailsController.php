<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\UserFamilyDetail;

class UserFamilyDetailsController extends Controller
{
  public function __construct()
  {
    $this->middleware(['auth:api', 'company']);
  }

  public function index(Request $request, User $user)
  {
    $userFamilyDetail = $user->user_family_details;

    return response()->json([
      'data'     =>  $userFamilyDetail,
      'success'   =>  true
    ], 200);
  }

  public function store(Request $request, User $user)
  {
    $request->validate([
      'name'  =>  'required',
      'dob'       =>  'required',
      'gender' =>  'required',
      'relation'      =>  'required',
      'occupation'      =>  'required',
      'contact_number'      =>  'required',
    ]);

    $userFamilyDetail = new UserFamilyDetail($request->all());
    $user->user_family_details()->save($userFamilyDetail);

    return response()->json([
      'data'    =>  $userFamilyDetail,
      'success' =>  true
    ], 201); 
  }

  public function show(User $user, UserFamilyDetail $userFamilyDetail)
  {
    return response()->json([
      'data'   =>  $userFamilyDetail,
      'success' =>  true
    ], 200);   
  }

  public function update(Request $request, User $user, UserFamilyDetail $userFamilyDetail)
  {
    $request->validate([
      'name'  =>  'required',
      'dob'       =>  'required',
      'gender' =>  'required',
      'relation'      =>  'required',
      'occupation'      =>  'required',
      'contact_number'      =>  'required',
    ]);

    $userFamilyDetail->update($request->all());
      
    return response()->json([
      'data'  =>  $userFamilyDetail,
      'success' =>  true
    ], 200);
  }
}
