<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Mark;
use \Carbon\Carbon;

class MarksController extends Controller
{
  public function __construct()
  {
    // $this->middleware(['auth:api', 'company']);
  }

  /*
   * To get all marks
     *
   *@
   */
  public function index(Request $request)
  {
    // $marks = request()->user()->marks;
    if($request->date && $request->user_id)
    {
      $marks = Mark::whereDate('created_at', $request->date)
        ->where('user_id', '=', $request->user_id)->latest()->get();

      dd($marks->toArray());
    }
    else {
      $marks = Mark::whereDate('created_at', Carbon::today())
      ->where('user_id', '=', $request->user()->id)->latest()->get();
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
