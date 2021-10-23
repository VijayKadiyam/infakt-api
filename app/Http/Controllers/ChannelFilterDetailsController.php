<?php

namespace App\Http\Controllers;

use App\ChannelFilterDetail;
use Illuminate\Http\Request;

class ChannelFilterDetailsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:api', 'company']);
    }

    /*
       * To get all channel_filter_details
         *
       *@
       */
    public function index(Request $request)
    {
        $count = 0;
        if($request->userId && $request->date && $request->retailerId) {
            $channel_filter_details = request()->company->channel_filter_details()
                ->where('ba_1', '=', $request->userId)
                ->where('date', '=', $request->date)
                ->where('retailer_id', '=', $request->retailerId)
                ->get();
        }
        else if (request()->page && request()->rowsPerPage) {
            $channel_filter_details = request()->company->channel_filter_details();
            $count = $channel_filter_details->count();
            $channel_filter_details = $channel_filter_details->paginate(request()->rowsPerPage)->toArray();
            $channel_filter_details = $channel_filter_details['data'];
        } else {
            $channel_filter_details = request()->company->channel_filter_details;
            $count = $channel_filter_details->count();
        }

        return response()->json([
            'data'     =>  $channel_filter_details,
            'count'    =>   $count,
            'success'   =>  true
        ], 200);
    }

    /*
       * To store a new channel_filter_details
       *
       *@
       */
    public function store(Request $request)
    {
        $request->validate([
            'ba_1'    =>  'required'
        ]);

        if($request->id == null) {
            $channel_filter_detail = new ChannelFilterDetail($request->all());
            $request->company->channel_filter_details()->save($channel_filter_detail);
        } else {
            $channel_filter_detail = ChannelFilterDetail::where('id', '=', $request->id)
                ->first();
            if($channel_filter_detail) 
                $channel_filter_detail->update($request->all());
        }
        

        return response()->json([
            'data'    =>  $channel_filter_detail
        ], 201);
    }

    /*
       * To view a single channel_filter_detail
       *
       *@
       */
    public function show(ChannelFilterDetail $channel_filter_detail)
    {
        return response()->json([
            'data'   =>  $channel_filter_detail
        ], 200);
    }

    /*
       * To update a channel_filter_detail
       *
       *@
       */
    public function update(Request $request, ChannelFilterDetail $channel_filter_detail)
    {
        $request->validate([
            'ba_1'  =>  'required',
        ]);

        $channel_filter_detail->update($request->all());

        return response()->json([
            'data'  =>  $channel_filter_detail
        ], 200);
    }
}
