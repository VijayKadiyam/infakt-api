<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Version;

class VersionsController extends Controller
{
  public function index(Request $request)
  {
    $version = [];
    if($request->search == 'latest') {
      $version = Version::latest()->first();
    }
    return response()->json([
      'data'  =>  $version
    ]);
  }

  public function store(Request $request)
  {
    $request->validate([
      'version'  =>  'required'
    ]);

    $version = new Version(request()->all());
    $version->save();

    return response()->json([
      'data'  =>  $version
    ], 201); 
  }
}
