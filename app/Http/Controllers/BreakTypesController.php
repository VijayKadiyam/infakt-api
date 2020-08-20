<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\BreakType;
use App\Company;

class BreakTypesController extends Controller
{
  public function __construct()
  {
    $this->middleware(['auth:api', 'company']);
  }

  /*
   * To get all company break types
     *
   *@
   */
  public function index()
  {
    $companyBreakTypes = request()->company->break_types;

    return response()->json([
      'data'     =>  $companyBreakTypes
    ], 200);
  }

  /*
   * To store a new company break type
   *
   *@
   */
  public function store(Request $request)
  {
    $request->validate([
      'name'    =>  'required'
    ]);

    $companyBreakType = new BreakType($request->all());
    $request->company->break_types()->save($companyBreakType);

    return response()->json([
      'data'    =>  $companyBreakType
    ], 201); 
  }

  /*
   * To view a single company break type
   *
   *@
   */
  public function show($breakType)
  {
    $breakType = request()->company->break_types->find($breakType);

    return response()->json([
      'data'   =>  $breakType
    ], 200);   
  }

  /*
   * To update a company designation
   *
   *@
   */
  public function update(Request $request, BreakType $breakType)
  {

    $request->validate([
      'name'  =>  'required',
    ]);

    $breakType->update($request->all());
    
    return response()->json([
      'data'  =>  $breakType
    ], 200);
  }
}
