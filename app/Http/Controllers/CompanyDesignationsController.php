<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\CompanyDesignation;
use App\Company;

class CompanyDesignationsController extends Controller
{
  public function __construct()
  {
    $this->middleware(['auth:api', 'company']);
  }

  /*
   * To get all company designations
     *
   *@
   */
  public function index(Company $company)
  {
    $companyDesignations = $company->company_designations;

    return response()->json([
      'data'     =>  $companyDesignations
    ], 200);
  }

  /*
   * To store a new company designations
   *
   *@
   */
  public function store(Request $request)
  {
    $request->validate([
      'name'    =>  'required'
    ]);

    $companyDesignation = new CompanyDesignation($request->all());
    $request->company->company_designations()->save($companyDesignation);

    return response()->json([
      'data'    =>  $companyDesignation
    ], 201); 
  }

  /*
   * To view a single company designation
   *
   *@
   */
  public function show(Company $company, CompanyDesignation $companyDesignation)
  {
    return response()->json([
      'data'   =>  $companyDesignation
    ], 200);   
  }

  /*
   * To update a company designation
   *
   *@
   */
  public function update(Request $request, Company $company, CompanyDesignation $companyDesignation)
  {
    $request->validate([
      'name'  =>  'required',
    ]);

    $companyDesignation->update($request->all());
      
    return response()->json([
      'data'  =>  $companyDesignation
    ], 200);
  }
}
