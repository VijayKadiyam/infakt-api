<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Inquiry;
use App\InquiryFollowup;

class InquiryFollowupsController extends Controller
{
  public function __construct()
  {
    $this->middleware(['auth:api', 'company']);
  }

  public function index(Request $request, Inquiry $inquiry)
  {
    $inquiry_followups = $inquiry->inquiry_followups;

    return response()->json([
      'data'     =>  $inquiry_followups
    ], 200);
  }

  public function store(Request $request, Inquiry $inquiry)
  {
    $request->validate([
      'user_id' =>  'required',
    ]);

    $remark = new InquiryFollowup($request->all());
    $inquiry->inquiry_followups()->save($remark);

    return response()->json([
      'data'    =>  $remark,
      'success' =>  true
    ], 201); 
  }

  public function show(Inquiry $inquiry, InquiryFollowup $inquiryFollowup)
  {
    return response()->json([
      'data'   =>  $inquiryFollowup
    ], 200);   
  }

  public function update(Request $request, Inquiry $inquiry, InquiryFollowup $inquiryFollowup)
  {
    $inquiryFollowup->update($request->all());
      
    return response()->json([
      'data'  =>  $inquiryFollowup
    ], 200);
  }
}
