<?php

namespace App\Http\Controllers;

use App\Tracker;
use App\TrackerSku;
use Illuminate\Http\Request;

class TrackersController extends Controller
{
    public function __construct()
  {
    $this->middleware(['auth:api', 'company']);
  }

  public function index()
  {
    $count = 0;
    if(request()->page && request()->rowsPerPage) {
        $trackers = request()->company->trackers();
        $count = $trackers->count();
        $trackers = $trackers->paginate(request()->rowsPerPage)->toArray();
        $trackers = $trackers['data'];
      } else {
        $trackers = request()->company->trackers; 
        $count = $trackers->count();
      }
  
      return response()->json([
        'data'     =>  $trackers,
        'count'    =>   $count
      ], 200);
  }

  /*
   * To store a new tracker
   *
   *@
   */
  public function store(Request $request)
  {
    $request->validate([
      'retailer_id'  =>  'required',
    ]);


    if ($request->id == null || $request->id == '') {
      // Save Tracker
      $tracker = new Tracker(request()->all());
      $request->company->trackers()->save($tracker);

      // Save Tracker Expiries
      if (isset($request->skus))
        foreach ($request->skus as $sku) {
          $sku = new TrackerSku($sku);
          $tracker->tracker_skus()->save($sku);
        }
      // ---------------------------------------------------
    } else {
      // Update Tracker
      $tracker = Tracker::find($request->id);
      $tracker->update($request->all());
      
      // Check if Tracker Sku deleted
      if (isset($request->skus)){
        $trackerSkuIdResponseArray = array_pluck($request->skus, 'id');
      }
      else
      $trackerSkuIdResponseArray = [];
      $trackerId = $tracker->id;
      $trackerSkuIdArray = array_pluck(TrackerSku::where('tracker_id', '=', $trackerId)->get(), 'id');
      $differenceTrackerSkuIds = array_diff($trackerSkuIdArray, $trackerSkuIdResponseArray);
      // Delete which is there in the database but not in the response
      if ($differenceTrackerSkuIds)
      foreach ($differenceTrackerSkuIds as $differenceTrackerSkuId) {
        $trackerSku = TrackerSku::find($differenceTrackerSkuId);
        $trackerSku->delete();
      }
      
      // Update Tracker Sku
      if (isset($request->skus))
      foreach ($request->skus as $sku) {
        if (!isset($sku['id'])) {
          $tracker_sku = new TrackerSku($sku);
          $tracker->tracker_skus()->save($tracker_sku);
        } else {
          $tracker_sku = TrackerSku::find($sku['id']);
          $tracker_sku->update($sku);
          }
        }

      // ---------------------------------------------------

    }

    $tracker->tracker_skus = $tracker->tracker_skus;
    return response()->json([
      'data'    =>  $tracker
    ], 201);
  }

  /*
   * To view a single channel$tracker
   *
   *@
   */
  public function show(Tracker $tracker)
  {
    $tracker->tracker_skus = $tracker->tracker_skus;

    return response()->json([
      'data'   =>  $tracker,
      'success' =>  true
    ], 200);
  }

  /*
   * To update a channel$tracker
   *
   *@
   */
  public function update(Request $request, Tracker $tracker)
  {
    $tracker->update($request->all());

    return response()->json([
      'data'  =>  $tracker
    ], 200);
  }

  public function destroy($id)
  {
    $tracker = Tracker::find($id);
    $tracker->delete();

    return response()->json([
      'message' =>  'Deleted'
    ], 204);
  }
}
