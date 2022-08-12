<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Value;

class ValuesController extends Controller
{
  public function __construct()
  {
    $this->middleware(['company']);
  }

  public function index(Request $request)
  {
    $count = 0;
    if($request->search) {
      $values = request()->company->values()
        ->where('name', 'LIKE', '%' . $request->search . '%')
        ->get();
      $count = $values->count();
    }
    else if(request()->page && request()->rowsPerPage) {
      $values = request()->company->values();
      $count = $values->count();
      $values = $values->paginate(request()->rowsPerPage)->toArray();
      $values = $values['data'];
    } else {
      $values = request()->company->values; 
      $count = $values->count();
    }

    return response()->json([
      'data'     =>  $values,
      'count'    =>   $count
    ], 200);
  }

  /*
   * To store a new value
   *
   *@
   */
  public function store(Request $request)
  {
    $request->validate([
      'name'        =>  'required',
    ]);

    $value = new Value(request()->all());
    $request->company->values()->save($value);

    return response()->json([
      'data'    =>  $value
    ], 201); 
  }

  /*
   * To view a single value
   *
   *@
   */
  public function show(Value $value)
  {
    return response()->json([
      'data'   =>  $value,
      'success' =>  true
    ], 200);   
  }

  /*
   * To update a value
   *
   *@
   */
  public function update(Request $request, Value $value)
  {
    $value->update($request->all());
      
    return response()->json([
      'data'  =>  $value
    ], 200);
  }

  public function destroy($id)
  {
    $value = Value::find($id);
    $value->delete();

    return response()->json([
      'message' =>  'Deleted'  
    ], 204);
  }
}
