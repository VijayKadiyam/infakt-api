<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Version;

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

    $version = Version::latest()->first();

    return response()->json([
      'data'    =>  $user->toArray(),
      'version' =>  $version,
      'success' =>  true
    ], 200);
  }
}
