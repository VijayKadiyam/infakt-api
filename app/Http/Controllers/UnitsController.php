<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Unit;

class UnitsController extends Controller
{
  public function __construct()
  {
    $this->middleware(['auth:api', 'company']);
  }

  /*
   * To get all units
     *
   *@
   */
  public function index()
  {
    $count = 0;
    if(request()->page && request()->rowsPerPage) {
      $units = request()->company->units();
      $count = $units->count();
      $units = $units->paginate(request()->rowsPerPage)->toArray();
      $units = $units['data'];
    } else {
      $units = request()->company->units; 
      $count = $units->count();
    }

    return response()->json([
      'data'     =>  $units,
      'count'    =>   $count
    ], 200);
  }

  /*
   * To store a new units
   *
   *@
   */
  public function store(Request $request)
  {
    $request->validate([
      'name'    =>  'required'
    ]);

    $unit = new Unit($request->all());
    $request->company->units()->save($unit);

    return response()->json([
      'data'    =>  $unit
    ], 201); 
  }

  /*
   * To view a single unit
   *
   *@
   */
  public function show(Unit $unit)
  {
    return response()->json([
      'data'   =>  $unit
    ], 200);   
  }

  /*
   * To update a unit
   *
   *@
   */
  public function update(Request $request, Unit $unit)
  {
    $request->validate([
      'name'  =>  'required',
    ]);

    $unit->update($request->all());
      
    return response()->json([
      'data'  =>  $unit
    ], 200);
  }
}
