<?php

namespace App\Http\Controllers;

use App\PjpSupervisor;
use Illuminate\Http\Request;

class PjpSupervisorsController extends Controller
{
  public function __construct()
  {
    $this->middleware(['auth:api', 'company']);
  }

  public function masters(Request $request)
  {
    if ($request->is_simple == true) {
      $months = [
        ['text'  =>  'JANUARY', 'value' =>  1],
        ['text'  =>  'FEBRUARY', 'value' =>  2],
        ['text'  =>  'MARCH', 'value' =>  3],
        ['text'  =>  'APRIL', 'value' =>  4],
        ['text'  =>  'MAY', 'value' =>  5],
        ['text'  =>  'JUNE', 'value' =>  6],
        ['text'  =>  'JULY', 'value' =>  7],
        ['text'  =>  'AUGUST', 'value' =>  8],
        ['text'  =>  'SEPTEMBER', 'value' =>  9],
        ['text'  =>  'OCTOBER', 'value' =>  10],
        ['text'  =>  'NOVEMBER', 'value' =>  11],
        ['text'  =>  'DECEMBER', 'value' =>  12],
      ];

      $years = ['2020', '2021', '2022'];
      return response()->json([
        'months'  =>  $months,
        'years'   =>  $years
      ], 200);
    } else {
      $request->request->add(['role_id' => '4']);
      $usersController = new UsersController();
      $usersResponse = $usersController->index($request);

      $pjpController = new PjpsController();
      $pjpResponse = $pjpController->index($request);
      return response()->json([
        'users'           =>  $usersResponse->getData()->data,
        'pjps' =>  $pjpResponse->getData()->data,
      ], 200);
    }
  }
  /*
         * To get all pjp_supervisors
           *
         *@
         */
  public function index(Request $request)
  {
    $count = 0;

    if (request()->page && request()->rowsPerPage) {
      $pjp_supervisors = request()->company->pjp_supervisors();
      $count = $pjp_supervisors->count();
      $pjp_supervisors = $pjp_supervisors->paginate(request()->rowsPerPage)->toArray();
      $pjp_supervisors = $pjp_supervisors['data'];
    } else if (request()->search == 'all') {
      $pjp_supervisors = request()->company->pjp_supervisors;
    } else if (request()->search) {
      $pjp_supervisors = request()->company->pjp_supervisors()
        ->whereHas('user',  function ($q) {
          $q->where('name', 'LIKE', '%' . request()->search . '%');
        })->get();
    } else {
      $pjp_supervisors = request()->company->pjp_supervisors;
      $count = $pjp_supervisors->count();
    }

    $month = $request->month;
    $year = $request->year;
    if ($month != null || $year != null) {
      $pjp_supervisors = request()->company->pjp_supervisors()
        ->whereMonth('date', '=', $month)
        ->whereYear('date', '=', $year)
        ->get();
    }

    foreach ($pjp_supervisors as $key => $pjp) {
      $explodelocation = explode("#", $pjp_supervisors[$key]['pjp']['location']);
      $pjp_supervisors[$key]['pjp']['location'] = $explodelocation[0];
    }

    return response()->json([
      'data'     =>  $pjp_supervisors,
      'count'    =>   $count
    ], 200);
  }

  /*
         * To store a new pjp_supervisors
         *
         *@
         */
  public function store(Request $request)
  {
    $request->validate([
      'user_id'    =>  'required'
    ]);

    $pjp_supervisor = new PjpSupervisor($request->all());
    $request->company->pjp_supervisors()->save($pjp_supervisor);

    return response()->json([
      'data'    =>  $pjp_supervisor
    ], 201);
  }

  /*
         * To view a single pjp_supervisor
         *
         *@
         */
  public function show(PjpSupervisor $pjp_supervisor)
  {
    return response()->json([
      'data'   =>  $pjp_supervisor
    ], 200);
  }

  /*
         * To update a pjp_supervisor
         *
         *@
         */
  public function update(Request $request, PjpSupervisor $pjp_supervisor)
  {
    $request->validate([
      'user_id'  =>  'required',
    ]);

    $pjp_supervisor->update($request->all());

    return response()->json([
      'data'  =>  $pjp_supervisor
    ], 200);
  }

  public function destroy(Request $request)
  {
    $PjpSupervisor = PjpSupervisor::find($request->id);
    $PjpSupervisor->delete();
    return response()->json([
      'data'  =>  'Pjp Supervisor Deleted Succesfully',
    ]);
  }
}
