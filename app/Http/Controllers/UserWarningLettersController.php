<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\UserWarningLetter;
use App\User;

class UserWarningLettersController extends Controller
{
  public function __construct()
  {
    $this->middleware(['auth:api', 'company']);
  }

  public function index(Request $request, User $user)
  {
    $userWarningLetter = $user->user_warning_letters;

    return response()->json([
      'data'     =>  $userWarningLetter,
      'success'   =>  true
    ], 200);
  }

  public function store(Request $request, User $user)
  {
    $request->validate([
      'letter'          =>  'required',
    ]);

    $userWarningLetter = new UserWarningLetter($request->all());
    $user->user_warning_letters()->save($userWarningLetter);

    return response()->json([
      'data'    =>  $userWarningLetter
    ], 201); 
  }

  public function show(User $user, UserWarningLetter $userWarningLetter)
  {
    return response()->json([
      'data'   =>  $userWarningLetter
    ], 200);   
  }

  public function update(Request $request, User $user, UserWarningLetter $userWarningLetter)
  {
    $request->validate([
      'letter'        =>  'required',
    ]);

    $userWarningLetter->update($request->all());
      
    return response()->json([
      'data'  =>  $userWarningLetter
    ], 200);
  }
}
