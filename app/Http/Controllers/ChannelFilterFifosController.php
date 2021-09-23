<?php

namespace App\Http\Controllers;

use App\ChannelFilterFifo;
use App\ChannelFilterFifoExpiry;
use Illuminate\Http\Request;

class ChannelFilterFifosController extends Controller
{
    public function __construct()
  {
    $this->middleware(['auth:api', 'company']);
  }

  public function index()
  {
    $count = 0;
    if(request()->page && request()->rowsPerPage) {
        $channel_filter_fifos = request()->company->channel_filter_fifos();
        $count = $channel_filter_fifos->count();
        $channel_filter_fifos = $channel_filter_fifos->paginate(request()->rowsPerPage)->toArray();
        $channel_filter_fifos = $channel_filter_fifos['data'];
      } else {
        $channel_filter_fifos = request()->company->channel_filter_fifos; 
        $count = $channel_filter_fifos->count();
      }
  
      return response()->json([
        'data'     =>  $channel_filter_fifos,
        'count'    =>   $count
      ], 200);
  }

  /*
   * To store a new channel$channel_filter_fifo
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
      // Save ChannelFilterFifo
      $channel_filter_fifo = new ChannelFilterFifo(request()->all());
      $request->company->channel_filter_fifos()->save($channel_filter_fifo);

      // Save ChannelFilterFifo Expiries
      if (isset($request->expiries))
        foreach ($request->expiries as $expiry) {
          $expiry = new ChannelFilterFifoExpiry($expiry);
          $channel_filter_fifo->channel_filter_fifo_expiries()->save($expiry);
        }
      // ---------------------------------------------------
    } else {
      // Update ChannelFilterFifo
      $channel_filter_fifo = ChannelFilterFifo::find($request->id);
      $channel_filter_fifo->update($request->all());
      
      // Check if ChannelFilterFifo Expiry deleted
      if (isset($request->expiries)){
        $channel_filter_fifoExpiryIdResponseArray = array_pluck($request->expiries, 'id');
      }
      else
      $channel_filter_fifoExpiryIdResponseArray = [];
      $channel_filter_fifoId = $channel_filter_fifo->id;
      $channel_filter_fifoExpiryIdArray = array_pluck(ChannelFilterFifoExpiry::where('channel_filter_fifo_id', '=', $channel_filter_fifoId)->get(), 'id');
      $differenceChannelFilterFifoExpiryIds = array_diff($channel_filter_fifoExpiryIdArray, $channel_filter_fifoExpiryIdResponseArray);
      // Delete which is there in the database but not in the response
      if ($differenceChannelFilterFifoExpiryIds)
      foreach ($differenceChannelFilterFifoExpiryIds as $differenceChannelFilterFifoExpiryId) {
        $channel_filter_fifoExpiry = ChannelFilterFifoExpiry::find($differenceChannelFilterFifoExpiryId);
        $channel_filter_fifoExpiry->delete();
      }
      
      // Update ChannelFilterFifo Expiry
      if (isset($request->expiries))
      foreach ($request->expiries as $expiry) {
        if (!isset($expiry['id'])) {
          $channel_filter_fifo_expiry = new ChannelFilterFifoExpiry($expiry);
          $channel_filter_fifo->channel_filter_fifo_expiries()->save($channel_filter_fifo_expiry);
        } else {
          $channel_filter_fifo_expiry = ChannelFilterFifoExpiry::find($expiry['id']);
          $channel_filter_fifo_expiry->update($expiry);
          }
        }

      // ---------------------------------------------------

    }

    $channel_filter_fifo->channel_filter_fifo_expiries = $channel_filter_fifo->channel_filter_fifo_expiries;
    return response()->json([
      'data'    =>  $channel_filter_fifo
    ], 201);
  }

  /*
   * To view a single channel$channel_filter_fifo
   *
   *@
   */
  public function show(ChannelFilterFifo $channel_filter_fifo)
  {
    $channel_filter_fifo->channel_filter_fifo_expiries = $channel_filter_fifo->channel_filter_fifo_expiries;

    return response()->json([
      'data'   =>  $channel_filter_fifo,
      'success' =>  true
    ], 200);
  }

  /*
   * To update a channel$channel_filter_fifo
   *
   *@
   */
  public function update(Request $request, ChannelFilterFifo $channel_filter_fifo)
  {
    $channel_filter_fifo->update($request->all());

    return response()->json([
      'data'  =>  $channel_filter_fifo
    ], 200);
  }

  public function destroy($id)
  {
    $channel_filter_fifo = ChannelFilterFifo::find($id);
    $channel_filter_fifo->delete();

    return response()->json([
      'message' =>  'Deleted'
    ], 204);
  }
}
