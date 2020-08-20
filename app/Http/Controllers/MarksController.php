<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Mark;
use \Carbon\Carbon;

class MarksController extends Controller
{
  public function __construct()
  {
    $this->middleware(['auth:api', 'company']);
  }

  /*
   * To get all marks
     *
   *@
   */
  public function index(Request $request)
  {
    // $marks = request()->user()->marks;
    if($request->date && $request->id)
    {
      $marks = Mark::whereDate('created_at', $request->date)
        ->where('user_id', '=', $request->id)->latest()->get();
    }
    else if($request->month && $request->id)
    {
      $marks = Mark::whereMonth('created_at', $request->month)
        ->where('user_id', '=', $request->id)->latest()->get();
    }
    else {
      $marks = Mark::whereDate('created_at', Carbon::today())
        ->where('user_id', '=', $request->user()->id)->latest()->get();
    }
    $geocodesController = new GeocodesController();
    foreach($marks as $mark) {
      if($mark->in_lat) {
        $request->request->add(['lat' => $mark->in_lat]);
        $request->request->add(['lng' => $mark->in_lng]);
        $mark['address_in'] = json_decode($geocodesController->index($request)->getContent())->data;
      }
      if($mark->out_lat) {
        $request->request->add(['lat' => $mark->out_lat]);
        $request->request->add(['lng' => $mark->out_lng]);
        $mark['address_out'] = json_decode($geocodesController->index($request)->getContent())->data;
      }
    }

    return response()->json([
      'data'     =>  $marks,
      'success' =>  true
    ], 200);
  }

  /*
   * To store a new mark
   *
   *@
   */
  public function store(Request $request)
  {
    $request->validate([
      'in_lat'   =>  'required',
      'in_lng'   =>  'required',
    ]); 

    $mark = new Mark($request->all());
    $request->user()->marks()->save($mark);

    return response()->json([
      'data'    =>  $mark,
      'success' =>  true
    ], 201); 
  }

  /*
   * To view a single mark
   *
   *@
   */
  public function show(Mark $mark)
  {
    return response()->json([
      'data'   =>  $mark
    ], 200);   
  }

  /*
   * To update a mark
   *
   *@
   */
  public function update(Request $request, Mark $mark)
  {
    $request->validate([
      // 'in_lat'   =>  'required',
      // 'in_lng'   =>  'required',
      // 'out_lat'  =>  'required',
      // 'out_lng'  =>  'required'    
    ]);

    $mark->update($request->all());
      
    return response()->json([
      'data'  =>  $mark,
      'success' =>  true
    ], 200);
  }
}
