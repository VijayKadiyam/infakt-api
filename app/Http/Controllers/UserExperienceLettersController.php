<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\UserExperienceLetter;

class UserExperienceLettersController extends Controller
{
  public function __construct()
  {
    $this->middleware(['auth:api', 'company']);
  }
  public function index(Request $request)
  {
    $userExperienceLetter = request()->user()->user_experience_letters;

    return response()->json([
      'data'     =>  $userExperienceLetter
    ], 200);
  }

  public function store(Request $request)
  {
    $request->validate([
      'letter'          =>  'required',
    ]);

    $userExperienceLetter = new UserExperienceLetter($request->all());
    $request->user()->user_experience_letters()->save($userExperienceLetter);

    return response()->json([
      'data'    =>  $userExperienceLetter
    ], 201); 
  }

  public function show(UserExperienceLetter $userExperienceLetter)
  {
    return response()->json([
      'data'   =>  $userExperienceLetter
    ], 200);   
  }

  public function update(Request $request, UserExperienceLetter $userExperienceLetter)
  {
    $request->validate([
      'letter'        =>  'required',
    ]);

    $userExperienceLetter->update($request->all());
      
    return response()->json([
      'data'  =>  $userExperienceLetter
    ], 200);
  }
}
