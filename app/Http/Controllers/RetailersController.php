<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Retailer;
use App\ReferencePlan;

class RetailersController extends Controller
{
  public function __construct()
  {
    $this->middleware(['auth:api', 'company']);
  }

  /*
   * To get all retailers
     *
   *@
   */
  public function index(ReferencePlan $referencePlan)
  {
    $retailers = $referencePlan->retailers;

    return response()->json([
      'data'     =>  $retailers,
      'success'   =>  true
    ], 200);
  }

  /*
   * To store a new retailer
   *
   *@
   */
  public function store(Request $request, ReferencePlan $referencePlan)
  {
    $request->validate([
      'name'    =>  'required',
      'address' =>  'required'
    ]);

    $retailer = new Retailer($request->all());
    $referencePlan->retailers()->save($retailer);

    return response()->json([
      'data'    =>  $retailer
    ], 201); 
  }

  /*
   * To view a single retailer
   *
   *@
   */
  public function show(ReferencePlan $referencePlan, Retailer $retailer)
  {
    return response()->json([
      'data'   =>  $retailer
    ], 200);   
  }

  /*
   * To update a retailer
   *
   *@
   */
  public function update(Request $request, ReferencePlan $referencePlan, Retailer $retailer)
  {
    $request->validate([
      'name'  =>  'required',
    ]);

    $retailer->update($request->all());
      
    return response()->json([
      'data'  =>  $retailer
    ], 200);
  }
}
