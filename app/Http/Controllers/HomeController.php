<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
  public function __construct()
  {
    $this->middleware(['auth:api', 'company']);
  }

  public function count(Request $request)
  {
    $users = $request->company->users()
          ->whereHas('roles', function($q){ 
            $q->where('name', '=', 'Employee');
          });
    // Employee Count
    $count['employees'] = $users->count();
    // Active Employee Count
    $count['activeEmployees'] = $users->where('active', '=', 1)->count();
    // Present Employees
    if($request->date) {
      $date = $request->date;
      $count['presentEmployees'] = $request->company->users()->whereHas('user_attendances', function($q) use($date) {
          $q->where('date', '=', $date);
        })->count();
    }
    $count['absentEmployees'] = $count['activeEmployees'] - $count['presentEmployees'];
    $count['employeesOnLeave'] = 0;
    $count['leaveApplication'] = 0;
    $count['leaveApproval'] = 0;
    $count['leaveRejected'] = 0;
    
    return response()->json([
      'count' =>  $count
    ]);
  }
}
