<?php

namespace App\Http\Controllers;

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
    public function index()
    {
        $count = 0;
        if (request()->page && request()->rowsPerPage) {
            $pjp_markets = request()->company->pjp_markets();
            $count = $pjp_markets->count();
            $pjp_markets = $pjp_markets->paginate(request()->rowsPerPage)->toArray();
            $pjp_markets = $pjp_markets['data'];
        } else {
            $pjp_markets = request()->company->pjp_markets;
            $count = $pjp_markets->count();
        }

        return response()->json([
            'data'     =>  $pjp_markets,
            'count'    =>   $count
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
    public function show(PjpMarket $pjp_market)
    {
        return response()->json([
            'data'   =>  $pjp_market
        ], 200);
    }

    /*
         * To update a pjp_market
         *
         *@
         */
    public function update(Request $request, PjpMarket $pjp_market)
    {
        $request->validate([
            'pjp_id'    =>  'required',
            'market_name'    =>  'required'
        ]);

        $pjp_market->update($request->all());

        return response()->json([
            'data'  =>  $pjp_market
        ], 200);
    }
}
