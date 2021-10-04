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
  
    /*
         * To get all pjp_supervisors
           *
         *@
         */
    public function index()
    {
      $count = 0;
      if (request()->page && request()->rowsPerPage) {
        $pjp_supervisors = request()->company->pjp_supervisors();
        $count = $pjp_supervisors->count();
        $pjp_supervisors = $pjp_supervisors->paginate(request()->rowsPerPage)->toArray();
        $pjp_supervisors = $pjp_supervisors['data'];
      } else {
        $pjp_supervisors = request()->company->pjp_supervisors;
        $count = $pjp_supervisors->count();
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
  }
