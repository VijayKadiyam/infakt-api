<?php

namespace App\Http\Controllers;

use App\Pjp;
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
        ->get();
      foreach($pjpSupervisors as $pjpSupervisor) {
        $pjp = request()->company->pjps()
          ->where('id', '=', $pjpSupervisor->actual_pjp_id)
          ->first();
        foreach($pjp->pjp_markets  as $pjpMarket) {
          if($pjpMarket->id == $pjpSupervisor->visited_pjp_market_id) {
            $pjpMarket['pjp_supervisor'] = $pjpSupervisor;
          } else {
            // $pjpMarket['pjp_supervisor'] = [];
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
