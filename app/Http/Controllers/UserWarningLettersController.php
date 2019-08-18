<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\UserWarningLetter;

class UserWarningLettersController extends Controller
{
  public function __construct()
  {
    $this->middleware(['auth:api', 'company']);
  }

  public function index(Request $request)
  {
    $userWarningLetter = request()->user()->user_warning_letters;

    return response()->json([
      'data'     =>  $userWarningLetter
    ], 200);
  }

  public function store(Request $request)
  {
    $request->validate([
      'letter'          =>  'required',
    ]);

    $userWarningLetter = new UserWarningLetter($request->all());
    $request->user()->user_warning_letters()->save($userWarningLetter);

    return response()->json([
      'data'    =>  $userWarningLetter
    ], 201); 
  }

  public function show(UserWarningLetter $userWarningLetter)
  {
    return response()->json([
      'data'   =>  $userWarningLetter
    ], 200);   
  }

  public function update(Request $request, UserWarningLetter $userWarningLetter)
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
