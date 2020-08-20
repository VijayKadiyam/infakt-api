<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Holiday;

class HolidaysController extends Controller
{
  /*
   * To get all holidays
     *
   *@
   */
  public function index(Request $request)
  {
    $holidays = Holiday::get();

    return response()->json([
      'data'  =>  $holidays
    ], 200);
  }

  /*
   * To store a new holiday
   *
   *@
   */
  public function store(Request $request)
  {
    $request->validate([
      'name'  =>  'required',
      'date'  =>  'required'
    ]);

    $holiday = new Holiday(request()->all());
    $holiday->save();

    return response()->json([
      'data'  =>  $holiday
    ], 201); 
  }

  /*
   * To view a single holiday
   *
   *@
   */
  public function show(Holiday $holiday)
  {
    return response()->json([
      'data'  =>  $holiday
    ], 200);   
  }

  /*
   * To update a holiday
   *
   *@
   */
  public function update(Request $request, Holiday $holiday)
  {
    $request->validate([
      'name'  =>  'required',
    ]);

    $holiday->update($request->all());
      
    return response()->json([
      'data'  =>  $holiday
    ], 200);
  }
}
