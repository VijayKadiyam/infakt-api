<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\CompanyState;
use App\CompanyStateHoliday;

class CompanyStateHolidaysController extends Controller
{
  public function __construct()
  {
    $this->middleware(['auth:api', 'company']);
  }

  /*
   * To get all company state holidays
     *
   *@
   */
  public function index(CompanyState $companyState)
  {
    $companyStateHolidays = $companyState->company_state_holidays;

    return response()->json([
      'data'     =>  $companyStateHolidays
    ], 200);
  }

  /*
   * To store a new company state holiday
   *
   *@
   */
  public function store(Request $request, CompanyState $companyState)
  {
    $request->validate([
      'holiday_id'    =>  'required'
    ]);

    $companyStateHoliday = new CompanyStateHoliday($request->all());
    $companyState->company_state_branches()->save($companyStateHoliday);

    return response()->json([
      'data'    =>  $companyStateHoliday
    ], 201); 
  }

  /*
   * To view a single company state holiday
   *
   *@
   */
  public function show(CompanyState $companyState, CompanyStateHoliday $companyStateHoliday)
  {
    return response()->json([
      'data'   =>  $companyStateHoliday
    ], 200);   
  }

  /*
   * To update a company state holiday
   *
   *@
   */
  public function update(Request $request, CompanyState $companyState, CompanyStateHoliday $companyStateHoliday)
  {
    $request->validate([
      'holiday_id'  =>  'required',
    ]);

    $companyStateHoliday->update($request->all());
      
    return response()->json([
      'data'  =>  $companyStateHoliday
    ], 200);
  }

  public function destroy(CompanyState $companyState, CompanyStateHoliday $companyStateHoliday)
  {
    $companyStateHoliday->delete();

    return response()->json([
      'message' =>  'Deleted Successfuly'
    ], 200);

    dd(1);
  }
}
