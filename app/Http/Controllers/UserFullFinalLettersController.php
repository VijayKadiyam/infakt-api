<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\UserFullFinalLetter;

class UserFullFinalLettersController extends Controller
{
  public function __construct()
  {
    $this->middleware(['auth:api', 'company']);
  }

  public function index(Request $request, User $user)
  {
    $userFullFinalLetter = $user->user_full_final_letters;

    return response()->json([
      'data'     =>  $userFullFinalLetter,
      'success'   =>  true
    ], 200);
  }

  public function store(Request $request, User $user)
  {
    $request->validate([
      'letter'          =>  'required',
    ]);

    $userFullFinalLetter = new UserFullFinalLetter($request->all());
    $user->user_full_final_letters()->save($userFullFinalLetter);

    return response()->json([
      'data'    =>  $userFullFinalLetter
    ], 201); 
  }

  public function show(User $user, UserFullFinalLetter $userFullFinalLetter)
  {
    return response()->json([
      'data'   =>  $userFullFinalLetter
    ], 200);   
  }

  public function update(Request $request, User $user, UserFullFinalLetter $userFullFinalLetter)
  {
    $request->validate([
      'letter'        =>  'required',
    ]);

    $userFullFinalLetter->update($request->all());
      
    return response()->json([
      'data'  =>  $userFullFinalLetter
    ], 200);
  }
}
