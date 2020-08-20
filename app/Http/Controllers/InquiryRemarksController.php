<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Inquiry;
use App\InquiryRemark;

class InquiryRemarksController extends Controller
{
  public function __construct()
  {
    $this->middleware(['auth:api', 'company']);
  }

  public function index(Request $request, Inquiry $inquiry)
  {
    $inquiry_remarks = [];
    if($request->search != null) {
      $inquiry_remarks = $inquiry->inquiry_remarks()
        ->where('company_name', 'LIKE', '%' . $request->search . '%')
        ->orWhere('email_1', 'LIKE', '%' . $request->search . '%')
        ->orWhere('email_2', 'LIKE', '%' . $request->search . '%')
        ->orWhere('mobile_1', 'LIKE', '%' . $request->search . '%')
        ->orWhere('mobile_2', 'LIKE', '%' . $request->search . '%')
        ->orWhere('contact_person_1', 'LIKE', '%' . $request->search . '%')
        ->orWhere('contact_person_2', 'LIKE', '%' . $request->search . '%')
        ->orWhere('contact_person_3', 'LIKE', '%' . $request->search . '%')
        ->latest()->get();
    }
    else
      $inquiry_remarks = $inquiry->inquiry_remarks;

    return response()->json([
      'data'     =>  $inquiry_remarks
    ], 200);
  }

  public function store(Request $request, Inquiry $inquiry)
  {
    $request->validate([
      'user_id' =>  'required',
    ]);

    $remark = new InquiryRemark($request->all());
    $inquiry->inquiry_remarks()->save($remark);

    return response()->json([
      'data'    =>  $remark
    ], 201); 
  }

  public function show(Inquiry $inquiry, InquiryRemark $inquiryRemark)
  {
    return response()->json([
      'data'   =>  $inquiryRemark
    ], 200);   
  }

  public function update(Request $request, Inquiry $inquiry, InquiryRemark $inquiryRemark)
  {
    $inquiryRemark->update($request->all());
      
    return response()->json([
      'data'  =>  $inquiryRemark
    ], 200);
  }
}
