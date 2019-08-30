<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\UserWorkExperience;

class UserWorkExperiencesController extends Controller
{
  public function __construct()
  {
    $this->middleware(['auth:api', 'company']);
  }

  public function index(Request $request, User $user)
  {
    $userWorkExperiences = $user->user_work_experiences;

    return response()->json([
      'data'     =>  $userWorkExperiences,
      'success'   =>  true
    ], 200);
  }

  public function store(Request $request, User $user)
  {
    $request->validate([
      'company_name'  =>  'required',
      'from'          =>  'required',
      'to'            =>  'required',
      'designation'   =>  'required',
      'uan_no'        =>  'required',
      'esic_no'       =>  'required',
    ]);

    $userWorkExperience = new UserWorkExperience($request->all());
    $user->user_work_experiences()->save($userWorkExperience);

    return response()->json([
      'data'    =>  $userWorkExperience,
      'success' =>  true
    ], 201); 
  }

  public function show(User $user, UserWorkExperience $userWorkExperience)
  {
    return response()->json([
      'data'   =>  $userWorkExperience
    ], 200);   
  }

  public function update(Request $request, User $user, UserWorkExperience $userWorkExperience)
  {
    $request->validate([
      'company_name'  =>  'required',
      'from'          =>  'required',
      'to'            =>  'required',
      'designation'   =>  'required',
      'uan_no'        =>  'required',
      'esic_no'       =>  'required',
    ]);

    $userWorkExperience->update($request->all());
      
    return response()->json([
      'data'  =>  $userWorkExperience
    ], 200);
  }
}
