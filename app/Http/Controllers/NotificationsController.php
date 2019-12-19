<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Notification;

class NotificationsController extends Controller
{
  public function __construct()
  {
    $this->middleware(['auth:api', 'company']);
  }

  public function index(Request $request, User $user)
  {
    $notifications = $user->notifications;

    return response()->json([
      'data'     =>  $notifications,
      'success'   =>  true
    ], 200);
  }

  public function store(Request $request, User $user)
  {
    $request->validate([
      'notification'  =>  'required',
    ]);

    $notification = new Notification($request->all());
    $user->notifications()->save($notification);

    return response()->json([
      'data'    =>  $notification
    ], 201); 
  }

  public function show(User $user, Notification $notification)
  {
    return response()->json([
      'data'   =>  $notification
    ], 200);   
  }

  public function update(Request $request, User $user, Notification $notification)
  {
    $request->validate([
      'notification'        =>  'required',
    ]);

    $notification->update($request->all());
      
    return response()->json([
      'data'  =>  $notification
    ], 200);
  }
}
