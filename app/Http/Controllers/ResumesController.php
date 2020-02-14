<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Resume;

class ResumesController extends Controller
{
  public function __construct()
  {
    $this->middleware(['auth:api', 'company']);
  }

  public function index(Request $request)
  {
    $resumes = [];
    if($request->search != null) {
      $resumes = request()->company->resumes()
        ->where('name', 'LIKE', '%' . $request->search . '%')
        ->orWhere('mobile_1', 'LIKE', '%' . $request->search . '%')
        ->orWhere('mobile_2', 'LIKE', '%' . $request->search . '%')
        ->orWhere('present_company_name', 'LIKE', '%' . $request->search . '%')
        ->orWhere('designation', 'LIKE', '%' . $request->search . '%')
        ->latest()->get();
    }
    else
      $resumes = request()->company->resumes;

    return response()->json([
      'data'     =>  $resumes
    ], 200);
  }

  public function store(Request $request)
  {
    $request->validate([
      'user_id'   =>  'required',
      'name'      =>  'required',
      'mobile_1'  =>  'required',
    ]);

    $resume = new Resume($request->all());
    $request->company->resumes()->save($resume);

    return response()->json([
      'data'    =>  $resume
    ], 201); 
  }

  public function show(Resume $resume)
  {
    return response()->json([
      'data'   =>  $resume
    ], 200);   
  }

  public function update(Request $request, Resume $resume)
  {
    $resume->update($request->all());
      
    return response()->json([
      'data'  =>  $resume
    ], 200);
  }
}
