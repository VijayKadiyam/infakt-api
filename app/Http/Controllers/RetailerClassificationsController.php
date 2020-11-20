<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\RetailerClassification;

class RetailerClassificationsController extends Controller
{
  public function __construct()
  {
    $this->middleware(['auth:api', 'company']);
  }

  /*
   * To get all classifications
     *
   *@
   */
  public function index()
  {
    $count = 0;
    if(request()->page && request()->rowsPerPage) {
      $retailer_classifications = request()->company->retailer_classifications();
      $count = $retailer_classifications->count();
      $retailer_classifications = $retailer_classifications->paginate(request()->rowsPerPage)->toArray();
      $retailer_classifications = $retailer_classifications['data'];
    } else {
      $retailer_classifications = request()->site->retailer_classifications; 
      $count = $retailer_classifications->count();
    }

    return response()->json([
      'data'     =>  $retailer_classifications,
      'count'    =>   $count
    ], 200);
  }

  /*
   * To store a new classification
   *
   *@
   */
  public function store(Request $request)
  {
    $request->validate([
      'name'    =>  'required'
    ]);

    $retailer_classification = new RetailerClassification($request->all());
    $request->company->retailer_classifications()->save($retailer_classification);

    return response()->json([
      'data'    =>  $retailer_classification
    ], 201); 
  }

  /*
   * To view a single classification
   *
   *@
   */
  public function show(RetailerClassification $retailerClassification)
  {
    return response()->json([
      'data'   =>  $retailerClassification
    ], 200);   
  }

  /*
   * To update a classification
   *
   *@
   */
  public function update(Request $request, RetailerClassification $retailerClassification)
  {
    $request->validate([
      'name'  =>  'required',
    ]);

    $retailerClassification->update($request->all());
      
    return response()->json([
      'data'  =>  $retailerClassification
    ], 200);
  }
}
