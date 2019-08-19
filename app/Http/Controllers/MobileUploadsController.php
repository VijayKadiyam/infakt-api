<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MobileUploadsController extends Controller
{
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
}
