<?php

namespace App\Http\Controllers;

use App\Pjp;
use App\PjpMarket;
use Illuminate\Http\Request;

class PjpMarketsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:api', 'company']);
    }



    /*
         * To get all pjp_markets
           *
         *@
         */
    public function index(Pjp $pjp)
    {
        // $pjp_markets = PjpMarket::where('pjp_id', '=', $pjp->id)
        // ->get();
        // dd($pjp_markets);
        $pjp_markets = $pjp->pjp_markets;
        // dd($pjp_markets);

        return response()->json([
            'data'     =>  $pjp_markets,
            'success'   =>  true
        ], 200);
    }

    /*
         * To store a new pjp_markets
         *
         *@
         */
    public function store(Request $request)
    {
        $request->validate([
            'pjp_id'    =>  'required',
            'market_name'    =>  'required'
        ]);

        $pjp_market = new PjpMarket($request->all());
        $request->company->pjp_markets()->save($pjp_market);

        return response()->json([
            'data'    =>  $pjp_market
        ], 201);
    }

    /*
         * To view a single pjp_market
         *
         *@
         */
    public function show(PjpMarket $pjpMarket)
    {
        return response()->json([
            'data'   =>  $pjpMarket
        ], 200);
    }

    /*
         * To update a pjp_market
         *
         *@
         */
    public function update(Request $request, PjpMarket $pjpMarket)
    {
        $request->validate([
            'pjp_id'    =>  'required',
            'market_name'    =>  'required'
        ]);

        $pjpMarket->update($request->all());

        return response()->json([
            'data'  =>  $pjpMarket
        ], 200);
    }
}
