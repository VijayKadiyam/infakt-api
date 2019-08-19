<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\UserExperienceLetter;
use App\User;

class UserExperienceLettersController extends Controller
{
  public function __construct()
  {
    $this->middleware(['auth:api', 'company']);
  }
  public function index(Request $request, User $user)
  {
    $userExperienceLetter = $user->user_experience_letters;

    return response()->json([
      'data'     =>  $userExperienceLetter,
      'success'   =>  true
    ], 200);
  }

  public function store(Request $request, User $user)
  {
    $request->validate([
      'letter'          =>  'required',
    ]);

    $userExperienceLetter = new UserExperienceLetter($request->all());
    $user->user_experience_letters()->save($userExperienceLetter);

    return response()->json([
      'data'    =>  $userExperienceLetter
    ], 201); 
  }

  public function show(User $user, UserExperienceLetter $userExperienceLetter)
  {
    return response()->json([
      'data'   =>  $userExperienceLetter
    ], 200);   
  }

  public function update(Request $request, User $user, UserExperienceLetter $userExperienceLetter)
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
