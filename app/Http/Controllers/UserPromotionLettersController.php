<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\UserPromotionLetter;
use App\User;

class UserPromotionLettersController extends Controller
{
  public function __construct()
  {
    $this->middleware(['auth:api', 'company']);
  }

  public function index(Request $request, User $user)
  {
    $userPromotionLetter = $user->user_promotion_letters;

    return response()->json([
      'data'     =>  $userPromotionLetter,
      'success'   =>  true
    ], 200);
  }

  public function store(Request $request, User $user)
  {
    $request->validate([
      'letter'          =>  'required',
    ]);

    $UserPromotionLetter = new UserPromotionLetter($request->all());
    $user->user_promotion_letters()->save($UserPromotionLetter);

    return response()->json([
      'data'    =>  $UserPromotionLetter
    ], 201); 
  }

  public function show(User $user, UserPromotionLetter $userPromotionLetter)
  {
    return response()->json([
      'data'   =>  $userPromotionLetter
    ], 200);   
  }

  public function update(Request $request, User $user, UserPromotionLetter $userPromotionLetter)
  {
    $request->validate([
      'letter'        =>  'required',
    ]);

    $userPromotionLetter->update($request->all());
      
    return response()->json([
      'data'  =>  $userPromotionLetter
    ], 200);
  }
}