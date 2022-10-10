<?php

namespace App\Http\Controllers;

use App\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationsController extends Controller

{
    public function __construct()
    {
        $this->middleware(['auth:api', 'company']);
    }

    public function index(Request $request)
    {
        $count = 0;
        $user_id = Auth::user()->id;
        $notifications = $request->company->notifications();
        if ($user_id) {
            $notifications = $notifications->where('user_id', $user_id);
        }
        $notifications = $notifications->get();
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

    public function mark_all_read()
    {
        $user_id = request()->user_id;
        $notifications = Notification::where('user_id', $user_id)->update(['is_read' => true]);
        return response()->json([
            'data' => $notifications,
            'message' =>  'Marked All Notifications as read'
        ], 200);
    }
    public function clear_all()
    {
        $user_id = request()->user_id;
        $notifications = Notification::where('user_id', $user_id)->update(['is_deleted' => true]);

        return response()->json([
            'message' =>  'Cleared All Messages'
        ], 200);
    }
    public function clear()
    {
        $id = request()->notification_id;
        $notifications = Notification::find($id)->update(['is_deleted' => true]);
        return response()->json([
            'message' =>  'Cleared All Messages'
        ], 200);
    }
}
