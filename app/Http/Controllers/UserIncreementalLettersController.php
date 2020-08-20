<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\UserIncreementalLetter;
use App\User;

class UserIncreementalLettersController extends Controller
{
  public function __construct()
  {
    $this->middleware(['auth:api', 'company']);
  }

  public function index(Request $request, User $user)
  {
    $userIncreementalLetter = $user->user_increemental_letters;

    return response()->json([
      'data'     =>  $userIncreementalLetter,
      'success'   =>  true
    ], 200);
  }

  public function store(Request $request, User $user)
  {
    $request->validate([
      'letter'          =>  'required',
    ]);

    $userIncreementalLetter = new UserIncreementalLetter($request->all());
    $user->user_increemental_letters()->save($userIncreementalLetter);

    return response()->json([
      'data'    =>  $userIncreementalLetter
    ], 201); 
  }

  public function show(User $user, UserIncreementalLetter $userIncreementalLetter)
  {
    return response()->json([
      'data'   =>  $userIncreementalLetter
    ], 200);   
  }

  public function update(Request $request, User $user, UserIncreementalLetter $userIncreementalLetter)
  {
    $request->validate([
      'letter'        =>  'required',
    ]);

    $userIncreementalLetter->update($request->all());
      
    return response()->json([
      'data'  =>  $userIncreementalLetter
    ], 200);
  }
}
