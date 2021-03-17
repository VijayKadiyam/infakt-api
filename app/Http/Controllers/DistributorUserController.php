<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;

class DistributorUserController extends Controller
{
  public function store(Request $request)
  {
    $request->validate([
      'user_id'         =>  'required',
      'distributor_id'  =>  'required'
    ]);

    $user =  User::find($request->user_id);
    $distributor =  User::find($request->distributor_id);
    if($request->op == 'assign')
      $user->assignDistributor($distributor->id);
    if($request->op == 'unassign')
      $user->unassignDistributor($distributor->id);
    $distributorUser = User::with('distributors')->find($request->user_id);

    return response()->json([
    'data'    =>  $distributorUser
    ], 201); 
  }
}
