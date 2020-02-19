<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Inquiry;

class InquiriesController extends Controller
{
  public function __construct()
  {
    $this->middleware(['auth:api', 'company']);
  }

  public function index(Request $request)
  {
    $inquiries = [];
    if($request->search != null) {
      $inquiries = request()->company->inquiries()
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
      $inquiries = request()->company->inquiries;
    return response()->json([
      'data'     => $inquiries,
      'success' =>  true
    ], 200);
  }

  public function store(Request $request)
  {
    $request->validate([
      'date'              =>  'required',
      'company_name'      =>  'required',
      'contact_person_1'  =>  'required',
      'mobile_1'          =>  'required',
    ]);

    $inquiry = new Inquiry($request->all());
    $request->company->inquiries()->save($inquiry);

    return response()->json([
      'data'    =>  $inquiry,
      'success' =>  true
    ], 201); 
  }

  public function show(Inquiry $inquiry)
  {
    return response()->json([
      'data'   =>  $inquiry,
      'success' =>  true
    ], 200);   
  }

  public function update(Request $request, Inquiry $inquiry)
  {
    $inquiry->update($request->all());
      
    return response()->json([
      'data'  =>  $inquiry
    ], 200);
  }
}
