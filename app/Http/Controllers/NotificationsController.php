<?php

namespace App\Http\Controllers;

use App\Notification;
use Illuminate\Http\Request;

class NotificationsController extends Controller

{
    public function __construct()
    {
        $this->middleware(['auth:api','company']);
    }

    public function index(Request $request)
    {
        $count = 0;
        $notifications = $request->company->notifications;
        $count = $notifications->count();

        return response()->json([
            'data'     =>  $notifications,
            'count'    =>   $count
        ], 200);
    }

    /*
     * To store a new user_section
     *
     *@
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id'        =>  'required',
        ]);

        $notifications = new Notification(request()->all());
        $request->company->notifications()->save($notifications);

        return response()->json([
            'data'    =>  $notifications
        ], 201);
    }

    /*
     * To view a single user_section
     *
     *@
     */
    public function show(Notification $notification)
    {
        return response()->json([
            'data'   =>  $notification,
            'success' =>  true
        ], 200);
    }

    /*
     * To update a user_section
     *
     *@
     */
    public function update(Request $request, Notification $notification)
    {
        $notification->update($request->all());

        return response()->json([
            'data'  =>  $notification
        ], 200);
    }

    public function destroy($id)
    {
        $notifications = Notification::find($id);
        $notifications->delete();

        return response()->json([
            'message' =>  'Deleted'
        ], 204);
    }
}
