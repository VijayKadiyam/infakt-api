<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Company;

class CompanyUserController extends Controller
{
  public function __construct()
  {
    $this->middleware('auth:api')
      ->except('store');
  }

  /*
   * Assign company to user
   *
   *@
   */
  public function store(Request $request)
  {
    $request->validate([
      'user_id'    =>  'required',
      'company_id' =>  'required'
    ]);

    $user = User::find($request->user_id);
    $company = Company::find($request->company_id);
    $user->assignCompany($company->id);
    $companyUser = User::with('companies')->find($request->user_id);

    return response()->json([
        'data'  =>  $companyUser
    ], 201); 
  }
}
