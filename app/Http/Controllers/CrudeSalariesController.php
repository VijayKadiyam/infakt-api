<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Imports\SalaryImport;
use App\CrudeSalary;
use Maatwebsite\Excel\Facades\Excel;
use App\User;

class CrudeSalariesController extends Controller
{
  public function __construct()
  {
    $this->middleware(['auth:api', 'company'])
      ->except(['index']);
  }

  public function index()
  {
    return response()->json([
      'data'  =>  CrudeSalary::all()
    ]);
  }

  public function uploadSalary(Request $request)
  {
    set_time_limit(0);
    
    if ($request->hasFile('salaryData')) {
      $file = $request->file('salaryData');

      Excel::import(new SalaryImport, $file);
      
      return response()->json([
        'data'    =>  CrudeSalary::all(),
        'success' =>  true
      ]);
    }
  }
}
