<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Feedback;

class FeedbacksController extends Controller
{
  public function __construct()
  {
    $this->middleware(['auth:api', 'company']);
  }

  /*
   * To get all feedbacks
     *
   *@
   */
  public function index()
  {
    $feedbacks = request()->company->feedbacks;

    return response()->json([
      'data'     => $feedbacks,
      'success'  => true
    ], 200);
  }

  /*
   * To store a new feedback
   *
   *@
   */
  public function store(Request $request)
  {
    $request->validate([
      'description'    =>  'required'
    ]);

    $feedback = new Feedback($request->all());
    $request->company->feedbacks()->save($feedback);

    return response()->json([
      'data'    =>  $feedback,
      'success' =>  true
    ], 201); 
  }

  /*
   * To view a single feedback
   *
   *@
   */
  public function show(Feedback $feedback)
  {
    return response()->json([
      'data'   =>  $feedback
    ], 200);   
  }

  /*
   * To update a feedback
   *
   *@
   */
  public function update(Request $request, Feedback $feedback)
  {
    $request->validate([
      'description'  =>  'required',
    ]);

    $feedback->update($request->all());
    
    return response()->json([
      'data'  =>  $feedback,
      'success' =>  true
    ], 200);
  }
}
