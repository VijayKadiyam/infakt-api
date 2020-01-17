<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Imports\StateImport;
use Maatwebsite\Excel\Facades\Excel;
use App\CrudeState;
use App\CompanyState;
use App\CompanyStateBranch;

class CrudeStatesController extends Controller
{
  public function __construct()
  {
    $this->middleware(['auth:api', 'company'])
      ->except(['index']);
  }

  public function index()
  {
    return response()->json([
      'data'  =>  CrudeState::all()
    ]);
  }

  public function uploadState(Request $request)
  {
    if ($request->hasFile('stateData')) {
      $file = $request->file('stateData');
      Excel::import(new StateImport, $file);
      
      return response()->json([
        'data'    =>  CrudeState::all(),
        'success' =>  true
      ]);
    }
  }

  public function processState()
  {
    set_time_limit(0);
    
    $crude_states = CrudeState::all();
    
    $st = new CompanyState();
    foreach($crude_states as $state) {
      if($state->state != null) {
        $st = CompanyState::where('name', '=', $state->state)
          ->first();
        if(!$st) {
          $data = [
            'name'            =>  $state->state
          ];
          $st = new CompanyState($data);
          request()->company->company_states()->save($st);
        }
      }
      
      $branch = CompanyStateBranch::where('name', '=', $state->branch)
        ->where('company_state_id', '=', $state->id)
        ->first();
      if(!$branch) {
        $data = [
          'name'            =>  $state->branch
        ];
        $branch = new CompanyStateBranch($data);
        $st->company_state_branches()->save($branch);
      }
    }
  }

  public function truncate()
  {
    CrudeState::truncate();
  }
}
