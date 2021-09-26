<?php

namespace App\Http\Controllers;

use App\ChannelCompetitionOffer;
use Illuminate\Http\Request;

class ChannelCompetitionOffersController extends Controller
{
    public function __construct()
    {
      $this->middleware(['auth:api', 'company']);
    }
  
    /*
       * To get all channel_competition_offers
         *
       *@
       */
    public function index()
    {
      $count = 0;
      if (request()->page && request()->rowsPerPage) {
        $channel_competition_offers = request()->company->channel_competition_offers();
        $count = $channel_competition_offers->count();
        $channel_competition_offers = $channel_competition_offers->paginate(request()->rowsPerPage)->toArray();
        $channel_competition_offers = $channel_competition_offers['data'];
      } else {
        $channel_competition_offers = request()->company->channel_competition_offers;
        $count = $channel_competition_offers->count();
      }
  
      return response()->json([
        'data'     =>  $channel_competition_offers,
        'count'    =>   $count
      ], 200);
    }
  
    /*
       * To store a new channel_competition_offers
       *
       *@
       */
    public function store(Request $request)
    {
      $request->validate([
        'competitor_name'    =>  'required'
      ]);
  
      $channel_competition_offer = new ChannelCompetitionOffer($request->all());
      $request->company->channel_competition_offers()->save($channel_competition_offer);
  
      return response()->json([
        'data'    =>  $channel_competition_offer
      ], 201);
    }
  
    /*
       * To view a single channel_competition_offer
       *
       *@
       */
    public function show(ChannelCompetitionOffer $channel_competition_offer)
    {
      return response()->json([
        'data'   =>  $channel_competition_offer
      ], 200);
    }
  
    /*
       * To update a channel_competition_offer
       *
       *@
       */
    public function update(Request $request, ChannelCompetitionOffer $channel_competition_offer)
    {
      $request->validate([
        'competitor_name'  =>  'required',
      ]);
  
      $channel_competition_offer->update($request->all());
  
      return response()->json([
        'data'  =>  $channel_competition_offer
      ], 200);
    }
  }
