<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\AllowanceType;

class AllowanceTypesController extends Controller
{
  public function __construct()
  {
    $this->middleware(['auth:api', 'company']);
  }

  /*
   * To get all allowance types
     *
   *@
   */
  public function index()
  {
    $allowanceTypes = request()->company->allowance_types;

    return response()->json([
      'data'     =>  $allowanceTypes,
      'success'   =>  true
    ], 200);
  }

  /*
   * To store a new allowance type
   *
   *@
   */
  public function store(Request $request)
  {
    $request->validate([
      'name'    =>  'required',
      'amount'    =>  'required',
    ]);

    $allowanceType = new AllowanceType($request->all());
    $request->company->allowance_types()->save($allowanceType);

    return response()->json([
      'data'    =>  $allowanceType
    ], 201); 
  }

  /*
   * To view a single allowance type
   *
   *@
   */
  public function show(AllowanceType $allowanceType)
  {

    return response()->json([
      'data'   =>  $allowanceType
    ], 200);   
  }

  /*
   * To update an allowance type
   *
   *@
   */
  public function update(Request $request, AllowanceType $allowanceType)
  {

    $request->validate([
      'name'  =>  'required',
    ]);

    $allowanceType->update($request->all());
    
    return response()->json([
      'data'  =>  $allowanceType
    ], 200);
  }
}
