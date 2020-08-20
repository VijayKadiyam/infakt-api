<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\UserAttendance;
use App\UserAttendanceBreak;

class UserAttendanceBreaksController extends Controller
{
  public function __construct()
  {
    $this->middleware(['auth:api', 'company']);
  }

  /*
   * To get all user attendance breaks
     *
   *@
   */
  public function index(UserAttendance $userAttendance)
  {
    $userAttendanceBreaks = $userAttendance->user_attendance_breaks;

    return response()->json([
      'data'     =>  $userAttendanceBreaks
    ], 200);
  }

  /*
   * To store a new user attendance breaks
   *
   *@
   */
  public function store(Request $request, UserAttendance $userAttendance)
  {
    $request->validate([
      'break_type_id' =>  'required',
      'start_time'    =>  'required'
    ]);

    $userAttendanceBreak = new UserAttendanceBreak($request->all());
    $userAttendance->user_attendance_breaks()->save($userAttendanceBreak);
    $userAttendanceBreak->break_type = $userAttendanceBreak->break_type;

    return response()->json([
      'data'    =>  $userAttendanceBreak
    ], 201); 
  }

  /*
   * To view a single user attendance
   *
   *@
   */
  public function show(UserAttendance $userAttendance, UserAttendanceBreak $userAttendanceBreak)
  {
    return response()->json([
      'data'   =>  $userAttendanceBreak
    ], 200);   
  }

  /*
   * To update a user attendance
   *
   *@
   */
  public function update(Request $request, UserAttendance $userAttendance, UserAttendanceBreak $userAttendanceBreak)
  {
    $request->validate([
      'break_type_id' =>  'required',
      'start_time'    =>  'required'
    ]);

    $userAttendanceBreak->update($request->all());
      
    return response()->json([
      'data'  =>  $userAttendanceBreak
    ], 200);
  }
}
