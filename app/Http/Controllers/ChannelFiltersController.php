<?php

namespace App\Http\Controllers;

use App\ChannelFilter;
use Illuminate\Http\Request;

class ChannelFiltersController extends Controller
{
  public function __construct()
  {
    $this->middleware(['auth:api', 'company']);
  }

  /*
     * To get all channel_filters
       *
     *@
     */
  public function index()
  {
    $count = 0;
    if (request()->page && request()->rowsPerPage) {
      $channel_filters = request()->company->channel_filters();
      $count = $channel_filters->count();
      $channel_filters = $channel_filters->paginate(request()->rowsPerPage)->toArray();
      $channel_filters = $channel_filters['data'];
    } else {
      $channel_filters = request()->company->channel_filters;
      $count = $channel_filters->count();
    }

    return response()->json([
      'data'     =>  $channel_filters,
      'count'    =>   $count
    ], 200);
  }

  /*
     * To store a new channel_filters
     *
     *@
     */
  public function store(Request $request)
  {
    $request->validate([
      'name'    =>  'required'
    ]);

    $channel_filter = new ChannelFilter($request->all());
    $request->company->channel_filters()->save($channel_filter);

    return response()->json([
      'data'    =>  $channel_filter
    ], 201);
  }

  /*
     * To view a single channel_filter
     *
     *@
     */
  public function show(ChannelFilter $channel_filter)
  {
    return response()->json([
      'data'   =>  $channel_filter
    ], 200);
  }

  /*
     * To update a channel_filter
     *
     *@
     */
  public function update(Request $request, ChannelFilter $channel_filter)
  {
    $request->validate([
      'name'  =>  'required',
    ]);

    $channel_filter->update($request->all());

    return response()->json([
      'data'  =>  $channel_filter
    ], 200);
  }
}
