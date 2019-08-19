<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\UserRenewalLetter;
use App\User;

class UserRenewalLettersController extends Controller
{
  public function __construct()
  {
    $this->middleware(['auth:api', 'company']);
  }

  public function index(Request $request, User $user)
  {
    $userRenewalLetters = $user->user_renewal_letters;

    return response()->json([
      'data'     =>  $userRenewalLetters,
      'success'   =>  true
    ], 200);
  }

  public function store(Request $request, User $user)
  {
    $request->validate([
      'letter'          =>  'required',
    ]);

    $userRenewalLetter = new UserRenewalLetter($request->all());
    $user->user_renewal_letters()->save($userRenewalLetter);

    return response()->json([
      'data'    =>  $userRenewalLetter
    ], 201); 
  }

  public function show(User $user, UserRenewalLetter $userRenewalLetter)
  {
    return response()->json([
      'data'   =>  $userRenewalLetter
    ], 200);   
  }

  public function update(Request $request, User $user, UserRenewalLetter $userRenewalLetter)
  {
    $request->validate([
      'letter'        =>  'required',
    ]);

    $userRenewalLetter->update($request->all());
      
    return response()->json([
      'data'  =>  $userRenewalLetter
    ], 200);
  }
}
