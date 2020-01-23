<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Salary;

class SalariesController extends Controller
{
  public function __construct()
  {
    $this->middleware(['auth:api', 'company']);
  }

  public function index(Request $request)
  {
    $salaries = request()->user()->salaries;

    return response()->json([
      'data'     =>  $salaries
    ], 200);
  }

  public function store(Request $request)
  {
    $salary = new Salary($request->all());
    $request->user()->salaries()->save($salary);

    return response()->json([
      'data'    =>  $salary
    ], 201); 
  }

  public function show(Salary $salary)
  {
    return response()->json([
      'data'   =>  $salary
    ], 200);   
  }

  public function update(Request $request, Salary $salary)
  {

    $salary->update($request->all());
      
    return response()->json([
      'data'  =>  $salary
    ], 200);
  }
}
