<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\UserAppointmentLetter;
use App\User;

class UserAppointmentLettersController extends Controller
{
  public function __construct()
  {
    $this->middleware(['auth:api', 'company']);
  }

  public function index(Request $request, User $user)
  {
    $userAppointmentLetter = $user->user_appointment_letters;

    return response()->json([
      'data'     =>  $userAppointmentLetter,
      'success'   =>  true
    ], 200);
  }

  public function store(Request $request, User $user)
  {
    $request->validate([
      'letter'          =>  'required',
    ]);

    $userAppointmentLetter = new UserAppointmentLetter($request->all());
    $user->user_appointment_letters()->save($userAppointmentLetter);

    return response()->json([
      'data'    =>  $userAppointmentLetter
    ], 201); 
  }

  public function show(User $user, UserAppointmentLetter $userAppointmentLetter)
  {
    return response()->json([
      'data'   =>  $userAppointmentLetter
    ], 200);   
  }

  public function update(Request $request, User $user, UserAppointmentLetter $userAppointmentLetter)
  {
    $request->validate([
      'letter'        =>  'required',
    ]);

    $userAppointmentLetter->update($request->all());
      
    return response()->json([
      'data'  =>  $userAppointmentLetter
    ], 200);
  }
}
