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
      if (request()->page && request()->rowsPerPage) {
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
        'count'    =>   $count
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
