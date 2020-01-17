<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Imports\DesignationImport;
use Maatwebsite\Excel\Facades\Excel;
use App\CrudeDesignation;
use App\CompanyDesignation;

class CrudeDesignationsController extends Controller
{
  public function __construct()
  {
    $this->middleware(['auth:api', 'company'])
      ->except(['index']);
  }

  public function index()
  {
    return response()->json([
      'data'  =>  CrudeDesignation::all()
    ]);
  }

  public function uploadDesignation(Request $request)
  {
    if ($request->hasFile('designationData')) {
      $file = $request->file('designationData');
      Excel::import(new DesignationImport, $file);
      
      return response()->json([
        'data'    =>  CrudeDesignation::all(),
        'success' =>  true
      ]);
    }
  }

  public function processDesignation()
  {
    set_time_limit(0);
    
    $crude_designations = CrudeDesignation::all();

    foreach($crude_designations as $designation) {
      $desg = CompanyDesignation::where('name', '=', $designation->name)
        ->first();
      if(!$desg) {
        $data = [
          'name'            =>  $designation->name
        ];
        $desg = new CompanyDesignation($data);
        request()->company->company_designations()->save($desg);
      }
    }
  }

  public function truncate()
  {
    CrudeDesignation::truncate();
  }
}
