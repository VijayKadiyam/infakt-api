<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\UserAppointmentLetter;

class UserAppointmentLettersController extends Controller
{
  public function __construct()
  {
    $this->middleware(['auth:api', 'company']);
  }

  public function index(Request $request)
  {
    $userAppointmentLetter = request()->user()->user_appointment_letters;

    return response()->json([
      'data'     =>  $userAppointmentLetter
    ], 200);
  }

  public function store(Request $request)
  {
    $request->validate([
      'letter'          =>  'required',
    ]);

    $userAppointmentLetter = new UserAppointmentLetter($request->all());
    $request->user()->user_appointment_letters()->save($userAppointmentLetter);

    return response()->json([
      'data'    =>  $userAppointmentLetter
    ], 201); 
  }

  public function show(UserAppointmentLetter $userAppointmentLetter)
  {
    return response()->json([
      'data'   =>  $userAppointmentLetter
    ], 200);   
  }

  public function update(Request $request, UserAppointmentLetter $userAppointmentLetter)
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
