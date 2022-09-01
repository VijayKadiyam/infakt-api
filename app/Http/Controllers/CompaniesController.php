<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Company;
use App\CompanyDesignation;

class CompaniesController extends Controller
{
  public function __construct()
  {
    $this->middleware('auth:api')
      ->except('index');
  }

  /*
   * To get all companies
     *
   *@
   */
  public function index()
  {


    if (request()->page && request()->rowsPerPage) {
      $companies = new Company();
      if (request()->search_keyword) {
        $companies = $companies

          ->orwhere('name', 'LIKE', '%' . request()->search_keyword . '%')
          ->orWhere('email', 'LIKE', '%' . request()->search_keyword . '%');
      }
      $count = $companies->count();
      $companies = $companies->with([
        'users'  => function ($query) {
          $query->whereHas('roles',  function ($q) {
            $q->where('name', '=', 'Admin');
          });
        }
      ])->paginate(request()->rowsPerPage)->toArray();
      $companies = $companies['data'];
    } else {
      $companies = Company::with([
        'users'  => function ($query) {
          $query->whereHas('roles',  function ($q) {
            $q->where('name', '=', 'Admin');
          });
        }
      ])->get();
    }
    return response()->json([
      'data'     =>  $companies,
      // 'count'    =>   $count,
      'success'   =>  true,
    ], 200);
  }

  /*
   * To store a new company
   *
   *@
   */
  public function store(Request $request)
  {
    $request->validate([
      'name'    =>  'required',
      'email'   =>  'required',
      'phone'   =>  'required',
      'address' =>  'required',
      'city' =>  'required',
      'state' =>  'required',
      'pincode' =>  'required',
    ]);

    $company = new Company(request()->all());
    $company->save();

    return response()->json([
      'data'    =>  $company,
    ], 201);
  }

  /*
   * To view a single company
   *
   *@
   */
  public function show(Company $company)
  {
    $company->leave_patterns = $company->leave_patterns;

    return response()->json([
      'data'   =>  $company,
      'success' =>  true
    ], 200);
  }

  /*
   * To update a company
   *
   *@
   */
  public function update(Request $request, Company $company)
  {
    $request->validate([
      'name'  =>  'required',
    ]);

    $company->update($request->all());

    return response()->json([
      'data'  =>  $company
    ], 200);
  }
}
