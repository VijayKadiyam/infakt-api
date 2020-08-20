<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Company;

class CompanyLeavePatternController extends Controller
{
  public function __construct()
  {
    $this->middleware(['auth:api']);
  }

  /*
   * Assign leave pattern to company
   *
   *@
   */
  public function store(Request $request)
  {
    $request->validate([
      'leave_pattern_id'    =>  'required',
      'company_id' =>  'required'
    ]);

    $company = Company::find($request->company_id);
    $company->assignLeavePattern($request->leave_pattern_id);
    $companyLeavePattern = Company::with('leave_patterns')->find($company->id);

    return response()->json([
        'data'  =>  $companyLeavePattern
    ], 201); 
  }
}
