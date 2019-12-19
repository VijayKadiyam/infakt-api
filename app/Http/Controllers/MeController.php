<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MeController extends Controller
{
  public function __construct()
  {
    $this->middleware(['auth:api', 'company']);
  }

  /*
   * Get the logged in user
   *
   *@
   */
  public function me(Request $request)
  {
    $user = $request->user();
    $user->roles = $user->roles;
    $user->companies = $user->companies;
    $user->notifications = $user->notifications;

    return response()->json([
      'data'    =>  $user->toArray(),
      'success' =>  true
    ], 200);
  }
}
