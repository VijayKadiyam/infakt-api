<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Manufacturer;
use App\Company;

class ManufacturersController extends Controller
{
  public function __construct()
  {
    $this->middleware(['auth:api', 'company']);
  }

  /*
   * To get all company break types
     *
   *@
   */
  public function index()
  {
    $manufacturers = request()->company->manufacturers;

    return response()->json([
      'data'     =>  $manufacturers,
      'success' =>  true,
    ], 200);
  }

  /*
   * To store a new company break type
   *
   *@
   */
  public function store(Request $request)
  {
    $request->validate([
      'name'    =>  'required'
    ]);

    $manufacturer = new Manufacturer($request->all());
    $request->company->manufacturers()->save($manufacturer);

    return response()->json([
      'data'    =>  $manufacturer,
      'success' =>  true,
    ], 201); 
  }

  /*
   * To view a single company break type
   *
   *@
   */
  public function show($manufacturer)
  {
    $manufacturer = request()->company->manufacturers->find($manufacturer);

    return response()->json([
      'data'   =>  $manufacturer
    ], 200);   
  }

  /*
   * To update a company designation
   *
   *@
   */
  public function update(Request $request, Manufacturer $manufacturer)
  {

    $request->validate([
      'name'  =>  'required',
    ]);

    $manufacturer->update($request->all());
    
    return response()->json([
      'data'  =>  $manufacturer
    ], 200);
  }
}
