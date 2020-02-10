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

  public function index()
  {
    $inquiries = request()->company->inquiries;

    return response()->json([
      'data'     =>  $inquiries
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
      'data'    =>  $inquiry
    ], 201); 
  }

  public function show(Inquiry $inquiry)
  {
    return response()->json([
      'data'   =>  $inquiry
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
