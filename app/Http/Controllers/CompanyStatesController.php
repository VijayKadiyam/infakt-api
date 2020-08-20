<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\CompanyState;

class CompanyStatesController extends Controller
{
  public function __construct()
  {
    $this->middleware(['auth:api', 'company']);
  }

  /*
   * To get all company states
     *
   *@
   */
  public function index()
  {
    $companyStates = request()->company->company_states;

    return response()->json([
      'data'     =>  $companyStates
    ], 200);
  }

  /*
   * To store a new company state
   *
   *@
   */
  public function store(Request $request)
  {
    $request->validate([
      'name'    =>  'required'
    ]);

    $companyState = new CompanyState($request->all());
    $request->company->company_states()->save($companyState);

    return response()->json([
      'data'    =>  $companyState
    ], 201); 
  }

  /*
   * To view a single company state
   *
   *@
   */
  public function show(CompanyState $companyState)
  {
    return response()->json([
      'data'   =>  $companyState
    ], 200);   
  }

  /*
   * To update a company state
   *
   *@
   */
  public function update(Request $request, CompanyState $companyState)
  {
    $request->validate([
      'name'  =>  'required',
    ]);

    $companyState->update($request->all());
      
    return response()->json([
      'data'  =>  $companyState
    ], 200);
  }
}
