<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\User;

class UserUploadsController extends Controller
{
  public function profileImage(Request $request)
  {
    $request->validate([
      'user_id'        => 'required'
    ]);

    if ($request->hasFile('image')) {
      $file = $request->file('image');
      $name = 'profile.';
      $name = $name . $file->getClientOriginalExtension();;
      $imagePath = 'users/' . $request->user_id . '/' . $name;
      Storage::disk('s3')->put('documentation/' . $imagePath, file_get_contents($file), 'public');

      $user = User::where('id', '=', request()->user_id)->first();
      $user->image_path = $imagePath;
      $user->update();

      return response()->json([
        'data'  => [
          'image_path'  =>  $imagePath
        ],
        'success' =>  true
      ]);
    }
  }
}
