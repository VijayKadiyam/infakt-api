<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\CompanyState;
use App\CompanyStateBranch;

class CompanyStateBranchesController extends Controller
{
  public function __construct()
  {
    $this->middleware(['auth:api', 'company']);
  }

  /*
   * To get all company state branches
     *
   *@
   */
  public function index(CompanyState $companyState)
  {
    $companyStateBranches = $companyState->company_state_branches;

    return response()->json([
      'data'     =>  $companyStateBranches
    ], 200);
  }

  /*
   * To store a new company state branch
   *
   *@
   */
  public function store(Request $request, CompanyState $companyState)
  {
    $request->validate([
      'name'    =>  'required'
    ]);

    $companyStateBranch = new CompanyStateBranch($request->all());
    $companyState->company_state_branches()->save($companyStateBranch);

    return response()->json([
      'data'    =>  $companyStateBranch
    ], 201); 
  }

  /*
   * To view a single company state branch
   *
   *@
   */
  public function show(CompanyState $companyState, CompanyStateBranch $companyStateBranch)
  {
    return response()->json([
      'data'   =>  $companyStateBranch
    ], 200);   
  }

  /*
   * To update a company state branch
   *
   *@
   */
  public function update(Request $request, CompanyState $companyState, CompanyStateBranch $companyStateBranch)
  {
    $request->validate([
      'name'  =>  'required',
    ]);

    $companyStateBranch->update($request->all());
      
    return response()->json([
      'data'  =>  $companyStateBranch
    ], 200);
  }
}
