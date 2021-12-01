<?php

namespace App\Http\Controllers;

use App\Pjp;
use App\PjpVisitedSupervisor;
use Illuminate\Http\Request;

class PjpsController extends Controller
{
  public function __construct()
  {
    $this->middleware(['auth:api', 'company']);
  }

  /*
       * To get all pjps
         *
       *@
       */
  public function index()
  {
    $count = 0;
    $pjps = [];
    if(request()->supervisorId && request()->monthId) {
      $pjpSupervisors = request()->company->pjp_supervisors()
        ->where('user_id', '=', request()->supervisorId)
        ->whereMonth('date', '=', request()->monthId)
        ->get();
      foreach($pjpSupervisors as $pjpSupervisor) {
        $pjp = request()->company->pjps()
          ->where('id', '=', $pjpSupervisor->actual_pjp_id)
          ->first();
        $pjp['pjp_supervisor'] = $pjpSupervisor;
        foreach($pjp->pjp_markets as  $pjpMarket) {
          $pjpVisitedSupervisor = PjpVisitedSupervisor::where('pjp_supervisor_id', '=', $pjpSupervisor->id)
            ->where('visited_pjp_market_id', '=', $pjpMarket->id)
            ->first();
          if($pjpVisitedSupervisor != null) {
            $pjpMarket['pjp_visited_supervisor'] = $pjpVisitedSupervisor;
          } else {
            $pjpMarket['pjp_visited_supervisor'] = (object)[];
          }
        }
        if($pjp) 
          $pjps[] = $pjp;
      }
    }
    else if (request()->page && request()->rowsPerPage) {
      $pjps = request()->company->pjps();
      $count = $pjps->count();
      $pjps = $pjps->paginate(request()->rowsPerPage)->toArray();
      $pjps = $pjps['data'];
    } else {
      $pjps = request()->company->pjps;
      $count = $pjps->count();
    }

    return response()->json([
      'data'     =>  $pjps,
      'count'    =>   $count,
      'success' =>  true,
    ], 200);
  }

  /*
       * To store a new pjps
       *
       *@
       */
  public function store(Request $request)
  {
    $request->validate([
      'location'    =>  'required'
    ]);

    $pjp = new Pjp($request->all());
    $request->company->pjps()->save($pjp);

    return response()->json([
      'data'    =>  $pjp
    ], 201);
  }

  /*
       * To view a single pjp
       *
       *@
       */
  public function show(Pjp $pjp)
  {
    return response()->json([
      'data'   =>  $pjp
    ], 200);
  }

  /*
       * To update a pjp
       *
       *@
       */
  public function update(Request $request, Pjp $pjp)
  {
    $request->validate([
      'location'  =>  'required',
    ]);

    $pjp->update($request->all());

    return response()->json([
      'data'  =>  $pjp
    ], 200);
  }
}