<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Salary;
use App\User;

class SalariesController extends Controller
{
  public function __construct()
  {
    $this->middleware(['auth:api', 'company'])
      ->except(['download']);
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

  public function download(Request $request)
  {
    $salary = Salary::where('user_id', '=', $request->id)
      ->where('month', '=', $request->month)
      ->where('year', '=', $request->year)
      ->first();

    $user = User::find($request->id);

    $data['salary'] = $salary;
    $data['user'] = $user;

    return view('salary.index', compact('user', 'salary'));

    $pdf = PDF::loadView('salary.index', $data);

    return $pdf->download($user->name . '-salary-slip.pdf');
  }
}
