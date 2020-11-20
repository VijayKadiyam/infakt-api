<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\RetailerCategory;

class RetailerCategoriesController extends Controller
{
  public function __construct()
  {
    $this->middleware(['auth:api', 'company']);
  }

  /*
   * To get all categories
     *
   *@
   */
  public function index()
  {
    $count = 0;
    if(request()->page && request()->rowsPerPage) {
      $retailer_categories = request()->company->retailer_categories();
      $count = $retailer_categories->count();
      $retailer_categories = $retailer_categories->paginate(request()->rowsPerPage)->toArray();
      $retailer_categories = $retailer_categories['data'];
    } else {
      $retailer_categories = request()->site->retailer_categories; 
      $count = $retailer_categories->count();
    }

    return response()->json([
      'data'     =>  $retailer_categories,
      'count'    =>   $count
    ], 200);
  }

  /*
   * To store a new category
   *
   *@
   */
  public function store(Request $request)
  {
    $request->validate([
      'name'    =>  'required'
    ]);

    $retailer_category = new RetailerCategory($request->all());
    $request->company->retailer_categories()->save($retailer_category);

    return response()->json([
      'data'    =>  $retailer_category
    ], 201); 
  }

  /*
   * To view a single cateogry
   *
   *@
   */
  public function show(RetailerCategory $retailerCategory)
  {
    return response()->json([
      'data'   =>  $retailerCategory
    ], 200);   
  }

  /*
   * To update a category
   *
   *@
   */
  public function update(Request $request, RetailerCategory $retailerCategory)
  {
    $request->validate([
      'name'  =>  'required',
    ]);

    $retailerCategory->update($request->all());
      
    return response()->json([
      'data'  =>  $retailerCategory
    ], 200);
  }
}
