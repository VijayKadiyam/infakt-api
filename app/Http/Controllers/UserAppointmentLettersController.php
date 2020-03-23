<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\UserAppointmentLetter;
use App\User;
use PDF;

class UserAppointmentLettersController extends Controller
{
  public function __construct()
  {
    $this->middleware(['auth:api', 'company'])
      ->except('download');
  }

  public function getAll(Request $request)
  {
    $users = $request->company->users;
    $userAppointmentLetters = [];
    foreach($users as $user) {
      foreach($user->user_appointment_letters as $letter)
      $userAppointmentLetters[] = $letter->toArray();
    }

    return response()->json([
      'data'     =>  $userAppointmentLetters,
      'success'   =>  true
    ], 200);
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
      'start_date'      =>  'required',
      'end_date'        =>  'required',
      'stc_issue_date'  =>  'required',
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

  public function stream(User $user, UserAppointmentLetter $userAppointmentLetter)
  {
    $data['user'] = $user;
    $data['letter'] = $userAppointmentLetter;

    $pdf = PDF::loadView('letters.al', $data);

    return $pdf->stream();
  }

  public function download(User $user, UserAppointmentLetter $userAppointmentLetter)
  {
    $data['user'] = $user;
    $data['letter'] = $userAppointmentLetter;

    $pdf = PDF::loadView('letters.al', $data);

    return $pdf->download($user->name . '-appointment-letter.pdf');
  }
}
