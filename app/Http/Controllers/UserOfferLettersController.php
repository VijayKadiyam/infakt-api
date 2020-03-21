<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\UserOfferLetter;
use App\User;

class UserOfferLettersController extends Controller
{
  public function __construct()
  {
    $this->middleware(['auth:api', 'company']);
  }

  public function index(Request $request, User $user)
  {
    $userOfferLetter = $user->user_offer_letters;

    return response()->json([
      'data'     =>  $userOfferLetter,
      'success'   =>  true
    ], 200);
  }

  public function store(Request $request, User $user)
  {
    $request->validate([
      'letter'          =>  'required',
    ]);

    $userOfferLetter = new UserOfferLetter($request->all());
    $user->user_offer_letters()->save($userOfferLetter);

    return response()->json([
      'data'    =>  $userOfferLetter
    ], 201); 
  }

  public function show(User $user, UserOfferLetter $userOfferLetter)
  {
    return response()->json([
      'data'   =>  $userOfferLetter
    ], 200);   
  }

  public function update(Request $request, User $user, UserOfferLetter $userOfferLetter)
  {
    $request->validate([
      'letter'        =>  'required',
    ]);

    $userOfferLetter->update($request->all());
      
    return response()->json([
      'data'  =>  $userOfferLetter
    ], 200);
  }
}
