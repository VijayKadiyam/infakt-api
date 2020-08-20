<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\UserEducation;

class UserEducationsController extends Controller
{
  public function __construct()
  {
    $this->middleware(['auth:api', 'company']);
  }

  public function index(Request $request, User $user)
  {
    $userEducation = $user->user_educations;

    return response()->json([
      'data'     =>  $userEducation,
      'success'   =>  true
    ], 200);
  }

  public function store(Request $request, User $user)
  {
    $request->validate([
      'examination'  =>  'required',
      'school'       =>  'required',
      'passing_year' =>  'required',
      'percent'      =>  'required',
    ]);

    $userEducation = new UserEducation($request->all());
    $user->user_educations()->save($userEducation);

    return response()->json([
      'data'    =>  $userEducation,
      'success' =>  true
    ], 201); 
  }

  public function show(User $user, UserEducation $userEducation)
  {
    return response()->json([
      'data'   =>  $userEducation
    ], 200);   
  }

  public function update(Request $request, User $user, UserEducation $userEducation)
  {
    $request->validate([
      'examination'  =>  'required',
      'school'       =>  'required',
      'passing_year' =>  'required',
      'percent'      =>  'required',
    ]);

    $userEducation->update($request->all());
      
    return response()->json([
      'data'  =>  $userEducation,
      'success' =>  true
    ], 200);
  }

  public function destroy($userId, $id)
  {
    $userEducation = UserEducation::find($id);
    $userEducation->delete();
  }

}
