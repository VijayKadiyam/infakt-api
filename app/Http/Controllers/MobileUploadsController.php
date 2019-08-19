<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Storage;

class MobileUploadsController extends Controller
{
  public function __construct()
  {
    $this->middleware(['auth:api', 'company']);
  }

  public function mobilePhotoImage(Request $request)
  {
    $image = $request->image;
    $name = $request->name;

    $realImage = base64_decode($image);
    $path = 'users/' . request()->user()->id . '/' . $name;

    Storage::disk('s3')->put('documentation/' . $path, $realImage, 'public');

    $user = User::where('id', '=', request()->user()->id)->first();
    $user->photo_path = $path;
    $user->update();

    return response()->json([
      'data'  => [
        'image_path'  =>  $path
      ],
      'success' =>  true
    ]);
  }

  public function mobileResidentialProofImage(Request $request)
  {
    $image = $request->image;
    $name = $request->name;

    $realImage = base64_decode($image);
    $path = 'users/' . request()->user()->id . '/' . $name;

    Storage::disk('s3')->put('documentation/' . $path, $realImage, 'public');

    $user = User::where('id', '=', request()->user()->id)->first();
    $user->residential_proof_path = $path;
    $user->update();

    return response()->json([
      'data'  => [
        'image_path'  =>  $path
      ],
      'success' =>  true
    ]);
  }
}
