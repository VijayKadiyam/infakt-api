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
    $count = 0;
    if(request()->page && request()->rowsPerPage) {
      $company_states = request()->company->company_states();
      $count = $company_states->count();
      $company_states = $company_states->paginate(request()->rowsPerPage)->toArray();
      $company_states = $company_states['data'];
    } else {
      $company_states = request()->site->company_states; 
      $count = $company_states->count();
    }

    return response()->json([
      'data'     =>  $company_states,
      'count'    =>   $count
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
