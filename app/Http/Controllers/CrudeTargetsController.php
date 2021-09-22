<?php

namespace App\Http\Controllers;

use App\CrudeTarget;
use App\Imports\TargetImport;
use App\Target;
use App\User;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class CrudeTargetsController extends Controller
{
  public function __construct()
  {
    $this->middleware(['auth:api', 'company'])
      ->except(['index']);
  }

  public function index()
  {
    return response()->json([
      'data'  =>  CrudeTarget::all()
    ]);
  }

  public function uploadTarget(Request $request)
  {
    set_time_limit(0);
    if ($request->hasFile('targetData')) {
      $file = $request->file('targetData');

      Excel::import(new TargetImport, $file);
      // $target = new Target($data);
      // $target->save($target);
      return response()->json([
        'data'    =>  CrudeTarget::all(),
        'success' =>  true
      ]);
    }
  }

  public function processTarget(User $user)
  {
    set_time_limit(0);

    $crude_targets = CrudeTarget::all();

    foreach ($crude_targets as $target) {
      if ($target->store_code) {
        $us = User::where('employee_code', '=', $target->store_code)
          ->first();
          if ($us) {
          $user_id = $us['id'];
          $data = [
            'company_id'=> request()->company->id,
            'user_id' => $user_id,
            'month' => $target->month,
            'year' => $target->year,
            'target' => $target->target,
          ];
          $User_target = Target::where('user_id', '=', $user_id)
            ->where('month', '=', $target->month)
            ->where('year', '=', $target->year)->first();
          if ($User_target) {
            // Update Target
            $targetData = Target::where('id', '=', $User_target->id);
            $targetData->update($data);
          } else {
            // Insert Target
            $target = new Target($data);
            $target->save();
          }
        }
      }
    }
  }

  public function truncate()
  {
    CrudeTarget::truncate();
  }
}
