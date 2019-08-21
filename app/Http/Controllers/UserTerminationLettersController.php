<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\UserTerminationLetter;

class UserTerminationLettersController extends Controller
{
  public function __construct()
  {
    $this->middleware(['auth:api', 'company']);
  }

  public function index(Request $request, User $user)
  {
    $userTerminationLetter = $user->user_termination_letters;

    return response()->json([
      'data'     =>  $userTerminationLetter,
      'success'   =>  true
    ], 200);
  }

  public function store(Request $request, User $user)
  {
    $request->validate([
      'letter'          =>  'required',
    ]);

    $userTerminationLetter = new userTerminationLetter($request->all());
    $user->user_termination_letters()->save($userTerminationLetter);

    return response()->json([
      'data'    =>  $userTerminationLetter
    ], 201); 
  }

  public function show(User $user, userTerminationLetter $userTerminationLetter)
  {
    return response()->json([
      'data'   =>  $userTerminationLetter
    ], 200);   
  }

  public function update(Request $request, User $user, userTerminationLetter $userTerminationLetter)
  {
    $request->validate([
      'letter'        =>  'required',
    ]);

    $userTerminationLetter->update($request->all());
      
    return response()->json([
      'data'  =>  $userTerminationLetter
    ], 200);
  }
}
