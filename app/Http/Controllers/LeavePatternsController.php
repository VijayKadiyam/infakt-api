<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\LeavePattern;

class LeavePatternsController extends Controller
{
  /*
   * To get all leave patterns
   *
   *@
   */
  public function index(Request $request)
  {
    $leave_patterns = LeavePattern::get();

    return response()->json([
      'data'  =>  $leave_patterns
    ], 200);
  }

  /*
   * To store a new leave pattern
   *
   *@
   */
  public function store(Request $request)
  {
    $request->validate([
      'name'  =>  'required'
    ]);

    $leavePattern = new LeavePattern(request()->all());
    $leavePattern->save();

    return response()->json([
      'data'  =>  $leavePattern
    ], 201); 
  }

  /*
   * To view a single leave pattern
   *
   *@
   */
  public function show(LeavePattern $leave_pattern)
  {
    return response()->json([
      'data'  =>  $leave_pattern
    ], 200);   
  }

  /*
   * To update a leave pattern
   *
   *@
   */
  public function update(Request $request, LeavePattern $leave_pattern)
  {
    $request->validate([
      'name'  =>  'required',
    ]);

    $leave_pattern->update($request->all());
      
    return response()->json([
      'data'  =>  $leave_pattern
    ], 200);
  }
}
