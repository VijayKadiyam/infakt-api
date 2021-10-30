<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Requisition;

class RequisitionsController extends Controller
{
    public function __construct()
  {
    $this->middleware(['auth:api', 'company']);
  }

  /*
   * To get all requisitions
     *
   *@
   */
  public function index()
  {
    $requisitions = request()->company->requisitions;

    return response()->json([
      'data'     => $requisitions,
      'success'  => true
    ], 200);
  }

  /*
   * To store a new requisition
   *
   *@
   */
  public function store(Request $request)
  {
    $request->validate([
      'title'    =>  'required'
    ]);

    $requisition = new Requisition($request->all());
    $request->company->requisitions()->save($requisition);

    return response()->json([
      'data'    =>  $requisition,
      'success' =>  true
    ], 201); 
  }

  /*
   * To view a single requisition
   *
   *@
   */
  public function show(Requisition $requisition)
  {
    return response()->json([
      'data'   =>  $requisition
    ], 200);   
  }

  /*
   * To update a requisition
   *
   *@
   */
  public function update(Request $request, Requisition $requisition)
  {
    $request->validate([
      'title'  =>  'required',
    ]);

    $requisition->update($request->all());
    
    return response()->json([
      'data'  =>  $requisition,
      'success' =>  true
    ], 200);
  }
}
