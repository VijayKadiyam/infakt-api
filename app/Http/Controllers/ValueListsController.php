<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ValueList;
use App\Value;

class ValueListsController extends Controller
{
  public function __construct()
  {
    $this->middleware(['company']);
  }

  public function masters(Request $request)
  {
    $valuesController = new ValuesController();
    $valuesResponse = $valuesController->index($request);

    return response()->json([
      'values'  =>  $valuesResponse->getData()->data,
    ], 200);
  }

  public function index(Request $request, Value $value)
  {
    $count = 0;
    $valueLists = $value->value_lists();
    $valueLists = $request->showOnlyActive ? $valueLists->where('is_active', '=', 1) : $valueLists;
    if(request()->page && request()->rowsPerPage) {
      $count = $valueLists->count();
      $valueLists = $valueLists->paginate(request()->rowsPerPage)->toArray();
      $valueLists = $valueLists['data'];
    } else {
      $valueLists = $valueLists->get();
      $count = $valueLists->count();
    }

    return response()->json([
      'data'     =>   $valueLists,
      'count'    =>   $count
    ], 200);
  }

  public function store(Request $request, Value $value)
  {
    $request->validate([
      'value_id'    =>  'required',
      'company_id'     =>  'required',
      'description' =>  'required',
    ]);

    $valueList = new ValueList(request()->all());
    $value->value_lists()->save($valueList);

    return response()->json([
      'data'    =>  $valueList
    ], 201); 
  }

  public function storeMultiple(Request $request, Value $value)
  {
    $request->validate([
      'datas.*.value_id'    =>  'required',
      'datas.*.company_id'     =>  'required',
      'datas.*.description' =>  'required',
      'datas.*.code'        =>  'required',
    ]);

    $valueLists = [];
    foreach ($request->datas as $valueList) {
      if(!isset($valueList['id'])) {
        $valueList = new ValueList($valueList);
        $value->value_lists()->save($valueList);
        $valueLists[] = $valueList;
      }
      else {
        $vL = ValueList::find($valueList['id']);
        $vL->update($valueList);
        $valueLists[] = $vL;
      }
    }

    return response()->json([
      'data'    =>  $valueLists
    ], 201); 
  }

  public function show(Value $value, ValueList $valueList)
  {
    return response()->json([
      'data'   =>  $valueList,
      'success' =>  true
    ], 200);   
  }

  public function update(Request $request, Value $value, ValueList $valueList)
  {
    $valueList->update($request->all());
      
    return response()->json([
      'data'  =>  $valueList
    ], 200);
  }

  public function destroy(Value $value, $id)
  {
    $valueList = ValueList::find($id);
    $valueList->delete();

    return response()->json([
      'message' =>  'Deleted'  
    ], 204);
  }
}
