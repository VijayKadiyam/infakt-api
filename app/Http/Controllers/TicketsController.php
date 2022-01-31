<?php

namespace App\Http\Controllers;

use App\Ticket;
use Illuminate\Http\Request;

class TicketsController extends Controller
{
  public function __construct()
  {
    $this->middleware(['auth:api', 'company']);
  }

  /*
     * To get all ticket
       *
     *@
     */

  public function masters(Request $request)
  {
    $request->request->add(['role_id' => '4']);
    $usersController = new UsersController();
    $usersResponse = $usersController->index($request);
    return response()->json([
      'AssignedTo'   =>  $usersResponse->getData()->data,
    ], 200);
  }

  public function index()
  {
    $count = 0;
    if (request()->page && request()->rowsPerPage) {
      $tickets = request()->company->tickets();
      $count = $tickets->count();
      $tickets = $tickets->paginate(request()->rowsPerPage)->toArray();
      $tickets = $tickets['data'];
    } else {
      $tickets = request()->company->tickets;
      $count = $tickets->count();
    }

    return response()->json([
      'data'     =>   $tickets,
      'count'    =>   $count,
      'success'  =>   true
    ], 200);
  }

  /*
     * To store a new ticket
     *
     *@
     */
  public function store(Request $request)
  {
    $request->validate([
      'title'    =>  'required'
    ]);

    $ticket = new Ticket($request->all());
    $request->company->tickets()->save($ticket);

    return response()->json([
      'data'    =>  $ticket
    ], 201);
  }

  /*
     * To view a single ticket
     *
     *@
     */
  public function show(Ticket $ticket)
  {
    return response()->json([
      'data'   =>  $ticket
    ], 200);
  }

  /*
     * To update a ticket
     *
     *@
     */
  public function update(Request $request, Ticket $ticket)
  {
    $request->validate([
      'title'  =>  'required',
    ]);

    $ticket->update($request->all());

    return response()->json([
      'data'  =>  $ticket
    ], 200);
  }
}
