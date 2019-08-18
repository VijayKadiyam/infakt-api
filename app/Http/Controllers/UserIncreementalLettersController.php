<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\UserIncreementalLetter;
class UserIncreementalLettersController extends Controller
{
  public function __construct()
  {
    $this->middleware(['auth:api', 'company']);
  }

  public function index(Request $request)
  {
    $userIncreementalLetter = request()->user()->user_increemental_letters;

    return response()->json([
      'data'     =>  $userIncreementalLetter
    ], 200);
  }

  public function store(Request $request)
  {
    $request->validate([
      'letter'          =>  'required',
    ]);

    $userIncreementalLetter = new UserIncreementalLetter($request->all());
    $request->user()->user_increemental_letters()->save($userIncreementalLetter);

    return response()->json([
      'data'    =>  $userIncreementalLetter
    ], 201); 
  }

  public function show(UserIncreementalLetter $userIncreementalLetter)
  {
    return response()->json([
      'data'   =>  $userIncreementalLetter
    ], 200);   
  }

  public function update(Request $request, UserIncreementalLetter $userIncreementalLetter)
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
