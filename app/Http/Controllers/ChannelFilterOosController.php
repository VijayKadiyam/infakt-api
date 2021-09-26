<?php

namespace App\Http\Controllers;

use App\ChannelFilterOos;
use App\ChannelFilterOosSku;
use Illuminate\Http\Request;

class ChannelFilterOosController extends Controller
{
    public function __construct()
  {
    $this->middleware(['auth:api', 'company']);
  }

  public function index()
  {
    $count = 0;
    if(request()->page && request()->rowsPerPage) {
        $channel_filter_oos = request()->company->channel_filter_oos();
        $count = $channel_filter_oos->count();
        $channel_filter_oos = $channel_filter_oos->paginate(request()->rowsPerPage)->toArray();
        $channel_filter_oos = $channel_filter_oos['data'];
      } else {
        $channel_filter_oos = request()->company->channel_filter_oos; 
        $count = $channel_filter_oos->count();
      }
  
      return response()->json([
        'data'     =>  $channel_filter_oos,
        'count'    =>   $count
      ], 200);
  }

  /*
   * To store a new channel$channel_filter_oos
   *
   *@
   */
  public function store(Request $request)
  {
    // return $request->all();
    $request->validate([
      'channel_filter_id'  =>  'required',
    ]);


    if ($request->id == null || $request->id == '') {
      // Save ChannelFilterOos
      $channel_filter_oos = new ChannelFilterOos(request()->all());
      $request->company->channel_filter_oos()->save($channel_filter_oos);

      // Save ChannelFilterOos Expiries
      if (isset($request->skus))
        foreach ($request->skus as $sku) {
          $sku = new ChannelFilterOosSku($sku);
          $channel_filter_oos->channel_filter_oos_skus()->save($sku);
        }
      // ---------------------------------------------------
    } else {
      // Update ChannelFilterOos
      $channel_filter_oos = ChannelFilterOos::find($request->id);
      $channel_filter_oos->update($request->all());
      
      // Check if ChannelFilterOos Sku deleted
      if (isset($request->skus)){
        $channel_filter_oosSkuIdResponseArray = array_pluck($request->skus, 'id');
      }
      else
      $channel_filter_oosSkuIdResponseArray = [];
      $channel_filter_oosId = $channel_filter_oos->id;
      $channel_filter_oosSkuIdArray = array_pluck(ChannelFilterOosSku::where('channel_filter_oos_id', '=', $channel_filter_oosId)->get(), 'id');
      $differenceChannelFilterOosSkuIds = array_diff($channel_filter_oosSkuIdArray, $channel_filter_oosSkuIdResponseArray);
      // Delete which is there in the database but not in the response
      if ($differenceChannelFilterOosSkuIds)
      foreach ($differenceChannelFilterOosSkuIds as $differenceChannelFilterOosSkuId) {
        $channel_filter_oosSku = ChannelFilterOosSku::find($differenceChannelFilterOosSkuId);
        $channel_filter_oosSku->delete();
      }
      
      // Update ChannelFilterOos Sku
      if (isset($request->skus))
      foreach ($request->skus as $sku) {
        if (!isset($sku['id'])) {
          $channel_filter_oos_sku = new ChannelFilterOosSku($sku);
          $channel_filter_oos->channel_filter_oos_skus()->save($channel_filter_oos_sku);
        } else {
          $channel_filter_oos_sku = ChannelFilterOosSku::find($sku['id']);
          $channel_filter_oos_sku->update($sku);
          }
        }

      // ---------------------------------------------------

    }

    $channel_filter_oos->channel_filter_oos_skus = $channel_filter_oos->channel_filter_oos_skus;
    return response()->json([
      'data'    =>  $channel_filter_oos
    ], 201);
  }

  /*
   * To view a single channel$channel_filter_oos
   *
   *@
   */
  public function show(ChannelFilterOos $channel_filter_oos)
  {
    $channel_filter_oos->channel_filter_oos_skus = $channel_filter_oos->channel_filter_oos_skus;

    return response()->json([
      'data'   =>  $channel_filter_oos,
      'success' =>  true
    ], 200);
  }


  /*
   * To update a channel$channel_filter_oos
   *
   *@
   */
  public function update(Request $request, ChannelFilterOos $channel_filter_oos)
  {
    $channel_filter_oos->update($request->all());

    return response()->json([
      'data'  =>  $channel_filter_oos
    ], 200);
  }

  public function destroy($id)
  {
    $channel_filter_oos = ChannelFilterOos::find($id);
    $channel_filter_oos->delete();

    return response()->json([
      'message' =>  'Deleted'
    ], 204);
  }
}
