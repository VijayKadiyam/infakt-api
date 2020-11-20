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

  public function masters(Request $request)
  {
    $offerTypesController = new OfferTypesController();
    $offerTypesResponse = $offerTypesController->index($request);

    return response()->json([
      'offer_types'=>  $offerTypesResponse->getData()->data,
    ], 200);
  }

  /*
   * To get all offers
     *
   *@
   */
  public function index()
  {
    $count = 0;
    if(request()->page && request()->rowsPerPage) {
      $offers = request()->company->offers();
      $count = $offers->count();
      $offers = $offers->paginate(request()->rowsPerPage)->toArray();
      $offers = $offers['data'];
    } else {
      $offers = request()->company->offers; 
      $count = $offers->count();
    }

    return response()->json([
      'data'     =>  $offers,
      'count'    =>   $count
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
