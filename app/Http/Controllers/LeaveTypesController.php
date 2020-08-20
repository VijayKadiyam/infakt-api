<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\LeaveType;

class LeaveTypesController extends Controller
{
  public function __construct()
  {
    $this->middleware(['auth:api', 'company']);
  }

  /*
   * To get all leave types
     *
   *@
   */
  public function index()
  {
    $leaveTypes = request()->company->leave_types;

    return response()->json([
      'data'     =>  $leaveTypes,
      'success'   =>  true
    ], 200);
  }

  /*
   * To store a new leave type
   *
   *@
   */
  public function store(Request $request)
  {
    $request->validate([
      'name'    =>  'required',
    ]);

    $leaveType = new LeaveType($request->all());
    $request->company->leave_types()->save($leaveType);

    return response()->json([
      'data'    =>  $leaveType
    ], 201); 
  }

  /*
   * To view a single leave type
   *
   *@
   */
  public function show(LeaveType $leaveType)
  {

    return response()->json([
      'data'   =>  $leaveType
    ], 200);   
  }

  /*
   * To update an leave type
   *
   *@
   */
  public function update(Request $request, LeaveType $leaveType)
  {

    $request->validate([
      'name'  =>  'required',
    ]);

    $leaveType->update($request->all());
    
    return response()->json([
      'data'  =>  $leaveType
    ], 200);
  }

}
