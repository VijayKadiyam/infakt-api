<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\CompanyLeave;

class CompanyLeavesController extends Controller
{
  public function __construct()
  {
    $this->middleware(['auth:api', 'company']);
  }

  /*
   * To get all company leaves
     *
   *@
   */
  public function index()
  {
    $companyleaves = request()->company->company_leaves;

    return response()->json([
      'data'     =>  $companyleaves,
      'success'   =>  true
    ], 200);
  }

  /*
   * To store a new company leave
   *
   *@
   */
  public function store(Request $request)
  {
    $request->validate([
      'leave_pattern_id'    =>  'required',
      'name'                =>  'required',
      'leaves'              =>  'required'
    ]);

    $companyLeave = new CompanyLeave($request->all());
    $request->company->company_leaves()->save($companyLeave);

    return response()->json([
      'data'    =>  $companyLeave
    ], 201); 
  }

  /*
   * To view a single company leave
   *
   *@
   */
  public function show($companyLeave)
  {
    $companyLeave = CompanyLeave::find($companyLeave);

    return response()->json([
      'data'   =>  $companyLeave
    ], 200);
  }

  /*
   * To update a company leave
   *
   *@
   */
  public function update(Request $request, $companyLeave)
  {
    $request->validate([
      'name'  =>  'required',
    ]);

    $companyLeave = CompanyLeave::find($companyLeave);
    $companyLeave->update($request->all());
      
    return response()->json([
      'data'  =>  $companyLeave
    ], 200);
  }
}
