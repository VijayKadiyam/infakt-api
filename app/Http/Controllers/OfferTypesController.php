<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\OfferType;

class OfferTypesController extends Controller
{
  public function __construct()
  {
    $this->middleware(['auth:api', 'company']);
  }

  /*
   * To get all offer types
     *
   *@
   */
  public function index()
  {
    $offer_types = request()->company->offer_types;

    return response()->json([
      'data'     =>  $offer_types
    ], 200);
  }

  /*
   * To store a new offer types
   *
   *@
   */
  public function store(Request $request)
  {
    $request->validate([
      'name'    =>  'required'
    ]);

    $offerType = new OfferType($request->all());
    $request->company->offer_types()->save($offerType);

    return response()->json([
      'data'    =>  $offerType
    ], 201); 
  }

  /*
   * To view a single offer type
   *
   *@
   */
  public function show(OfferType $offer_type)
  {
    return response()->json([
      'data'   =>  $offer_type
    ], 200);   
  }

  /*
   * To update a offer type
   *
   *@
   */
  public function update(Request $request, OfferType $offer_type)
  {
    $request->validate([
      'name'  =>  'required',
    ]);

    $offer_type->update($request->all());
      
    return response()->json([
      'data'  =>  $offer_type
    ], 200);
  }
}
