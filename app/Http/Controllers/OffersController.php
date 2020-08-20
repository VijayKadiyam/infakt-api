<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Offer;

class OffersController extends Controller
{
  public function __construct()
  {
    $this->middleware(['auth:api', 'company']);
  }

  /*
   * To get all offers
     *
   *@
   */
  public function index()
  {
    $offers = request()->company->offers;

    return response()->json([
      'data'     =>  $offers
    ], 200);
  }

  /*
   * To store a new offer
   *
   *@
   */
  public function store(Request $request)
  {
    $request->validate([
      'offer'    =>  'required',
      'offer_type_id'    =>  'required'
    ]);

    $offer = new Offer($request->all());
    $request->company->offers()->save($offer);

    return response()->json([
      'data'    =>  $offer
    ], 201); 
  }

  /*
   * To view a single offer
   *
   *@
   */
  public function show(Offer $offer)
  {
    return response()->json([
      'data'   =>  $offer
    ], 200);   
  }

  /*
   * To update an offer
   *
   *@
   */
  public function update(Request $request, Offer $offer)
  {
    $request->validate([
      'offer'  =>  'required',
      'offer_type_id'    =>  'required'
    ]);

    $offer->update($request->all());
      
    return response()->json([
      'data'  =>  $offer
    ], 200);
  }
}
