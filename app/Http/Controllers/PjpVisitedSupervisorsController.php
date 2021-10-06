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
        $pjp_visited_supervisors = $pjpSupervisor->pjp_visited_supervisor;

        return response()->json([
            'data'     =>  $pjp_visited_supervisors,
            'success'   =>  true
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

        $pjp_visited_supervisor = new PjpVisitedSupervisor($request->all());
        $request->company->pjp_visited_supervisor()->save($pjp_visited_supervisor);

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
