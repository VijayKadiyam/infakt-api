<?php

namespace App\Http\Controllers;

use App\PjpSupervisor;
use App\PjpVisitedSupervisor;
use Illuminate\Http\Request;

class PjpVisitedSupervisorsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:api', 'company']);
    }



    /*
         * To get all pjp_markets
           *
         *@
         */
    public function index(PjpSupervisor $pjpSupervisor)
     {
    $count = 0;
    if (request()->page && request()->rowsPerPage) {
      $pjp_visited_supervisors = request()->company->pjp_visited_supervisors();
      $count = $pjp_visited_supervisors->count();
      $pjp_visited_supervisors = $pjp_visited_supervisors->paginate(request()->rowsPerPage)->toArray();
      $pjp_visited_supervisors = $pjp_visited_supervisors['data'];
    } 
    // else if (request()->search == 'all') {
    //   $pjp_visited_supervisors = request()->company->pjp_visited_supervisors;
    // } else if (request()->search) {
    //   $pjp_visited_supervisors = request()->company->pjp_visited_supervisors()
    //   ->whereHas('user',  function ($q) {
    //     $q->where('name', 'LIKE', '%' . request()->search . '%');
    //   })->get();
        
    // } 
    else {
      $pjp_visited_supervisors = request()->company->pjp_visited_supervisors;
      $count = $pjp_visited_supervisors->count();
    }

    return response()->json([
      'data'     =>  $pjp_visited_supervisors,
      'count'    =>   $count
    ], 200);
  }

    /*
         * To store a new pjp_markets
         *
         *@
         */
    public function store(Request $request)
    {
        $request->validate([
            'pjp_supervisor_id'    =>  'required',
        ]);

        if($request->id == null) {
          $pjp_visited_supervisor = new PjpVisitedSupervisor($request->all());
          $request->company->pjp_visited_supervisors()->save($pjp_visited_supervisor);
        } else {
          $pjp_visited_supervisor = PjpVisitedSupervisor::where('id', '=', $request->id)
            ->first();
          if($pjp_visited_supervisor) 
            $pjp_visited_supervisor->update($request->all());
          
        }

        return response()->json([
            'data'    =>  $pjp_visited_supervisor
        ], 201);
    }

    /*
         * To view a single pjp_market
         *
         *@
         */
    public function show(PjpVisitedSupervisor $pjpVisitedSupervisor)
    {
        // dd($pjpVisitedSupervisor);
        return response()->json([
            'data'   =>  $pjpVisitedSupervisor,
            'success' => true
        ], 200);
    }

    /*
         * To update a pjp_market
         *
         *@
         */
    public function update(Request $request, PjpVisitedSupervisor $pjpVisitedSupervisor)
    {

        $request->validate([
            'pjp_supervisor_id'    =>  'required',
        ]);

        $pjpVisitedSupervisor->update($request->all());

        return response()->json([
            'data'  =>  $pjpVisitedSupervisor
        ], 200);
    }
}
