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

  public function masters(Request $request)
  {
    $retailerClassificationsController = new RetailerClassificationsController();
    $retailerClassificationsResponse = $retailerClassificationsController->index($request);

    $retailerCategoriesController = new RetailerCategoriesController();
    $retailerCategoriesResponse = $retailerCategoriesController->index($request);

    return response()->json([
      'retailer_classifications'  =>  $retailerClassificationsResponse->getData()->data,
      'retailer_categories'      =>  $retailerCategoriesResponse->getData()->data,
    ], 200);
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

  /*
   * To get Un Approved Retailers
   *
   *@
   */
  public function unApprovedRetailers()
  {
    $retailers = [];
    $referencePlans = request()->company->reference_plans;
    foreach ($referencePlans as $referencePlan) {
      // dd($referencePlan->retailers->where('approved', '=', '0')->toArray());
      $rets = $referencePlan->retailers->where('approved', '=', '0')->toArray();
      foreach ($rets as $ret) {
        $retailers[] = $ret;
      }
    }

    return response()->json([
      'data'     =>  $retailers,
      'success'   =>  true
    ], 200);
  }

  public function singleApproveRetailer(Request $request)
  {
    $retailer = Retailer::where('id', '=', $request->id)->first();
    
    return response()->json([
      'data'   =>  $retailer
    ], 200);  
  }

  public function approveRetailer(Request $request)
  {
    $request->validate([
      'retailer_id' => 'required',
      'approved'    =>  'required'
    ]);

    $retailer = Retailer::where('id', '=', $request->retailer_id)->first();
    $retailer->update($request->all());
    
    return response()->json([
      'data'   =>  $retailer
    ], 200);  
  }

  public function list_of_retailer()
  {
    $retailer = Retailer::all();
    return response()->json([
      'data'   =>  $retailer
    ], 200);  
  }
}
