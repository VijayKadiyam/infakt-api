<?php

namespace App\Http\Controllers;

use App\TicketFollowup;
use Illuminate\Http\Request;

class TicketFollowupsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:api', 'company']);
    }

    /*
       * To get all ticket_followup
         *
       *@
       */
    public function index()
    {
        $count = 0;
        if (request()->page && request()->rowsPerPage) {
            $ticket_followups = request()->company->ticket_followups();
            $count = $ticket_followups->count();
            $ticket_followups = $ticket_followups->paginate(request()->rowsPerPage)->toArray();
            $ticket_followups = $ticket_followups['data'];
        } else {
            $ticket_followups = request()->company->ticket_followups;
            $count = $ticket_followups->count();
        }

        return response()->json([
            'data'     =>   $ticket_followups,
            'count'    =>   $count,
            'success'  =>   true
        ], 200);
    }

    /*
       * To store a new ticket_followup
       *
       *@
       */
    public function store(Request $request)
    {
        $request->validate([
            'description'    =>  'required'
        ]);

        $ticket_followup = new TicketFollowup($request->all());
        $request->company->ticket_followups()->save($ticket_followup);

        return response()->json([
            'data'    =>  $ticket_followup
        ], 201);
    }

    /*
       * To view a single ticket_followup
       *
       *@
       */
    public function show(TicketFollowup $ticket_followup)
    {
        return response()->json([
            'data'   =>  $ticket_followup
        ], 200);
    }

    /*
       * To update a ticket_followup
       *
       *@
       */
    public function update(Request $request, TicketFollowup $ticket_followup)
    {
        $request->validate([
            'description'  =>  'required',
        ]);

        $ticket_followup->update($request->all());

        return response()->json([
            'data'  =>  $ticket_followup
        ], 200);
    }
}