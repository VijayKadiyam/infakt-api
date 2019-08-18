<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\UserRenewalLetter;

class UserRenewalLettersController extends Controller
{
  public function __construct()
  {
    $this->middleware(['auth:api', 'company']);
  }

  public function index(Request $request)
  {
    $userRenewalLetters = request()->user()->user_renewal_letters;

    return response()->json([
      'data'     =>  $userRenewalLetters
    ], 200);
  }

  public function store(Request $request)
  {
    $request->validate([
      'letter'          =>  'required',
    ]);

    $userRenewalLetter = new UserRenewalLetter($request->all());
    $request->user()->user_renewal_letters()->save($userRenewalLetter);

    return response()->json([
      'data'    =>  $userRenewalLetter
    ], 201); 
  }

  public function show(UserRenewalLetter $userRenewalLetter)
  {
    return response()->json([
      'data'   =>  $userRenewalLetter
    ], 200);   
  }

  public function update(Request $request, UserRenewalLetter $userRenewalLetter)
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
