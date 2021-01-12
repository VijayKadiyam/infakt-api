<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Notice;

class NoticesController extends Controller
{
  public function __construct()
  {
    $this->middleware(['auth:api', 'company']);
  }

  /*
   * To get all reference plans
     *
   *@
   */
  public function index(Request $request)
  {
    $notices = request()->company->notices;

    return response()->json([
      'data'     =>  $notices,
      'success'   =>  true,
    ], 200);
  }

  /*
   * To store a new reference plan
   *
   *@
   */
  public function store(Request $request)
  {
    $request->validate([
      'title'       =>  'required',
      'description' =>  'required'
    ]);

    $notice = new Notice($request->all());
    $request->company->notices()->save($notice);

    return response()->json([
      'data'    =>  $notice
    ], 201); 
  }

  /*
   * To view a single reference plan
   *
   *@
   */
  public function show(Notice $notice)
  {
    return response()->json([
      'data'   =>  $notice
    ], 200);   
  }

  /*
   * To update a reference plan
   *
   *@
   */
  public function update(Request $request, Notice $notice)
  {
    $request->validate([
      'name'  =>  'required',
    ]);

    $notice->update($request->all());
      
    return response()->json([
      'data'  =>  $notice
    ], 200);
  }
}
